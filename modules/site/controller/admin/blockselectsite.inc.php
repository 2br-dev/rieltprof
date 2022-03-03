<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Controller\Admin;

class BlockSelectSite extends \RS\Controller\Admin\Block
{
    protected
        $default_params = [
            'indexTemplate' => 'block_select_site.tpl',
    ];
    
    function actionIndex()
    {
        $sites = \RS\Application\Auth::getCurrentUser()->getAllowSites();
        $this->view->assign([
            'sites' => $sites,
            'current' => \RS\Site\Manager::getAdminCurrentSite()
        ]);
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}

