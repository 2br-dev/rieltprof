<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Alerts\Model\Types;

/**
 * Интерфейс уведомления, которое может быть отправлено по SMS
 */
interface InterfaceSms
{
    /**
     * Возвращает путь к шаблону SMS-сообщения
     *
     * @return string
     */
    public function getTemplateSms();

    /**
     * Возвращает объект NoticeData
     *
     * @return NoticeDataSms|void
     */
    public function getNoticeDataSms();
}
