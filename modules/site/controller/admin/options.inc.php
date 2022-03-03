<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Controller\Admin;

class Options extends \RS\Controller\Admin\ConfigEdit
{
    public function __construct()
    {                                      
        $orm_object = \RS\Config\Loader::getSiteConfig();
        $orm_object->replaceOn(true);
        $orm_object->tpl_module_folders = \RS\Module\Item::getResourceFolders('templates');
        
        parent::__construct($orm_object);
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Настройка сайта'));
        return $helper;
    }
}

