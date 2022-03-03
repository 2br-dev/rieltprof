<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Board;

use Crm\Model\DealApi;
use RS\Module\AbstractModel\EntityList;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Router\Manager as RouterManager;

/**
 * Сделки на доске Kanban
 */
class DealBoardItem extends AbstractBoardItem
{
    /**
     * Возвращает название типа объектов, которые будут отображаться на kanban доске
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Сделки');
    }

    /**
     * Возвращает идентификатор объекта, к которому привязан статус
     *
     * @return mixed
     */
    public function getStatusObjectType()
    {
        return 'crm-deal';
    }

    /**
     * Возвращает кнопки, которые следует отобразить в верхней панели
     *
     * @return array
     */
    public function getButtons($filters = [])
    {
        $router = RouterManager::obj();
        return [
            new ToolbarButton\Add($router->getAdminUrl('add', [], 'crm-dealctrl'), t('Добавить сделку')),
            new ToolbarButton\Button($router->getAdminUrl(false, [], 'crm-dealctrl'), t('Табличный вид'))
        ];
    }


    /**
     * Возвращает объект класса EntityList
     *
     * @return DealApi
     */
    public function getApi()
    {
        $api = new DealApi();
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
        $deal_api = $this->getApi();
        $deal_api->initRightsFilters();
        $deal_api->setFilter('status_id', $status_id);

        return $deal_api;
    }


    /**
     * Возвращает путь к шаблону, который будет отвечать за отображение элмента на доске
     *
     * @return string
     */
    public function getItemTemplate()
    {
        return '%crm%/admin/board/itemtype/deal.tpl';
    }
}