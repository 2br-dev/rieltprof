<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Controller\Block;
use \RS\Orm\Type;

/**
* Блок - выбор сайта
*/
class Selector extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Выбор сайта',
        $controller_description = 'Позволяет переключаться между сайтами системы';
    
    protected
        $default_params = [
            'indexTemplate' => 'blocks/selector/sitelist.tpl'
    ];
    
    function actionIndex()
    {
        $api = new \Site\Model\Api();
        $api->setOrder('sortn');
        $sites = $api->getList();
        
        $this->view->assign([
            'sites' => $sites,
            'current_site' => \RS\Site\Manager::getSiteId()
        ]);
        
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}