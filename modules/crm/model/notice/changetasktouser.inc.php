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
class ChangeTaskToUser extends AbstractNotice
    implements InterfaceEmail, InterfaceDesktopApp, InterfaceSms
{

    /**
     * @var Task
     */
    public $task;
    public $user_fields_manager;
    public $changer_user_id;
    public $changed_values;
    public $recipients_data;

    /**
     * Возвращает краткое описание уведомления
     * @return string
     */
    public function getDescription()
    {
        return t('Задача изменена (создателю или исполнителю)');
    }


    function init(Task $task, $changes_values, $changer_user_id)
    {
        $this->changer_user_id = $changer_user_id;
        $this->changed_values = $changes_values;
        $this->task = $task;
        $this->recipients_data = $this->getRecipientUsersData();
        $this->user_fields_manager = Loader::byModule($this)->getTaskUserFieldsManager();
        $this->user_fields_manager->setValues($task['custom_fields']);
    }

    /**
     * Возвращает список объектов пользователей, которые должны получить данное сообщение
     */
    function getRecipientUsersData()
    {
        $emails = [];
        $phones = [];
        $user_ids = [];

        //Если изменяет третий человек, то получают уведомление и создатель и исполнитель
        $no_one = ($this->task['creator_user_id'] != $this->changer_user_id) &&
                    ($this->task['implementer_user_id'] != $this->changer_user_id);

        if ($no_one || $this->task['creator_user_id'] == $this->changer_user_id) {
            //Если изменяет создатель, то получает сообщение исполнитель
            $user = $this->task->getImplementerUser();
            if ($user_id = $user['id']) {
                if ($user->e_mail) $emails[$user_id] = $user->e_mail;
                if ($user->phone) $phones[$user_id] = $user->phone;
                $user_ids[$user_id] = $user_id;
            }
        }

        if ($no_one || $this->task['implementer_user_id'] == $this->changer_user_id) {
            //Если изменяет исполнитель, то получает сообщение создатель
            $user = $this->task->getCreatorUser();
            if ($user_id = $user['id']) {
                if ($user->e_mail) $emails[$user_id] = $user->e_mail;
                if ($user->phone) $phones[$user_id] = $user->phone;
                $user_ids[$user_id] = $user_id;
            }
        }

        return [
            'emails' => $emails,
            'phones' => $phones,
            'user_ids' => $user_ids
        ];
    }


    /**
     * Возвращает путь к шаблону уведомления для Desktop приложения
     *
     * @return string
     */
    public function getTemplateDesktopApp()
    {
        return '%crm%/notice/change_task_to_user_desktop.tpl';
    }

    /**
     * Возвращает данные, которые необходимо передать при инициализации уведомления
     *
     * @return NoticeDataDesktopApp
     */
    public function getNoticeDataDesktopApp()
    {
        if (isset($this->recipients_data['user_ids'][0])) {
            $notice_data = new NoticeDataDesktopApp();
            $notice_data->title = t('Изменена задача №%0', [$this->task->task_num]);

            $notice_data->destination_user_id = $this->recipients_data['user_ids'][0];

            $notice_data->short_message = t('%title %creator', [
                'title' => $this->task['title'],
                'creator' => ($this->task['creator_user_id'] ? t('от %0', [$this->task->getCreatorUser()->getFio()]) : '')
            ]);
            $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->task['id']], 'crm-taskctrl', true);
            $notice_data->link_title = t('Перейти к задаче');
            $notice_data->vars = $this;

            return $notice_data;
        }
    }

    /**
     * Возвращает путь к шаблону письма
     * @return string
     */
    public function getTemplateEmail()
    {
        return '%crm%/notice/change_task_to_user_email.tpl';
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return \Alerts\Model\Types\NoticeDataEmail
     */
    public function getNoticeDataEmail()
    {
        $notice_data = new NoticeDataEmail();

        $notice_data->email     = implode(',', $this->recipients_data['emails']);
        $notice_data->subject   = t('Изменена задача №%0', [$this->task['task_num']]);
        $notice_data->vars      = $this;

        return $notice_data;
    }

    /**
     * Возвращает путь к шаблону SMS-сообщения
     * @return string
     */
    public function getTemplateSms()
    {
        return '%crm%/notice/change_task_to_user_sms.tpl';
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
        $notice_data->phone = implode(',', $this->recipients_data['phones']);

        return $notice_data;
    }
}