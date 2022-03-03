<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;

class Compare extends \RS\Controller\Front
{
    public
        $api;
        
    function init()
    {
        $this->api = \Catalog\Model\Compare::currentCompare();        
    }
    
    function actionIndex()
    {
        $this->view->assign('comp_data', $this->api->getCompareData());
        return $this->result->setTemplate( 'compare.tpl' );  
    }
    
    function actionRemove()
    {
        $id = $this->url->post('id', TYPE_INTEGER);
        return $this->result->setSuccess( $this->api->removeProduct($id) )
                            ->addSection('total', $this->api->getCount());
    }                
}