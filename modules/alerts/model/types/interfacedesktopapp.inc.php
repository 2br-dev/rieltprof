<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\Types;

/**
*  Интерфейс уведомления, которое может быть отправлено на Desktop приложение
*/
interface InterfaceDesktopApp
{
    /**
    * Возвращает путь к шаблону уведомления для Desktop приложения
    * 
    * @return string | null Если возвращен null, то Desktop Приложение просто покажет уведомление без возможности подробного просмотра
    */
    public function getTemplateDesktopApp();
    
    /**
    * Возвращает данные, которые необходимо передать при инициализации уведомления
    * 
    * @return NoticeDataDesktopApp
    */
    public function getNoticeDataDesktopApp();
}
