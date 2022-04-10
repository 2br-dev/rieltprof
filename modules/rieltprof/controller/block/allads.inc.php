<?php
/**
 * ReadyScript (http://readyscript.ru)
 *
 * @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
 * @license http://readyscript.ru/licenseAgreement/
 */

namespace Rieltprof\Controller\Block;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\DirApi;
use Catalog\Model\Orm\Dir;
use RS\Cache\Manager as CacheManager;
use RS\Controller\StandartBlock;
use RS\Debug\Action as DebugAction;
use RS\Debug\Tool as DebugTool;
use RS\Helper\Paginator;
use RS\Helper\Tools as HelperTools;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\Type;

/**
 * Контроллер - топ товаров из указанных категорий одним списком
 */
class AllAds extends StandartBlock
{
    protected static $controller_title = 'Все объявления';
    protected static $controller_description = 'Отображает все объявления';

    protected $default_params = [
        'indexTemplate' => '%rieltprof%/block/all_ads.tpl', //Должен быть задан у наследника
        'listTemplate'  => '%rieltprof%/block/ad_list.tpl',
        'pageSize' => 50,
    ];
    protected $page, $pageSize;

    /** @var ProductApi $api */
    public $api;

    function init()
    {
        $this->api = new ProductApi();
        $this->page = $this->url->get('p', TYPE_INTEGER, 1);
        $this->pageSize = $this->getParam('pageSize');
    }

    function actionIndex()
    {
        $this->view->assign([
            'ad_list_html' => $this->actionGetAdsList()->getHtml()
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }

    public function actionGetAdsList()
    {
        $this->api->setFilter('public', 1);
        $total = $this->api->getListCount();
        $paginator = new \RS\Helper\Paginator($this->page, $total, $this->pageSize);
        $products = $this->api->getList($this->page, $this->pageSize);
        $this->view->assign([
            'ads' => $products,
            'block_title' => $this->getParam('block_title'),
            'total' => $total,
            'pageSize' => $this->pageSize,
            'page' => $this->page,
            'paginator' => $paginator

        ]);
        return $this->result->setTemplate( $this->getParam('listTemplate') );
    }
}
