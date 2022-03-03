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
        'pageSize' => 100,
    ];
    protected $page;

    /** @var ProductApi $api */
    public $api;

    function init()
    {
        $this->api = new ProductApi();
    }

    function actionIndex()
    {
        $pageSize = $this->getParam('pageSize', null);

        $this->api->setFilter('public', 1);
        $total = $this->api->getListCount();
        $products = $this->api->getList();
        $this->view->assign([
            'ads' => $products,
            'block_title' => $this->getParam('block_title'),
            'total' => $total
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
