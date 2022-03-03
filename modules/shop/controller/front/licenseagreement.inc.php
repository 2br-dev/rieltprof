<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Front;

class LicenseAgreement extends \RS\Controller\Front
{
    function actionIndex()
    {
        $this->view->assign([
            'shop_config' => $this->getModuleConfig()
        ]);
        return $this->result->setTemplate( 'licagreement.tpl' );
    }
}