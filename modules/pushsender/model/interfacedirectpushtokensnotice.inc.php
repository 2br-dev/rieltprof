<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
    namespace PushSender\Model;
      
    /**
    * Интерфейс PUSH уведомления, которое должно быть отправлено на прямую по известным PUSH токенам
    */
    interface InterfaceDirectPushTokensNotice
    {
        /**
        * Возвращает массив PUSH токенов устройств, которым нужно отправить уведомление
        * @return array
        */
        public function getRecipientPushTokens();
    }