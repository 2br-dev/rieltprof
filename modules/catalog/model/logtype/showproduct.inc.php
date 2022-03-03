<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Logtype;

/**
* Класс - событие. Просмотр товара
*/
class ShowProduct extends \Users\Model\LogtypeAbstract
{
    function getObject()
    {
        $product = new \Catalog\Model\Orm\Product();
        $product->load($this->oid);
        return $product;
    }
    
}

