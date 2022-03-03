<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;

use Alerts\Model\Manager;
use Crm\Config\ModuleRights;
use Crm\Model\AutoTaskRuleApi;
use Crm\Model\Links\LinkManager;
use Crm\Model\Links\Type\AbstractType;
use Crm\Model\Links\Type\LinkTypeCall;
use Crm\Model\Links\Type\LinkTypeDeal;
use Crm\Model\Links\Type\LinkTypeOrder;
use Crm\Model\Links\Type\LinkTypeUser;
use Crm\Model\Notice\ChangeTaskToUser;
use Crm\Model\Notice\NewTaskToImplementer;
use Crm\Model\Notice\TaskSoonExpireToImplementer;
use Files\Model\FileApi;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\Event\Manager as EventManager;
use RS\Module\Manager as ModuleManager;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use Users\Model\Orm\User;

/**
 * ORM объект - "задача"
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $task_num Уникальный номер задачи
 * @property array $links Связь с другими объектами
 * @property string $title Суть задачи
 * @property integer $status_id Статус
 * @property string $description Описание
 * @property string $date_of_create Дата создания
 * @property string $date_of_planned_end Планируемая дата завершения задачи
 * @property string $date_of_end Фактическая дата завершения задачи
 * @property integer $expiration_notice_time Уведомить исполнителя о скором истечении срока выполнении задачи за...
 * @property integer $expiration_notice_is_send Было ли отправлено уведомление об истечении срока выполнения задачи?
 * @property integer $creator_user_id Создатель задачи
 * @property integer $implementer_user_id Исполнитель задачи
 * @property integer $board_sortn Сортировочный индекс на доске
 * @property integer $is_archived Задача архивная?
 * @property integer $autotask_index Порядковый номер автозадачи
 * @property integer $autotask_group Идентификатор группы связанных заказов
 * @property integer $is_autochange_status Включить автосмену статуса
 * @property array $autochange_status_rule_arr Условия для смены статуса
 * @property string $autochange_status_rule Условия для смены статуса
 * --\--
 */
class Task extends OrmObject
{
    const
        PLANNED_END_STATUS_BLACK = 'black',
        PLANNED_END_STATUS_ORANGE = 'orange',
        PLANNED_END_STATUS_RED = 'red',
        PLANNED_END_STATUS_GREEN = 'green',

        ORANGE_STATUS_DAYS = 2,

        FILES_LINK_TYPE = 'Crm-CrmTask';

    protected static
        $table = 'crm_task';

    protected $before;

    function _init()
    {
        $config = Loader::byModule(__CLASS__);

        parent::_init()->append([
            t('Основные'),
                'task_num' => new Type\Varchar([
                    'description' => t('Уникальный номер задачи'),
                    'hint' => t('Может использоваться для быстрой идентификации задачи внутри компании'),
                    'maxLength' => 20,
                    'unique' => true,
                    'meVisible' => false,
                ]),
                'links' => new \Crm\Model\OrmType\Link([
                    'description' => t('Связь с другими объектами'),
                    'allowedLinkTypes' => [self::getAllowedLinkTypes()],
                    'linkSourceType' => self::getLinkSourceType(),
                    'hint' => t('После связывания с другими объектами, вы сможете найти данную задачу прямо в карточках привязанных объектов'),
                    'compare' => function($value, $property, $orm) {
                        //Функция, возвращающая строковое значение данного поля для сравнения изменений
                        $result = [];
                        if ($value) {
                            foreach ($value as $link_type => $link_ids) {
                                foreach ($link_ids as $id) {
                                    $link = AbstractType::makeById($link_type);
                                    $link->init($id);
                                    $result[] = $link->getLinkText();
                                }
                            }
                        }

                        return implode(', ', $result);
                    },
                    'meVisible' => false,
                ]),
                'title' => new Type\Varchar([
                    'description' => t('Суть задачи'),
                    'checker' => ['ChkEmpty', t('Опишите суть задачи в одном предложении')],
                    'meVisible' => false,
                ]),
                'status_id' => new Type\Integer([
                    'description' => t('Статус'),
                    'list' => [['\Crm\Model\Orm\Status', 'getStatusesTitles'], 'crm-task']
                ]),
                'description' => new Type\Text([
                    'description' => t('Описание'),
                    'meVisible' => false,
                ]),
                'date_of_create' => new Type\Datetime([
                    'description' => t('Дата создания'),
                    'meVisible' => false,
                ]),
                'date_of_planned_end' => new Type\Datetime([
                    'description' => t('Планируемая дата завершения задачи')
                ]),
                'date_of_end' => new Type\Datetime([
                    'description' => t('Фактическая дата завершения задачи')
                ]),
                'expiration_notice_time' => new Type\Integer([
                    'description' => t('Уведомить исполнителя о скором истечении срока выполнении задачи за...'),
                    'hint' => t('Уведомление будет отправлено только, если статус задачи будет удовлетворять условиям в настройках модуля CRM и на сайте настроен планировщик'),
                    'list' => [[$config, 'getNoticeExpirationTimeList']],
                    'default' => $config->expiration_task_default_notice_time
                ]),
                'expiration_notice_is_send' => new Type\Integer([
                    'description' => t('Было ли отправлено уведомление об истечении срока выполнения задачи?'),
                    'checkboxView' => [1,0],
                ]),
                'creator_user_id' => new Type\User([
                    'description' => t('Создатель задачи'),
                    'compare' => function($value, $property, $orm) {
                        $user = new User($value);
                        return $user->getFio()."($value)";
                    }
                ]),
                'implementer_user_id' => new Type\User([
                    'description' => t('Исполнитель задачи'),
                    'requestUrl' => \RS\Router\Manager::obj()->getAdminUrl('ajaxEmail', [
                        'groups' => Loader::byModule($this)->implementer_user_groups
                    ], 'users-ajaxlist'),
                    'compare' => function($value, $property, $orm) {
                        $user = new User($value);
                        return $user->getFio()."($value)";
                    }
                ]),
                'board_sortn' => new Type\Integer([
                    'description' => t('Сортировочный индекс на доске'),
                    'visible' => false
                ]),
                'is_archived' => new Type\Integer([
                    'allowEmpty' => false,
                    'description' => t('Задача архивная?'),
                    'hint' => t('Архивные задачи не отображаются на Kanban доске'),
                    'checkboxView' => [1,0]
                ]),
            t('Настройки автозадачи'),
                'autotask_index' => new Type\Integer([
                    'description' => t('Порядковый номер автозадачи'),
                    'readOnly' => true,
                    'compare' => false,
                    'meVisible' => false,
                ]),
                'autotask_group' => new Type\Integer([
                    'description' => t('Идентификатор группы связанных заказов'),
                    'hint' => t('Используется в сгенерированных автоматически задачах'),
                    'readOnly' => true,
                    'compare' => false,
                    'meVisible' => false,
                ]),
                'is_autochange_status' => new Type\Integer([
                    'description' => t('Включить автосмену статуса'),
                    'checkboxView' => [1,0],
                    'compare' => false,
                    'meVisible' => false,
                ]),
                'autochange_status_rule_arr' => new Type\ArrayList([
                    'description' => t('Условия для смены статуса'),
                    'template' => '%crm%/form/tasktemplate/autochange_rule.tpl',
                    'checker' => [function($_this, $value) {
                        if ($_this['is_autochange_status'] && !$value) {
                            return t('Необходимо добавить хотя бы одно условие для смены статуса');
                        }
                        return true;
                    }],
                    'compare' => false,
                    'meVisible' => false,
                ]),
                'autochange_status_rule' => new Type\Text([
                    'description' => t('Условия для смены статуса'),
                    'visible' => false,
                    'compare' => false
                ])
        ]);


        $user_field_manager = Loader::byModule($this)
            ->getTaskUserFieldsManager()
            ->setArrayWrapper('custom_fields');

        if ($user_field_manager->notEmpty()) {
            $this->getPropertyIterator()->append([
                t('Доп. поля'),
                'custom_fields' => new \Crm\Model\OrmType\CustomFields([
                    'description' => t('Доп.поля'),
                    'fieldsManager' => $user_field_manager,
                    'checker' => [['\Crm\Model\Orm\CustomData', 'validateCustomFields'], 'custom_fields'],
                    'compare' => function($value, $property, $orm) use($user_field_manager) {
                        $lines = [];
                        $user_field_manager->setValues($value);
                        foreach($user_field_manager->getStructure() as $item) {
                            $lines[] = $item['title'].':'.$item['current_val'];
                        }

                        return implode("<br>\n", $lines);
                    }
                ])
            ]);
        }

        $this->getPropertyIterator()->append([
            t('Файлы'),
                '__files__' => new \Files\Model\OrmType\Files([
                    'linkType' => self::FILES_LINK_TYPE
                ])
        ]);


        //Включаем в форму hidden поле id.
        $this['__id']->setVisible(true);
        $this['__id']->setMeVisible(false);
        $this['__id']->setHidden(true);

        $this->addIndex(['expiration_notice_is_send', 'date_of_planned_end', 'status_id'], self::INDEX_KEY);
    }



    /**
     * Устанавливает права для полей ORM объекта
     *
     * @param string $flag Флаг опреации insert или update
     * @return void
     */
    public function initUserRights($flag)
    {
        $current_user_id = Auth::getCurrentUser()->id;

        if (!Rights::hasRight($this, ModuleRights::TASK_CHANGE_CREATOR_USER)) {
            $this['__creator_user_id']->setReadOnly(true);
            $this['__creator_user_id']->setListenPost(false);
            $this['__creator_user_id']->setMeVisible(false);
        }

        if ($flag == self::UPDATE_FLAG) {
            if (!Rights::hasRight($this, ModuleRights::TASK_CHANGE_IMPLEMENTER_USER)) {
                $this['__implementer_user_id']->setReadOnly(true);
                $this['__implementer_user_id']->setListenPost(false);
                $this['__implementer_user_id']->setMeVisible(false);
            }

            if (!Rights::hasRight($this, ModuleRights::TASK_CHANGE_PLANNED_END)) {
                $this['__date_of_planned_end']->setReadOnly(true);
                $this['__date_of_planned_end']->setListenPost(false);
                $this['__date_of_planned_end']->setMeVisible(false);
            }
        }
    }

    /**
     * Возвращает объект пользователя, создателя задачи
     *
     * @return User
     */
    public function getCreatorUser()
    {
        return new User($this['creator_user_id']);
    }

    /**
     * Возвращает объект пользователя, исполнителя задачи
     *
     * @return User
     */
    public function getImplementerUser()
    {
        return new User($this['implementer_user_id']);
    }

    /**
     * Обработчик, вызывается перед сохранением объекта
     *
     * @param string $flag
     */
    public function beforeWrite($flag)
    {
        $this->before = new self($this['id']);

        if ($this['id'] < 0) {
            $this['_tmpid'] = $this['id'];
            unset($this['id']);
        }

        if (!$this['is_autochange_status']) {
            $this['autochange_status_rule_arr'] = [];
        }

        $this['autochange_status_rule'] = serialize($this['autochange_status_rule_arr']);

        if ($flag == self::UPDATE_FLAG) {
            $no_right_change_creator = !Rights::hasRight($this, ModuleRights::TASK_CHANGE_CREATOR_USER);
            $no_right_change_implementer = !Rights::hasRight($this, ModuleRights::TASK_CHANGE_IMPLEMENTER_USER);

            if ($no_right_change_creator && $this->before['creator_user_id'] != $this['creator_user_id']) {
                return $this->addError($no_right_change_creator);
            }

            if ($no_right_change_implementer && $this->before['implementer_user_id'] != $this['implementer_user_id']) {
                return $this->addError($no_right_change_implementer);
            }

            if ($this->getStatus()->is_status_complete && !$this['date_of_end']) {
                $this['date_of_end'] = date('Y-m-d H:i:s');
            }
        }

        if ($flag == self::INSERT_FLAG) {
            //Устанавливаем максимальный сортировочный индекс
            $this['board_sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(board_sortn) as max')
                    ->from($this)
                    ->exec()->getOneField('max', 0) + 1;
        }
    }

    /**
     * Обработчик сохранения объекта
     *
     * @param string $flag
     */
    public function afterWrite($flag)
    {
        if ($this['_tmpid'] < 0) {
            //Переносим файлы к сохраненному объекту
            FileApi::changeLinkId($this['_tmpid'], $this['id'], self::FILES_LINK_TYPE);

        }

        if ($this->isModified('links')) {
            $this->before->fillLinks(); //Загрузим данные по предыдущему состоянию, чтобы далее сравнить изменения
            LinkManager::saveLinks($this->getLinkSourceType(), $this['id'], $this['links']);
        }

        CustomData::saveCustomFields($this->getShortAlias(), $this['id'], $this['custom_fields']);

        if (!$this['disable_autochange_status'] && $this->before['status_id'] != $this['status_id']) {
            AutoTaskRuleApi::autoChangeStatus($this);
        }

        if ($flag == self::INSERT_FLAG && $this['implementer_user_id']) {
            $notice = new NewTaskToImplementer();
            $notice->init($this);
            Manager::send($notice);
        }

        if ($flag == self::UPDATE_FLAG) {
            if ($changes = $this->getChanges($this->before)) {
                $current_user_id = Auth::getCurrentUser()->id;

                //Отправим уведомление об изменении объекта
                $notice = new ChangeTaskToUser();
                $notice->init($this, $changes, $current_user_id);

                Manager::send($notice);
            }
        }
    }


    /**
     * Обработчик, вызывается сразу после загрузки объекта
     */
    public function afterObjectLoad()
    {
        //Сохраняем значения доп. полей в дополнительную таблицу
        $this['custom_fields'] = CustomData::loadCustomFields($this->getShortAlias(), $this['id']);
        $this['autochange_status_rule_arr'] = @unserialize($this['autochange_status_rule']) ?: [];
    }

    /**
     * Возвращает идентификатор в менеджере связей
     *
     * @return string
     */
    public static function getLinkSourceType()
    {
        return 'task';
    }


    /**
     * Возвращает список возможных родительских объектов
     *
     * @return string[]
     */
    public static function getAllowedLinkTypes()
    {
        $allow_link_types = [
            LinkTypeDeal::getId(),
            LinkTypeCall::getId(),
            LinkTypeUser::getId()
        ];

        $event_result = EventManager::fire('crm.task.getlinktypes', $allow_link_types);
        $allow_link_types = $event_result->getResult();

        return $allow_link_types;
    }

    /**
     * Удаляет текущий объект, а также все ссылки на него
     *
     * @return bool
     */
    public function delete()
    {
        if ($result = parent::delete()) {
            //Удаляем ссылки связи с объектами
            LinkManager::removeLinks($this->getLinkSourceType(), $this['id']);
        }
        return $result;
    }

    /**
     * Возвращает объект статуса
     *
     * @return mixed
     */
    public function getStatus()
    {
        return new Status($this['status_id']);
    }

    /**
     * Возвращает цвет, которым следует подсветить дату планируемого завершения
     * черным - если до нее более двух дней
     * желтым - если до нее менее двух дней
     * красным - если дата просрочена и дата фактического завершения задачи позже
     * зеленым - если дата просрочена, но дата фактического завершения уложилась в срок
     *
     * @return string
     */
    public function getPlannedEndStatus()
    {
        $planned_end_time = strtotime($this['date_of_planned_end']);
        $yellow_time = $planned_end_time - 60*60*24*self::ORANGE_STATUS_DAYS; //За 2 дня задачи будут оранжевым подсвечены

        $end_time = strtotime($this['date_of_end']);
        $now = time();

        if ($now < $yellow_time) {

            return self::PLANNED_END_STATUS_BLACK;

        } elseif ($now >= $yellow_time && $now < $planned_end_time) {

            return self::PLANNED_END_STATUS_ORANGE;

        } else {
            if ($this['date_of_end'] && $end_time <= $planned_end_time) {
                return self::PLANNED_END_STATUS_GREEN;
            }

            return self::PLANNED_END_STATUS_RED;
        }
    }

    /**
     * Возвращает пояснение к планируемой дате завершения задачи
     *
     * @return string
     */
    public function getPlannedEndStatusTitle()
    {
        $status = $this->getPlannedEndStatus();

        switch ($status) {
            case self::PLANNED_END_STATUS_BLACK:
                $status_title = t('Срок еще не истек'); break;
            case self::PLANNED_END_STATUS_ORANGE:
                $status_title = t('Скоро истекает'); break;
            case self::PLANNED_END_STATUS_GREEN:
                $status_title = t('Задача завершена в срок'); break;
            case self::PLANNED_END_STATUS_RED:
                $status_title = t('Задача просрочена'); break;
        }

        return $status_title;
    }

    /**
     * Скрывает вкладку "Настройки автозадачи"
     */
    public function hideAutoTaskTab()
    {
        $this['__autotask_index']->setVisible(false);
        $this['__autotask_group']->setVisible(false);
        $this['__is_autochange_status']->setVisible(false);
        $this['__autochange_status_rule_arr']->setVisible(false);
    }

    /**
     * Возвращает массив с измененными полями в сравнении с предыдущим состоянием объекта
     *
     * @param Task $before_task
     * @return array
     */
    public function getChanges(Task $before_task)
    {
        $result = [];
        foreach($this->getProperties() as $key => $property) {
            if (isset($property->compare) && $property->compare === false) continue;

            if (isset($property->compare) && is_callable($property->compare)) {
                //Получаем значения из кастомной функции
                $current = call_user_func($property->compare, $this[$key], $property, $this);
                $before = call_user_func($property->compare, $before_task[$key], $property, $before_task);
            } else {
                //Получаем обычные значения
                $current = $property->textView();
                $before = $before_task['__'.$key]->textView();
            }

            if ($current != $before) {
                $result[$key] = [
                    'title' => $property->getDescription(),
                    'before_value' => $before,
                    'current_value' => $current
                ];
            }
        }

        return $result;
    }

    /**
     * Загружает данные в поле Links
     */
    public function fillLinks()
    {
        $links = \RS\Orm\Request::make()
            ->from(new \Crm\Model\Orm\Link())
            ->where([
                'source_type' => $this->getLinkSourceType(),
                'source_id' => $this['id']
            ])
            ->whereIn('link_type', $this['__links']->getAllowedLinkTypes())
            ->exec()->fetchSelected('link_type', 'link_id', true);

        $this['links'] = $links;
    }

    /**
     * Возвращает true, если задача создана текущим пользователем или назначена ему
     *
     * @return bool
     */
    public function isMyTask($user_id = null)
    {
        if ($user_id === null) {
            $user_id = Auth::getCurrentUser()->id;
        }

        return $this['creator_user_id'] == $user_id 
                    || $this['implementer_user_id'] == $user_id;
    }

    /**
     * Возвращает идентификатор права на чтение для данного объекта
     *
     * @return string
     */
    public function getRightRead()
    {
        return ModuleRights::TASK_READ;
    }

    /**
     * Возвращает идентификатор права на создание для данного объекта
     *
     * @return string
     */
    public function getRightCreate()
    {
        if ($this->isMyTask()) {
            return ModuleRights::TASK_CREATE;
        } else {
            return ModuleRights::TASK_OTHER_CREATE;
        }
    }

    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        if ($this->isMyTask()) {
            return ModuleRights::TASK_UPDATE;
        } else {
            return ModuleRights::TASK_OTHER_UPDATE;
        }
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        if (!$this['id'] || $this->isMyTask()) {
            return ModuleRights::TASK_DELETE;
        } else {
            return ModuleRights::TASK_OTHER_DELETE;
        }
    }

    /**
     * Возвращает true, если пришло время уведомить об окончании
     * Учитывается исключительно фактор времени
     *
     * @return bool
     */
    public function isTimeToExpire()
    {
        $date_of_planned_end_tm = strtotime($this['date_of_planned_end']);

        if ($this['expiration_notice_time'] >0 &&
            $date_of_planned_end_tm > time() &&
            $date_of_planned_end_tm <= time() + $this['expiration_notice_time'])
        {
            return true;
        }

        return false;
    }

    /**
     * Отправляет уведомление о скором истечении срока выполнения
     */
    public function sendExpireNotice()
    {
        $notice = new TaskSoonExpireToImplementer();
        $notice->init($this);

        Manager::send($notice);

        $this['expiration_notice_is_send'] = 1;
        $this->update();
    }
}
