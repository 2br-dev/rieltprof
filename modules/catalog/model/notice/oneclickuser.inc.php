<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Notice;

use Alerts\Model\Types\AbstractNotice;
use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\InterfaceSms;
use Alerts\Model\Types\NoticeDataEmail;
use Alerts\Model\Types\NoticeDataSms;
use Catalog\Model\Orm\OneClickItem;
use RS\Application\Auth;
use RS\Http\Request as HttpRequest;
use Users\Model\Orm\User;

/**
 * Уведомление - купить в один клик пользователю
 */
class OneClickUser extends AbstractNotice implements InterfaceEmail, InterfaceSms
{
    /** @var OneClickItem */
    public $oneclick;
    /** @var User */
    public $user;

    public function getDescription()
    {
        return t('Купить в один клик (пользователю)');
    }

    /**
     * Инициализация уведомления
     *
     * @param array $oneclick - массив с параметрами для передачи
     * @return void
     */
    public function init($oneclick)
    {
        $this->oneclick = $oneclick;
        $this->user = Auth::getCurrentUser();
    }

    /**
     * Возвращает путь к шаблону письма
     *
     * @return string
     */
    public function getTemplateEmail()
    {
        return '%catalog%/notice/touser_oneclick.tpl';
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return NoticeDataEmail|void
     */
    public function getNoticeDataEmail()
    {
        if (!$this->user['e_mail']) {
            return;
        }

        $notice_data = new NoticeDataEmail();
        $notice_data->email = $this->user['e_mail'];
        $notice_data->subject = t('Купить в один клик на сайте %0', [HttpRequest::commonInstance()->getDomainStr()]);
        $notice_data->vars = $this;

        return $notice_data;
    }

    public function getNoticeDataSms()
    {
        $notice_data = new NoticeDataSms();

        $notice_data->phone = $this->oneclick['phone'];
        $notice_data->vars = $this;

        return $notice_data;
    }

    function getTemplateSms()
    {
        return '%catalog%/notice/touser_oneclick_sms.tpl';
    }
}

