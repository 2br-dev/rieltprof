<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use RS\Config\Loader as ConfigLoader;

/**
 *  Возвращает текущий класс учета остатков
 *
 * Class StockManager
 * @package Catalog\Model
 */
class StockManager
{
    /**
     * Получить объект менеджера остатков
     *
     * @return InventoryManager|SimpleStockManager
     */
    static function getInstance()
    {
        $config = ConfigLoader::byModule('catalog');
        $ic_enable = $config['inventory_control_enable'];
        if ($ic_enable) {
            return new InventoryManager();
        } else {
            return new SimpleStockManager();
        }
    }
}
