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
use RS\Config\Loader;

/**
 * Уведомление о создании новой задачи для исполнителя
 */
class NewTaskToImplementer extends AbstractNotice
    implements InterfaceEmail, InterfaceDesktopApp, InterfaceSms
{

    /**
     * @var Task
     */
    public $task;
    public $user_fields_manager;

    /**
     * Возвращает краткое описание уведомления
     * @return string
     */
    public function getDescription()
    {
        return t('Создание новой задачи (исполнителю)');
    }


    function init(Task $task)
    {
        $this->task = $task;
        $this->user_fields_manager = Loader::byModule($this)->getTaskUserFieldsManager();
        $this->user_fields_manager->setValues($task['custom_fields']);
    }


    /**
     * Возвращает путь к шаблону уведомления для Desktop приложения
     *
     * @return string
     */
    public function getTemplateDesktopApp()
    {
        return '%crm%/notice/new_task_to_implementer_desktop.tpl';
    }

    /**
     * Возвращает данные, которые необходимо передать при инициализации уведомления
     *
     * @return NoticeDataDesktopApp
     */
    public function getNoticeDataDesktopApp()
    {
        $notice_data = new NoticeDataDesktopApp();
        $notice_data->title = t('Новая задача №%0', [$this->task->task_num]);

        if ($this->task['implementer_user_id']) {
            $notice_data->destination_user_id = $this->task['implementer_user_id'];
        }

        $notice_data->short_message = t('%title %creator', [
            'title' => $this->task['title'],
            'creator' => ($this->task['creator_user_id'] ? t('от %0', [$this->task->getCreatorUser()->getFio()]) : '')
        ]);
        $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->task['id']], 'crm-taskctrl', true);
        $notice_data->link_title = t('Перейти к задаче');
        $notice_data->vars = $this;

        return $notice_data;
    }

    /**
     * Возвращает путь к шаблону письма
     * @return string
     */
    public function getTemplateEmail()
    {
        return '%crm%/notice/new_task_to_implementer_email.tpl';
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
        $notice_data->subject   = t('Новая задача №%0', [$this->task['task_num']]);
        $notice_data->vars      = $this;

        return $notice_data;
    }

    /**
     * Возвращает путь к шаблону SMS-сообщения
     * @return string
     */
    public function getTemplateSms()
    {
        return '%crm%/notice/new_task_to_implementer_sms.tpl';
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