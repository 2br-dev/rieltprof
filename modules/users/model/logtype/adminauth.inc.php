<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Users\Model\Logtype;

/**
* Тип события - авторизация администратора
*/
class AdminAuth extends \Users\Model\LogtypeAbstract
{
    function getObject()
    {
        $product = new \Users\Model\Orm\User();
        $product->load($this->oid);
        return $product;
    }
    
    function getIP()
    {
        $data = $this->getData();
        return $data['ip'];
    }
}

