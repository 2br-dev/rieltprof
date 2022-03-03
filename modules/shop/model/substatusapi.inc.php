<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

/**
 * API для работы с причинами отмены заказов
 */
class SubStatusApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\SubStatus(), [
            'nameField' => 'title',
            'sortField' => 'sortn',
            'defaultOrder' => 'sortn',
            'multisite' => true,
        ]);
    }

    /**
     * Возвращает ассоциативный массив с ID и названиями оплат
     *
     * @param array $root - произвольный набор элементов, который будет помещен вначало
     * @return array
     */
    public static function staticSelectList($root = [])
    {
        $list = parent::staticSelectList();
        return $root + $list;
    }
}