<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Alerts\Model\SMS;

use Alerts\Model\Api as AlertsApi;
use Alerts\Model\Log\LogAlerts;
use RS\Config\Loader as ConfigLoader;
use RS\Exception;
use RS\View\Engine;

class Manager
{
    /**
     * Отправляет SMS сообщение
     *
     * @param array | string $phone_numbers - массив телефонов, или строка телефонов, разделенных запятой
     * @param string $tpl - путь к шаблону сообщения
     * @param mixed $data - параметры, передаваемые в шаблон
     * @param bool $suppress_exception - если true, по подавляет исключения
     * @return bool
     *
     * @throws Exception
     * @throws \SmartyException
     */
    public static function send($phone_numbers, $tpl, $data, $suppress_exception = true)
    {
        if(!is_array($phone_numbers)){
            $phone_numbers = explode(',', (string)$phone_numbers);
        }
        
        $phone_numbers = array_map('trim', $phone_numbers);
        
        $config = ConfigLoader::byModule('alerts');
        
        $view = new Engine();
        $view->assign('data', $data);
        $content = $view->fetch($tpl);
        
        $sender_class = $config['sms_sender_class'];
        $api = new AlertsApi();
        
        if ($sender_class){
            /** @var AbstractSender */
            if ($sender = $api->getSenderByShortName($sender_class)) {
                $log = LogAlerts::getInstance();

                $log_text = t('Попытка отправки SMS').'; '.t('получатели').' - '.implode(', ', $phone_numbers).'; '.t('содержимое').' - "'.$content.'"';
                $log->write($log_text, LogAlerts::LEVEL_SMS);

                try {
                    $sender->send($content, $phone_numbers);
                    $log->write(t('Отправка прошла успешно'), LogAlerts::LEVEL_SMS);
                    return true;

                } catch (\Exception $e) {
                    $log->write(t('Ошибка').' - ' . $e->getMessage(), LogAlerts::LEVEL_SMS);
                    
                    if (!$suppress_exception) {
                        throw new Exception($e->getMessage(), $e->getCode());
                    }
                }
            }
        }

        return false;
    }
}
