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
* Блок-контроллер Поиск по статьям
*/
class SearchLine extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Поиск статей на сайте',
        $controller_description = 'Отображает форму для поиска статей по ключевым словам';

    protected
        $action_var = 'sldo',
        $default_params = [
            'searchLimit' => 5,
            'hideAutoComplete' => 0,
            'indexTemplate' => 'blocks/searchline/searchform.tpl',
            'showPreview' => 1,
            'imageWidth' => 62,
            'imageHeight' => 62,
            'imageResizeType' => 'xy',
    ];
        
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'showPreview' => new Type\Integer([
                'description' => t('Показывать краткий текст в подсказках?'),
                'checkboxview' => [1,0]
            ]),
            'imageWidth' => new Type\Integer([
                'description' => t('Ширина изображения в подсказках'),
                'maxLength' => 6
            ]),
            'imageHeight' => new Type\Integer([
                'description' => t('Высота изображения в подсказках'),
                'maxLength' => 6
            ]),
            'imageResizeType' => new Type\Varchar([
                'description' => t('Тип масштабирования изображения в подсказках'),
                'maxLength' => 4,
                'listFromArray' => [[
                    'xy' => 'xy',
                    'axy' => 'axy',
                    'cxy' => 'cxy',
                    'ctxy' => 'ctxy',
                ]]
            ]),
            'hideAutoComplete' => new Type\Integer([
                'description' => t('Отключить подсказку результатов поиска в выпадающем списке'),
                'checkboxView' => [1,0]
            ]),
            'searchLimit' => new Type\Integer([
                'description' => t('Количество результатов в выпадающем списке')
            ])
        ]);
    }   

    function actionIndex()
    {             
        $query = trim($this->url->get('query', TYPE_STRING));       
        if ($this->router->getCurrentRoute() && $this->router->getCurrentRoute()->getId() == 'article-front-previewlist' && !empty($query)) {
            $this->view->assign('query', $query);
        }
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
    
    function actionAjaxSearchItems()
    {
        $query = trim($this->url->request('term', TYPE_STRING));
        $result_json = [];

        if (!empty($query)){
            $api = new \Article\Model\Api();
            
            //Если идет поиск по названию
            $q = $api->queryObj();
            $q->select = 'A.*';
            
            $search = \Search\Model\SearchApi::currentEngine();
            $search->setQuery($query);
            $search->joinQuery($q); 
            
            $list = $api->getList(1, $this->getParam('searchLimit'));
            
            $shop_config = \RS\Config\Loader::byModule('shop');
            
            foreach($list as $article){
                
                $result_json[] = [
                    'value' => $article['title'],
                    'label' => preg_replace("%($query)%iu", '<b>$1</b>', $article['title']),
                    'preview' => $this->getParam('showPreview') ? preg_replace("%($query)%iu", '<b>$1</b>', $article->getPreview(200, false)) : null,
                    'image' => $article['image'] ? $article->__image->getUrl($this->getParam('imageWidth'), $this->getParam('imageHeight'), $this->getParam('imageResizeType')) : null,
                    'url' => $article->getUrl()
                ];
            }
        }
        
        $this->app->headers->addHeader('content-type', 'application/json');
        return json_encode($result_json);
    }
}