<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\App;

/**
* Интерфейс подсказывает, что Приложение может отправлять Push уведомления
*/
interface InterfaceHasPush
{
    /**
    * Возвращает массив объектов Push уведомленийб которые 
    * может отправлять приложение
    * 
    * @return \PushSender\Model\AbstractPushNotice[]
    */
    public function getPushNotices();
}
