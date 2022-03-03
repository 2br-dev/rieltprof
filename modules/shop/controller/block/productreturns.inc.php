<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
/**
 * Created by PhpStorm.
 * User: Пользователь
 * Date: 09.10.2017
 * Time: 14:53
 */
namespace Shop\Controller\Block;

class ProductReturns extends \RS\Controller\Block
{
    protected static
                $controller_title = 'Возвраты кнопка',
                $controller_description = 'Добавляет кнопку возвраты на сайт';

    function actionIndex()
    {
        return $this -> result -> setTemplate('blocks/productreturns/productreturns.tpl');
    }
}