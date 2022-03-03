<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Menu\Controller\Admin;

/**
* Контроллер для отображения меню в администартивной части
*/
class View extends \RS\Controller\Admin\Block
{
    protected 
        $api;
    
    function __construct()
    {
        parent::__construct();
        $this->api = new \Menu\Model\Api();
    }
    
    function actionIndex()
    {           
        $items = $this->api->getAdminMenu();

        $this->view->assign('items', $items);
        return $this->view->fetch('adminmenu.tpl');
    }    
}


