<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Config\ModuleRights;
use Crm\Model\Orm\Task;
use Crm\Model\Orm\TaskFilter;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Config\Loader;
use \RS\Html\Filter;
use RS\Orm\Request;

/**
 * Класс для организации выборок ORM объекта
 */
class TaskApi extends AbstractLinkedApi
{
    protected
        $implementer_user_id_field = 'implementer_user_id';

    function __construct()
    {
        parent::__construct(new Orm\Task(), [
            'defaultOrder' => 'date_of_create DESC',
            'sortField' => 'board_sortn'
        ]);
    }

    /**
     * Возвращает структуру фильтра для фильтрации задач
     *
     * @return Filter\Control
     */
    public function getFilterControl()
    {
        $field_manager = Loader::byModule($this)->getTaskUserFieldsManager();

        return new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['items' => [
                        new Filter\Type\Text('task_num', t('Номер'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('title', t('Короткое описание'), ['SearchType' => '%like%']),
                        new Filter\Type\User('creator_user_id', t('Создатель')),
                        new Filter\Type\User('implementer_user_id', t('Исполнитель')),
                        new Filter\Type\Select('status_id', t('Статус'), StatusApi::staticSelectList(['' => t('Любой')], 'crm-task')),
                        new Filter\Type\Select('is_archived', t('Архивная?'), ['' => t('Не важно'), '0' => t('Нет'), '1' => t('Да')]),
                        new Filter\Type\DateRange('date_of_create', t('Дата создания')),
                        new Filter\Type\DateRange('date_of_planned_end', t('План окончания')),
                        new Filter\Type\DateRange('date_of_end', t('Дата завершения')),
                        new \Crm\Model\FilterType\CustomFields('custom_fields', $field_manager, $this->getElement()->getShortAlias()),
                        new \Crm\Model\FilterType\Links('links', Task::getAllowedLinkTypes(), Task::getLinkSourceType())
                    ]
                    ])
                ],
            ]),
            'ExcludeGetParams' => ['dir'],
            'Caption' => t('Поиск по задачам')
        ]);
    }

    /**
     * Применяет сохраненный фильтр
     *
     * @param TaskFilter $filter
     */
    public function applyFilter(TaskFilter $filter)
    {
        $filter_control = $this->getFilterControl();
        $filter_control->fill($filter['filters_arr'] ?: []);

        $this->addFilterControl($filter_control);
    }

    /**
     * Добавляет фильтр, который исключает архивные задачи
     *
     * @return void
     */
    public function excludeArchivedItems()
    {
        $this->setFilter('is_archived', 0);
    }

    /**
     * Устанавливает фильтры, которые соответствуют правам текущего пользователя
     */
    function initRightsFilters()
    {
        //Если у пользователя нет прав на просмотр чужих объектов, то не отображаем их.
        $user = Auth::getCurrentUser();

        if (!Rights::hasRight($this, ModuleRights::TASK_OTHER_READ)) {
            $filters = [
                //Отображаем только те объекты, которые мы создали
                $this->creator_user_id_field => $user['id']
            ];

            //Или те объекты, которые нам назначены
            $filters['|' . $this->implementer_user_id_field] = $user['id'];

            $this->setFilter([$filters]);
        }
    }

    /**
     * Возвращает задачи, по которым возможно необходимо отправить уведомление о предстоящем завершении
     *
     * @return integer Возвращает количество задач, для которых было отправлено уведомление
     */
    function sendTaskNotice()
    {
        $config = Loader::byModule($this);

        $times = $config->getNoticeExpirationTimeList();
        end($times);
        $max_interval = key($times);
        $current_time = time();
        $page_size = 50;

        //Выбираем задачи, которые могут истечь в ближайшее время
        $q = Request::make()
                ->from(new Task())
                ->limit($page_size)
                ->where([
                    'expiration_notice_is_send' => 0
                ])
                ->where("expiration_notice_time != 0 
                        AND date_of_planned_end <= '#max_interval' 
                        AND date_of_planned_end > '#current_time'
                        AND implementer_user_id > 0",
                    [
                        'current_time' => date('Y-m-d H:i:s', $current_time),
                        'max_interval' => date('Y-m-d H:i:s', $current_time + $max_interval)
                    ]);

        $need_statuses = (array)$config->expiration_task_notice_statuses;
        if ($need_statuses && !in_array(0, $need_statuses)) {
            $q->whereIn('status_id', $need_statuses);
        }

        $count = 0;
        $offset = 0;
        while($tasks = $q->offset($offset)->objects()) {
            foreach($tasks as $task) {
                if ($task->isTimeToExpire()) {
                    $task->sendExpireNotice();
                    $count++;
                }
            }
            $offset += $page_size;
        }

        return $count;
    }
}
