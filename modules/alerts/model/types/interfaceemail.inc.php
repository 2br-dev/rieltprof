<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Alerts\Model\Types;

/**
 * Интерфейс уведомления, которое может быть отправлено на Email
 */
interface InterfaceEmail
{
    /**
     * Возвращает путь к шаблону письма
     *
     * @return string
     */
    public function getTemplateEmail();

    /**
     * Возвращает объект NoticeData
     *
     * @return NoticeDataEmail|void
     */
    public function getNoticeDataEmail();
}
