<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Controller\Admin;

use Designer\Model\AtomApis\VideoApi;
use RS\Controller\Admin\Front;

/**
 * Контроллер, позволяющий работать с компонентом видео
 */
class AtomVideoCtrl extends Front
{
    /**
     *  Возвращает данные, если видео существует
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     */
    function actionVideoExists()
    {
        $type       = $this->request('type', TYPE_STRING, '');
        $video_url  = $this->request('url', TYPE_STRING, '');

        if (empty($type)){
            return $this->result->setSuccess(false)->addEMessage(t('Не предан параметр type с типом видео'));
        }
        if (empty($video_url)){
            return $this->result->setSuccess(false)->addEMessage(t('Не предан параметр url со ссылкой на видео'));
        }

        if ($type == 'youtube'){
            if (mb_stripos($video_url, "?") === false){
                return $this->result->setSuccess(false)->addEMessage(t('Адрес задан в неправильном формате.'));
            }
            $query = parse_url($video_url, PHP_URL_QUERY);
            if (empty($query)){
                return $this->result->setSuccess(false)->addEMessage(t('Адрес задан в неправильном формате.'));
            }
            parse_str($query, $params);
            if (!isset($params['v'])){
                return $this->result->setSuccess(false)->addEMessage(t('Адрес задан в неправильном формате.'));
            }

            $ratio = VideoApi::getYoutubeVideoAspectRatio($params['v']);

            $this->result->addSection('image', 'https://img.youtube.com/vi/'.$params['v'].'/maxresdefault.jpg')
                         ->addSection('video', 'https://www.youtube.com/embed/'.$params['v'])
                         ->addSection('ratio', $ratio);
        }

        if (!VideoApi::checkRemoteUrlExists($video_url)) {
            return $this->result->setSuccess(false)->addEMessage(t('Видео не найдено.'));
        }
        return $this->result->setSuccess(true);
    }
}