<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Controller\Block;
use \RS\Orm\Type;

/**
* Блок-контроллер Статья
*/
class Article extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Статья',
        $controller_description = 'Отображает текст одной статьи';

    protected
        $default_params = [
            'indexTemplate' => 'blocks/article/article.tpl'
    ];
        
    /**
    * Возвращает ORM объект, содержащий настриваемые параметры или false в случае, 
    * если контроллер не поддерживает настраиваемые параметры
    * @return \RS\Orm\ControllerParamObject | false
    */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
                'article_id' => new Type\Varchar([
                    'description' => t('Выберите статью из списка'),
                    'list' => [['\Article\Model\Api', 'staticSelectList']]
                ])
        ]);
    }        

    function actionIndex()
    {
        $api = new \Article\Model\Api();
        $article = $api->getById($this->getParam('article_id'));
        $this->view->assign([
            'article' => $article
        ]);
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}