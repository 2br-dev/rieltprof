<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Block;

use Main\Model\Microdata\MicrodataBreadcrumbs;
use RS\Application\Application;

/**
* Блок Хлебные крошки
*/
class BreadCrumbs extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Путь к текущему разделу',
        $controller_description = 'Отображает навигационную цепочку, ведущую к текущему разделу на сайте';
    
    protected
        $default_params = [
            'indexTemplate' => 'blocks/breadcrumbs/breadcrumbs.tpl',
    ];
        
    function actionIndex()
    {
        $breadcrumbs_data = Application::getInstance()->breadcrumbs->getBreadCrumbs();
        Application::getInstance()->microdata->addMicrodata(new MicrodataBreadcrumbs($breadcrumbs_data));

        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}
