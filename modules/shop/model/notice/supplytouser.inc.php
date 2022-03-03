<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Notice;

use Alerts\Model\Types\AbstractNotice;
use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\InterfaceSms;
use Alerts\Model\Types\NoticeDataEmail;
use Alerts\Model\Types\NoticeDataSms;
use RS\Http\Request as HttpRequest;
use Shop\Model\Orm\Reservation;

/**
 * Уведомление - Заказанный товар поступил на склад
 */
class SupplyToUser extends AbstractNotice implements InterfaceEmail, InterfaceSms
{
    /** @var Reservation[] */
    public $reservations;
    /** @var Reservation */
    public $reserve; // @deprecated (20.06) для совместимости со старыми шаблонами уведомлений

    public function getDescription()
    {
        return t('Уведомление о поступлении заказа на склад.');
    }

    function init($reservations)
    {
        $this->reservations = $reservations;
        $this->reserve = reset($this->reservations);
    }

    function getNoticeDataEmail()
    {
        $notice_data = new NoticeDataEmail();

        if (!reset($this->reservations)['email']) {
            return;
        }

        $notice_data->email = reset($this->reservations)['email'];
        $notice_data->subject = t('Поступление товара на склад, заказ на сайте %0', [HttpRequest::commonInstance()->getDomainStr()]);
        $notice_data->vars = $this;
        return $notice_data;
    }

    function getTemplateEmail()
    {
        return '%shop%/notice/touser_reservation.tpl';
    }

    function getNoticeDataSms()
    {
        $notice_data = new NoticeDataSms();

        $phone = reset($this->reservations)['phone'];
        if (!$phone) {
            return;
        }

        $notice_data->phone = $phone;
        $notice_data->vars = $this;
        return $notice_data;
    }

    function getTemplateSms()
    {
        return '%shop%/notice/touser_reservation_sms.tpl';
    }
}

