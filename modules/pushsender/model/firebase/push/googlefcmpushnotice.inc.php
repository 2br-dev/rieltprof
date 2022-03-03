<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\Firebase\Push;

/**
* Абстрактный класс уведомлений, отправляемых через провайдер Google Firebase Cloud Messaging
*/
abstract class GoogleFCMPushNotice extends \PushSender\Model\AbstractPushNotice
{
    private 
        $provider;
    /**
    * Возвращает провайдера, через которого нужно отправить данное уведомление
    * 
    * @return \PushSender\Model\AbstractProvider
    */
    public function getProvider()
    {
        if ($this->provider === null) {
            $this->provider = new \PushSender\Model\Firebase\Provider\GoogleFCM();
        }
        return $this->provider;
    }
}