<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Notice;

use Alerts\Model\Types\AbstractNotice;
use Alerts\Model\Types\InterfaceDesktopApp;
use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\InterfaceSms;
use Alerts\Model\Types\NoticeDataDesktopApp;
use Alerts\Model\Types\NoticeDataEmail;
use Alerts\Model\Types\NoticeDataSms;
use RS\Config\Loader as ConfigLoader;
use RS\Http\Request as HttpRequest;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Reservation as OrmReservation;

/**
 * Уведомление - оформлен предварительный заказ
 */
class Reservation extends AbstractNotice implements InterfaceEmail, InterfaceSms, InterfaceDesktopApp
{
    /** @var OrmReservation */
    public $reserve;

    public function getDescription()
    {
        return t('Предварительный заказ (администратору)');
    }

    function init(OrmReservation $reserve)
    {
        $this->reserve = $reserve;
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return NoticeDataEmail|void
     */
    function getNoticeDataEmail()
    {
        $site_config = ConfigLoader::getSiteConfig(SiteManager::getSiteId());
        $notice_data = new NoticeDataEmail();
        $notice_email = $site_config['admin_email'];
        $user_manager = $this->reserve->getUser()->getManager(); // метод добавлен через Behavior
        if (!empty($user_manager['e_mail'])) {
            $notice_email .= ',' . $user_manager['e_mail'];
        }

        if (!$notice_email) {
            return;
        }

        $notice_data->email = $notice_email;
        $notice_data->subject = t('Предварительный заказ на сайте %0', [HttpRequest::commonInstance()->getDomainStr()]);
        $notice_data->vars = $this;

        return $notice_data;
    }

    function getTemplateEmail()
    {
        return '%shop%/notice/toadmin_reservation.tpl';
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return NoticeDataSms|void
     */
    function getNoticeDataSms()
    {
        $site_config = ConfigLoader::getSiteConfig();

        $notice_data = new NoticeDataSms();
        $notice_phone = $site_config['admin_phone'];
        $user_manager = $this->reserve->getUser()->getManager(); // метод добавлен через Behavior
        if (!empty($user_manager['phone'])) {
            $notice_phone .= ',' . $user_manager['phone'];
        }

        if (!$notice_phone) {
            return;
        }

        $notice_data->phone = $notice_phone;
        $notice_data->vars = $this;

        return $notice_data;
    }

    function getTemplateSms()
    {
        return '%shop%/notice/toadmin_reservation_sms.tpl';
    }

    /**
     * Возвращает путь к шаблону уведомления для Desktop приложения
     *
     * @return string
     */
    public function getTemplateDesktopApp()
    {
        return '%shop%/notice/desktop_reservation.tpl';
    }

    /**
     * Возвращает данные, которые необходимо передать при инициализации уведомления
     *
     * @return NoticeDataDesktopApp
     */
    public function getNoticeDataDesktopApp()
    {
        $desktop_data = new NoticeDataDesktopApp();
        $desktop_data->title = t('Предварительный заказ №%0', [$this->reserve['id']]);
        $desktop_data->short_message = t('%product %offer (Кол-во: %amount)', [
            'product' => $this->reserve['product_title'],
            'offer' => $this->reserve['offer'],
            'amount' => $this->reserve['amount'],
        ]);

        $desktop_data->link = RouterManager::obj()->getAdminUrl('edit', ['id' => $this->reserve['id']], 'shop-reservationctrl', true);
        $desktop_data->link_title = t('Перейти к заказу');

        $desktop_data->vars = $this;

        return $desktop_data;
    }
}
