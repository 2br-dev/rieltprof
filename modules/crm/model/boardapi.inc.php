<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;
use Crm\Model\Board\AbstractBoardItem;
use RS\Event\Manager;
use RS\Module\AbstractModel\EntityList;

/**
 * Класс содержит методы по работе с доской kanban
 */
class BoardApi
{
    /**
     * Возвращает все зарегистрированные в системе типы элементов для отображения на доске Kanban
     *
     * @return AbstractBoardItem[]
     */
    public static function getAllBoardItems()
    {
        static $board_item_types;

        if (!isset($board_item_types)) {
            $board_item_types_list = Manager::fire('crm.getboardtypes', [])->getResult();

            $board_item_types = [];
            foreach ($board_item_types_list as $item) {
                if (!($item instanceof AbstractBoardItem)) {
                    throw new \RS\Exception(t('Класс-тип элементов для доски kanban должен быть потомком класса Crm\Model\Board\AbstractBoardItem'));
                }
                $board_item_types[$item->getStatusObjectType()] = $item;
            }
        }

        return $board_item_types;
    }

    /**
     * Возвращает экземпляр типа элемента по его идентификатору
     *
     * @param string $object_type
     * @return AbstractBoardItem
     */
    public static function makeBoardItemByObjectType($object_type)
    {
        $board_item_types = self::getAllBoardItems();

        if (!isset($board_item_types[$object_type])) {
            throw new \RS\Exception(t('Тип элементов с идентификатором `%0` не зарегистрирован', [$object_type]));
        }

        return $board_item_types[$object_type];
    }

    /**
     * Изменяет у элемента статус(при необходимости), а затем сортирует его среди элементов с тем же статусом
     *
     * @param EntityList $items_api
     * @param integer $from ID элемента, который нужно переместить
     * @param integer $to ID элемента, на место которого нужно переместить
     * @param string $direction up|down - флаг, выше или ниже жлемента $to нужно разместить элемент $from
     * @param integer $new_status_id ID нового статуса
     * @return bool
     */
    public function moveItem(EntityList $items_api, $from, $to, $direction, $new_status_id)
    {
        $items_api->setFilter('id', $from);
        if ($from_item = $items_api->getFirst()) {
            if ($from_item['status_id'] != $new_status_id) {
                $from_item['status_id'] = $new_status_id;
                $from_item->update();
            }

            if ($to && $direction) {
                return $items_api->moveElement($from, $to, $direction);
            } else {
                return true;
            }
        }

        return false;
    }
}