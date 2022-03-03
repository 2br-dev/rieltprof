<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Config;

use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use RS\Module\AbstractInstall;

/**
 * Класс отвечает за установку и обновление модуля
 */
class Install extends AbstractInstall
{
    function install()
    {
        $result = parent::install();
        if ($result) {

            //Обновляем структуру базы данных для объекта Товар
            $product = new Product();
            $product->dbUpdate();

            //Обновляем структуру базы данных для объекта Категория Товара
            $offer = new Offer();
            $offer->dbUpdate();
        }
        return $result;
    }
}
