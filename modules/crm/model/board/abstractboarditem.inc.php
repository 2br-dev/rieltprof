<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Board;
use RS\Module\AbstractModel\EntityList;

/**
 * Класс описывает абстрактный тип элементов, которые могут размещаться на Kanban доске
 */
abstract class AbstractBoardItem
{
    /**
     * Возвращает название типа объектов, которые будут отображаться на kanban доске
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Возвращает идентификатор объекта, к которому привязан статус
     *
     * @return mixed
     */
    abstract public function getStatusObjectType();

    /**
     * Возвращает фильтры, которые поддерживаются данным типом объектов
     *
     * @return array
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * Возвращает значения фильтров по умолчанию. (в случае, если значение не задано явно)
     *
     * @return array
     */
    public function getDefaultFilterValues()
    {
        return [];
    }

    /**
     * Возвращает кнопки, которые следует отобразить в верхней панели
     *
     * @return array
     */
    public function getButtons($filters = [])
    {
        return [];
    }

    /**
     * Возвращает объект EntityList
     *
     * @return EntityList
     */
    abstract public function getApi();

    /**
     * Возвращает объект EntityList с установленными фильтрами
     *
     * @param $status_id
     * @param $filters
     * @return EntityList
     */
    abstract public function getApiWithFilters($status_id, $filters = []);

    /**
     * Возвращает путь к шаблону, который будет отвечать за отображение элмента на доске
     *
     * @return string
     */
    abstract public function getItemTemplate();

}