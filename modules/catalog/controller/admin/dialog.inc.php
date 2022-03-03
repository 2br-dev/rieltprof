<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Admin;
use RS\Site\Manager;

/**
* Контроллер обслуживает диалог выбора товаров.
* Все методы вызываются Ajax методом
*/
class Dialog extends \RS\Controller\Admin\Block
{
    protected $id;
    /**
     * @var \Catalog\Model\Dirapi $dirapi
     */
    protected $dirapi;
    /**
     * @var \Catalog\Model\Api $productapi
     */
    protected $productapi;
    protected $action_var = 'do';
        
    function init()
    {
        $this->id = $this->url->request('id', TYPE_INTEGER);
        $this->dirapi     = new \Catalog\Model\Dirapi();
        $this->productapi = new \Catalog\Model\Api();
    }
    
    /**
    * Возвращает содержимое диалогового окна при первом открытии диалога
    */
    function actionIndex()
    {
        $this->dirapi->clearFilter();
        $showVirtualDirs = $this->url->request('showVirtualDirs', TYPE_INTEGER, 0);
        if (!$showVirtualDirs){ //Если виртуальные категории надо скрыть
            $this->dirapi->setFilter([[
                'is_virtual' => 0,
                '|is_virtual' => null
            ]]);
        }
        $tree_list = $this->dirapi->getTreeList(0);
        $this->view->assign([
            'treeList' => $tree_list,
            'products' => $this->actionGetProducts(0),
            'hideGroupCheckbox' => $this->url->request('hideGroupCheckbox', TYPE_INTEGER, 0)
        ]);
        return $this->result
                    ->setHtml( $this->view->fetch('dialog/wrapper.tpl') )
                    ->getOutput();
    }
    
    function actionGetChildCategory()
    {
        $dirlist = $this->dirapi->getTreeList($this->id);
        $this->view->assign([
            'hideGroupCheckbox' => $this->url->request('hideGroupCheckbox', TYPE_INTEGER, 0),
            'dirlist' => $dirlist
        ]);
        return $this->result
                        ->setHtml( $this->view->fetch('dialog/tree_branch.tpl') )
                        ->getOutput();
    }
    
    function actionGetProducts($category = null)
    {
        $catid = $this->url->request('catid', TYPE_INTEGER, 0);
        $page = $this->url->request('page',TYPE_INTEGER, 1);
        $pageSize = $this->url->request('pageSize', TYPE_INTEGER, 20);
        $filter = $this->url->get('filter', TYPE_ARRAY, []);

        if ($category !== null) $catid = $category;
        if ($catid>0) {
            $cat_id = $this->dirapi->getChildsId($catid);
            $this->productapi->setFilter('dir', $cat_id, 'in');
        }

        foreach($filter as $key=>$val)
        if (!empty($val)) {
                switch ($key)
                {
                    case 'id': $this->productapi->setFilter($key, (int)trim($val));
                        break;
                    case 'title': $this->productapi->setFilter($key, (string)trim($val), '%like%');
                        break;
                    case 'barcode': $this->productapi->setFilter($key, trim((string)$val), '%like%');
                        break;
                    case 'sku': $this->productapi->setFilter($key, trim((string)$val), '%like%');
                }
            }


        if ($page<1) $page = 1;
        if ($pageSize<1) $pageSize = 1;

        $total         = $this->productapi->getListCount();
        $list          = $this->productapi->getList($page, $pageSize);
        $list          = $this->productapi->addProductsPhotos($list);
        $products_dirs = $this->productapi->getProductsDirs($list, true);
        
        $costApi = new \Catalog\Model\CostApi();
        if ( $default_cost_id = $this->getModuleConfig()->default_cost ) {
            $costApi->setOrder("id != '{$default_cost_id}'");
        }
        
        $this->view->assign([
            'list' => $list,
            'hideProductCheckbox' => $this->url->request('hideProductCheckbox', TYPE_INTEGER, 0),
            'products_dirs' => $products_dirs,
            'costtypes' => $costApi->getList(),
            'paginator' => [
                'total' => $total,
                'totalPages' => ceil($total/$pageSize),
                'page' => $page,
                'pageSize' => $pageSize
            ]
        ]);
        
        if ($category !== null) {
            return $this->view->fetch('dialog/products.tpl');
        } else {
            return $this->result
                        ->setHtml( $this->view->fetch('dialog/products.tpl') )
                        ->getOutput();
        }

    }
}

