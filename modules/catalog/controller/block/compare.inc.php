<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Block;

use Catalog\Model\Compare as CompareApi;
use RS\Controller\StandartBlock;
use RS\Orm\Type;

/**
* Блок-контроллер Список категорий
*/
class Compare extends StandartBlock
{
    protected static
        $controller_title = 'Сравнение товаров',
        $controller_description = 'Отображает товары, которые были добавлены к сравнению. В данном блоке товары можно исключить или перейти к сравнению';
    
    protected
        $action_var = 'cpmdo',
        $default_params = [
            'indexTemplate' => 'blocks/compare/compare.tpl',
            'listTemplate' => 'blocks/compare/items.tpl'
    ];
    
    /** @var CompareApi */
    public $api;
    
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'listTemplate' => new Type\Template([
                'description' => t('Шаблон списка')
            ])
        ]);
    }
       
    function init()
    {
        $this->api = CompareApi::currentCompare();
    }
    
    function actionAjaxGetItems()
    {
        $list = $this->api->getCompareList();
        $this->view->assign('list', $list);        
        return $this->result
                        ->addSection('total', $this->api->getCount())
                        ->setTemplate( $this->getParam('listTemplate') );
    }            
    
    function actionIndex()
    {
        // Если включено кэширование блоков, то необходимо передавать
        // сведения обо всех товарах в сравнении в виде json в массиве global,
        // чтобы обновлять на JS состояние кнопок "Добавить в сравнение"
        if (\Setup::$CACHE_BLOCK_ENABLED) {
            $product_ids = array_map('intval', array_values($this->api->getList()));
            $this->app->addJsVar('compareProducts', $product_ids);
        }

        $this->view->assign('list_html', $this->actionAjaxGetItems()->getHtml());
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
    
    function actionAjaxAdd()
    {
        $id = $this->url->post('id', TYPE_INTEGER);
        $this->result->setSuccess( $this->api->addProduct($id) );
        return $this->actionAjaxGetItems();
    }
    
    function actionAjaxRemove()
    {
        $id = $this->url->post('id', TYPE_INTEGER);
        return $this->result
                        ->setSuccess( $this->api->removeProduct($id) )
                        ->addSection('total', $this->api->getCount());
    }
    
    function actionAjaxRemoveAll()
    {
        return $this->result->setSuccess( $this->api->removeAll() );
    }
}
