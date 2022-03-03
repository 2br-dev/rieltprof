<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Controller\Admin;

class Ctrl extends \RS\Controller\Admin\Front
{

    public function actionIndex()
    {
        return $this->result->setTemplate('index.tpl');
    }

}