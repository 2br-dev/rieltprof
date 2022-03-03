<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Block;
use \RS\Orm\Type;

/**
* Блок - Произвольный шаблон Smarty
*/
class UserTemplate extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Произвольный шаблон',
        $controller_description = 'Рендерит заданный шаблон Smarty. Удобен для вставки на разных страницах идентичных блоков HTML';
    
    function actionIndex()
    {
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}