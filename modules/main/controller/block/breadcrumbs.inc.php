<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Block;

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
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }    
    
}
