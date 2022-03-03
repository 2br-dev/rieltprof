<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Admin\Widget;

class AuthLog extends \RS\Controller\Admin\Widget
{
    protected
        $info_title = 'Безопасность',
        $info_description = 'Список последних авторизаций администраторов',
        $title = 'Недавние авторизации';
    
    function actionIndex()
    {
        $page = $this->myRequest('p', TYPE_INTEGER, 1);
        $pageSize = 5;
        $log_api = new \Users\Model\LogApi();
        $log_api->setFilter('class', 'Users\Model\Logtype\AdminAuth');
        $total = $log_api->getListCount();
        
        $list = $log_api->getLogItems('Users\Model\Logtype\AdminAuth', $pageSize, ($page-1)*$pageSize, false);
        
        $paginator = new \RS\Helper\Paginator($page, $total, $pageSize, 'main.admin', ['mod_controller' => $this->getUrlName()]);
        
        
        $this->view->assign([
            'list' => $list,
            'paginator' => $paginator
        ]);
        
        return $this->result->setTemplate('widget/authlog.tpl');
    }
    
}

