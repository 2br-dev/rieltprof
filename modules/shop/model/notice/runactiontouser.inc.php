<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;

use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\InterfaceSms;
use Alerts\Model\Types\NoticeDataEmail;
use Alerts\Model\Types\NoticeDataSms;
use Shop\Model\Orm\ActionTemplate;
use Shop\Model\Orm\Order;

/**
 * Шаблон отправки уведомления, при выполнения действия по шаблону курьером
 */
class RunActionToUser extends \Alerts\Model\Types\AbstractNotice
    implements InterfaceSms, InterfaceEmail
{
    public
        $order,
        $action_template;

    /**
     * Инициализирует уведомление
     *
     * @param Order $order
     * @param ActionTemplate $action_template
     */
    public function init(Order $order, ActionTemplate $action_template)
    {
        $this->order = $order;
        $this->action_template = $action_template;
    }

    /**
     * Возвращает краткое описание уведомления
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Шаблонное уведомление от курьера (пользователю)');
    }

    /**
     * Возвращает путь к шаблону письма
     *
     * @return string
     */
    public function getTemplateEmail()
    {
        return '%shop%/notice/touser_runaction_email.tpl';
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return \Alerts\Model\Types\NoticeDataEmail
     */
    public function getNoticeDataEmail()
    {
        $email = $this->order->getUser()->e_mail;
        if ($this->action_template->client_email_message && $email) {

            $notice_data_email = new NoticeDataEmail();

            $notice_data_email->subject = t('Сообщение от курьера по заказу №%num от %date', [
                'num' => $this->order['order_num'],
                'date' => date('d.m.Y', strtotime($this->order['dateof']))
            ]);
            $notice_data_email->email = $email;
            $notice_data_email->vars = $this;

            return $notice_data_email;

        }
    }

    /**
     * Возвращает путь к шаблону SMS-сообщения
     *
     * @return string
     */
    public function getTemplateSms()
    {
        return '%shop%/notice/touser_runaction_sms.tpl';
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return \Alerts\Model\Types\NoticeDataSms
     */
    public function getNoticeDataSms()
    {
        if ($this->action_template->client_sms_message) {
            $notice_data_sms = new NoticeDataSms();
            $notice_data_sms->vars = $this;
            $notice_data_sms->phone = $this->order->getUser()->phone;

            return $notice_data_sms;
        }
    }
}