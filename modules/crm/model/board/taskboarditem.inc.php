<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Board;

use Crm\Model\Orm\TaskFilter;
use Crm\Model\TaskApi;
use Crm\Model\TaskFilterApi;
use RS\Module\AbstractModel\EntityList;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Router\Manager as RouterManager;

/**
 * Задачи на доске Kanban
 */
class TaskBoardItem extends AbstractBoardItem
{
    /**
     * Возвращает название типа объектов, которые будут отображаться на kanban доске
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Задачи');
    }

    /**
     * Возвращает идентификатор объекта, к которому привязан статус
     *
     * @return mixed
     */
    public function getStatusObjectType()
    {
        return 'crm-task';
    }

    /**
     * Возвращает фильтры, которые поддерживаются данным типом объектов
     *
     * @return array
     */
    public function getFilters()
    {
        $task_filters_api = new TaskFilterApi();
        $filters = $task_filters_api->getSelectList([0 => t('Все')]);

        return [
            'preset_id' => $filters,
        ];
    }

    /**
     * Возвращает значения фильтров по умолчанию. (в случае, если значение не задано явно)
     *
     * @return array
     */
    public function getDefaultFilterValues()
    {
        return [
            'preset_id' => 0
        ];
    }

    /**
     * Возвращает кнопки, которые следует отобразить в верхней панели
     *
     * @return array
     */
    public function getButtons($filters = [])
    {
        $params = [];
        if (!empty($filters['preset_id'])) {
            $taskFilter = new TaskFilter($filters['preset_id']);
            $params = ['dir' => $taskFilter['id'], 'f' => $taskFilter['filters_arr']];

        }

        $router = RouterManager::obj();
        return [
            new ToolbarButton\Add($router->getAdminUrl('add', [], 'crm-taskctrl'), t('Добавить задачу')),
            new ToolbarButton\Button($router->getAdminUrl(false, $params, 'crm-taskctrl'), t('Табличный вид'))
        ];
    }

    /**
     * Возвращает объект класса EntityList
     *
     * @return TaskApi
     */
    public function getApi()
    {
        $api = new TaskApi();
        $api->initRightsFilters();
        $api->excludeArchivedItems();
        $api->setDefaultOrder('board_sortn');
        return $api;
    }

    /**
     * Возвращает объект EntityList с установленными фильтрами
     *
     * @param $status_id
     * @param $filters
     * @return EntityList
     */
    public function getApiWithFilters($status_id, $filters = [])
    {
        $task_api = $this->getApi();
        $task_api->setFilter('status_id', $status_id);

        if (!empty($filters['preset_id'])) {
            $task_filter = new TaskFilter($filters['preset_id']);
            $task_api->applyFilter($task_filter);
        }

        return $task_api;
    }

    /**
     * Возвращает путь к шаблону, который будет отвечать за отображение элмента на доске
     *
     * @return string
     */
    public function getItemTemplate()
    {
        return '%crm%/admin/board/itemtype/task.tpl';
    }
}