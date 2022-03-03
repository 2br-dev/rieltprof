<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Notice;

use Alerts\Model\Types\AbstractNotice;
use Alerts\Model\Types\InterfaceDesktopApp;
use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\InterfaceSms;
use Alerts\Model\Types\NoticeDataDesktopApp;
use Alerts\Model\Types\NoticeDataEmail;
use Alerts\Model\Types\NoticeDataSms;
use Crm\Model\Orm\Task;
use RS\Helper\Tools;
use RS\Config\Loader;

/**
 * Уведомление о создании новой задачи для исполнителя
 */
class TaskSoonExpireToImplementer extends AbstractNotice
    implements InterfaceEmail, InterfaceDesktopApp, InterfaceSms
{

    /**
     * @var Task
     */
    public $task;
    /**
     * @var string
     */
    public $remaining_time_str;
    public $user_fields_manager;

    /**
     * Возвращает краткое описание уведомления
     * @return string
     */
    public function getDescription()
    {
        return t('Уведомление о скором истечении срока выполнения задачи (исполнителю)');
    }


    function init(Task $task)
    {
        $this->task = $task;
        $this->remaining_time_str = $this->getExpireTimeStr();
        $this->user_fields_manager = Loader::byModule($this)->getTaskUserFieldsManager();
        $this->user_fields_manager->setValues($task['custom_fields']);
    }


    /**
     * Возвращает путь к шаблону уведомления для Desktop приложения
     *
     * @return string
     */
    public function getTemplateDesktopApp()
    {}

    /**
     * Возвращает данные, которые необходимо передать при инициализации уведомления
     *
     * @return NoticeDataDesktopApp
     */
    public function getNoticeDataDesktopApp()
    {
        $notice_data = new NoticeDataDesktopApp();
        $notice_data->title = t('Срок выполнения задачи №%num (%title) скоро подходит к концу', [
            'num' => $this->task->task_num,
            'title' => $this->task->title
        ]);

        if ($this->task['implementer_user_id']) {
            $notice_data->destination_user_id = $this->task['implementer_user_id'];
        }

        $notice_data->short_message = t('Осталось %time', [
            'title' => $this->task['title'],
            'time' => $this->remaining_time_str
        ]);
        $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->task['id']], 'crm-taskctrl', true);
        $notice_data->link_title = t('Перейти к задаче');
        $notice_data->vars = $this;

        return $notice_data;
    }

    /**
     * Возвращает строку с количеством оставшегося времени
     *
     * @return string
     */
    private function getExpireTimeStr()
    {
        $date_of_planned_end_time = strtotime($this->task->date_of_planned_end);
        $delta = $date_of_planned_end_time - time();

        if ($delta < 0) $delta = 0;

        $days = floor($delta / 86400);
        $hours = floor(($delta - ($days * 86400)) / 3600);
        $minutes = floor(($delta - ($days * 86400) - ($hours * 3600)) / 60);

        $result = [];

        if ($days > 0) {
            $result[] = $days.' '.Tools::verb($days, t('день'), t('дня'), t('дней'));
        }

        if ($hours > 0) {
            $result[] = $hours.' '.Tools::verb($hours, t('час'), t('часа'), t('часов'));
        }

        if ($minutes > 0) {
            $result[] = $minutes.' '.Tools::verb($minutes, t('минуту'), t('минуты'), t('минут'));
        }

        return implode($result,' ');
    }

    /**
     * Возвращает путь к шаблону письма
     * @return string
     */
    public function getTemplateEmail()
    {
        return '%crm%/notice/task_soon_expire_to_implementer_email.tpl';
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return \Alerts\Model\Types\NoticeDataEmail
     */
    public function getNoticeDataEmail()
    {
        $notice_data = new NoticeDataEmail();

        $notice_data->email     = $this->task->getImplementerUser()->e_mail;
        $notice_data->subject   = t('Срок выполнения задачи №%num (%title) скоро подходит к концу', [
            'num' => $this->task->task_num,
            'title' => $this->task->title
        ]);
        $notice_data->vars      = $this;

        return $notice_data;
    }

    /**
     * Возвращает путь к шаблону SMS-сообщения
     * @return string
     */
    public function getTemplateSms()
    {
        return '%crm%/notice/task_soon_expire_to_implementer_sms.tpl';
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return \Alerts\Model\Types\NoticeDataSms
     */
    public function getNoticeDataSms()
    {
        $notice_data = new NoticeDataSms();

        $notice_data->vars = $this;
        $notice_data->phone = $this->task->getImplementerUser()->phone;

        return $notice_data;
    }
}