<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model;

use \RS\Helper\Mailer;
use \RS\Event\Manager as EventManager;
use RS\Language\Core as LanguageCore;

/**
* Менеджер уведомлений
*/
class Manager
{
    public static $timeout_image_request = 5;
    /**
    * Отправляет уведомление по классу событий
    * 
    * @param $notice object событие
    * @throws
    */
    public static function send(\Alerts\Model\Types\AbstractNotice $notice)
    {
        //Отключаем генерацию изображений в webp для уведомлений
        \RS\Img\Core::switchFormat(\RS\Img\Core::FORMAT_WEBP, false);

        //Сменить язык для уведомлений, если язык админ. части отличается от языка сайта
        $temporary_language = $notice->getLanguage();
        $language_has_changed = false;
        if ( $temporary_language ) {
            $actual_language = LanguageCore::getCurrentLang();
            if ( $temporary_language != $actual_language ) {
                $language_has_changed = LanguageCore::setCurrentLang($temporary_language);
            }
        }

        $notice_config = \Alerts\Model\Api::getNoticeConfig(get_class($notice));

        $event = EventManager::fire('alerts.beforenoticesend', [
            'notice_config' => $notice_config,
            'notice' => $notice
        ])->getEvent();

        if ($event->isStopped()) return;
        
        // Отправка E-Mail уведомления
        if($notice instanceof \Alerts\Model\Types\InterfaceEmail && $notice_config['enable_email']){
            $notice_data = $notice->getNoticeDataEmail();
            if ($notice_config->additional_recipients && $notice_data ) {
                $notice_data->email .= ','.$notice_config->additional_recipients;
            }
            if($notice_data){
                $mailer = new Mailer();
                $mailer->Subject = $notice_data->subject;
                $mailer->addEmails($notice_data->email);
                $mailer->renderBody($notice_config->template_email ? $notice_config->template_email : $notice->getTemplateEmail(), $notice_data->vars);
                $mailer->Body = Manager::prepareBodyImages($mailer);
                $mailer->setEventParams('alerts', ['notice' => $notice]);
                $mailer->send();
            }
        }

        // Отправка SMS уведомления
        if($notice instanceof \Alerts\Model\Types\InterfaceSms && $notice_config['enable_sms']){
            $notice_data = $notice->getNoticeDataSms();
            if($notice_data){
                \Alerts\Model\SMS\Manager::send(
                    $notice_data->phone, 
                    $notice->getTemplateSms(), 
                    $notice_data->vars
                );
            }
        }
        
        //Отправка уведомления в Desktop приложение ReadyScript
        if ($notice instanceof \Alerts\Model\Types\InterfaceDesktopApp && $notice_config['enable_desktop']) {

            $template = $notice_config->template_desktop ? $notice_config->template_desktop : $notice->getTemplateDesktopApp();
            
            NoticeItemsApi::cleanOldNoticeItems();
            NoticeItemsApi::addNoticeItem($notice, $template);
        }

        //Восстановить язык
        if ( $language_has_changed ) {
            LanguageCore::setCurrentLang($actual_language);
        }
    }

    /**
     * Подготавливает тело письма к отправке. Делает изображения встроенными в письмо.
     *
     * @param \RS\Helper\Mailer $mailer - объект письма
     * @param string $body - тело письма, если не укзано - извлекается из объекта письма
     * @return string
     */
    public static function prepareBodyImages(Mailer $mailer, $body = null)
    {
        if ($body === null) {
            $body = $mailer->Body;
        }

        $replace_function = function($matches) use ($mailer) {

            $src = trim($matches[2],"'\"");
            $cid = md5($src);
            if (preg_match('/^data:/', $src)) {
                return $matches[0];
            }

            if (strpos($src, '://') === false) {
                $root = \RS\Site\Manager::getSite()->getRootUrl(true, false);
                //Если путь относительный, значит фото локальное
                $filename =  $root.ltrim($src,'/');
            } else {
                $filename = $src;
            }

            //Все фото загружаем через запрос, чтобы они генерировались в случае их отсутствия
            $context = [
                'http'=> [
                    'method'=>"GET",
                    'timeout' => \Alerts\Model\Manager::$timeout_image_request
                ],
                "ssl"=> [
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ]
            ];
            $image_data = @file_get_contents($filename, null, stream_context_create($context));
            if ($image_data) {
                $mailer->addStringEmbeddedImage($image_data, $cid, basename($src));
            }

            return $matches[1]."cid:$cid".$matches[3];
        };

        $body = preg_replace_callback('/(<img[^>]*src=["\'])(.*?)(["\'][^>]*>)/i', $replace_function, $body);
        $body = preg_replace_callback('/(style=["\'][^>]*url\()(.*?)(\))/i', $replace_function, $body);
        $body = preg_replace_callback('/(background=["\'])(.*?)(["\'])/i', $replace_function, $body);


        return $body;
    }
}
