<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Article\Controller\Block;

use RS\Controller\StandartBlock;
use RS\Orm\Type;

/**
 * Блок-контроллер Вывод товаров прикреплённых к статье
 */
class ArticleProducts extends StandartBlock
{
    protected static $controller_title = 'Список товаров прикреплённых к статье';
    protected static $controller_description = 'Отображает N товаров прикреплённых к статье';

    protected $default_params = [
        'indexTemplate' => 'blocks/article/products.tpl',
        'show_only_available' => 0,
    ];

    public $api;

    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'show_only_available' => new Type\Integer([
                'description' => t('Отображать только те товары, что есть в наличии'),
                'checkboxView' => [1, 0]
            ]),
            'article_id' => new Type\Integer([
                'description' => t('Номер статьи'),
            ]),
        ]);
    }

    function actionIndex()
    {
        if ($this->getParam('article_id')) {
            $article_id = $this->getParam('article_id');
        } else {
            $article_id = $this->router->getCurrentRoute()->getExtra('article_id'); //Получаем id товаров
        }

        if (!$article_id) {
            return '';
        }

        $this->api = new \Catalog\Model\Api(); //Апи товаров

        $template = $this->getParam('indexTemplate');
        if ($this->isViewCacheExpired($article_id, $template)) {
            $api = new \Article\Model\Api();

            /** @var \Article\Model\Orm\Article $article */
            $article = $api->getById($article_id);
            if ($article) {
                $products = $article->getAttachedProducts($this->getParam('show_only_available'));
                $this->view->assign([
                    'products' => $products,
                ]);
            }
        }
        return $this->result->setTemplate($template);
    }
}
