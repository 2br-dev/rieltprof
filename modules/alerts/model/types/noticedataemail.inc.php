<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Alerts\Model\Types;

/**
 * Объект с обязательными параметрами для уведомления
 */
class NoticeDataEmail
{
    /** @var string - Тема письма */
    public $subject;
    /** @var string - Адрес получателя */
    public $email;
    /** @var mixed - Данные, передаваемы в шаблон */
    public $vars;
}
