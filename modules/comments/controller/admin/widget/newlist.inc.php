<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Controller\Admin\Widget;

class Newlist extends \RS\Controller\Admin\Widget
{
    protected
        $info_title = 'Комментарии',
        $info_description = 'Отображает самые свежие комментарии, оставленные во всех разделах сайта',
        $title = 'Последние комментарии',
        $pageSize;
        
    function __construct($param = [])
    {
        parent::__construct($param + ['prefix_get' => 'wnl']);
    }
    
    function init()
    {   
        $config = \RS\Config\Loader::byModule($this);
        $this->pageSize = $config['widget_newlist_pagesize']; //Количество комментариев на странице
    }
    
    function actionIndex()
    {
        $api = new \Comments\Model\Api();
        $page = $this->url->request('p', TYPE_INTEGER, 1);
        
        $total = \RS\Cache\Manager::obj()
            ->watchTables($api->getElement()) //Кэш до изменения таблиц
            ->request([$api, 'getListCount']);
        
        $list = \RS\Cache\Manager::obj()
            ->watchTables($api->getElement()) //Кэш до изменения таблиц
            ->request([$api, 'getList'], $page, $this->pageSize);

        $paginator = new \RS\Helper\Paginator($page, $total, $this->pageSize, 'main.admin', ['mod_controller' => $this->getUrlName()]);
    
        $this->view->assign([
            'time' => time(),
            'day_before_time_int' => time() - 24*60*60,
            'list' => $list,
            'paginator' => $paginator
        ], $list);
            
        return $this->result->setTemplate('widget/newlist.tpl');
    }
    
}

