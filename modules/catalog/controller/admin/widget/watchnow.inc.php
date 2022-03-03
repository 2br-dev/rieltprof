<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin\Widget;

class WatchNow extends \RS\Controller\Admin\Widget
{
    const
        LOG_TYPE_CLASS = 'Catalog\Model\Logtype\ShowProduct';
        
    protected
        $info_title = 'Что смотрят на сайте',
        $info_description = 'Список товаров, которые пользователи смотрят прямо сейчас',
        
        $title = 'Что смотрят сейчас на сайте';
    
    function actionIndex()
    {
        $log_api = new \Users\Model\LogApi();
        $offset = $this->url->request('offset', TYPE_INTEGER);
        $total = $log_api->getLogItemsCount(self::LOG_TYPE_CLASS, false, \RS\Site\Manager::getSiteId());
        $list = $log_api->getLogItems(self::LOG_TYPE_CLASS, 1, $offset, false, \RS\Site\Manager::getSiteId());
        
        //Массив для Json'а
        $data = [];
        foreach($list as $n => $event) {
            $product = $event->getObject();
            $user = $event->getUser();
            $user_name = ($user['id'] > 0) ? "{$user['login']} ({$user['name']} {$user['surname']})" : t('Гость');
            $user_href = ($user['id'] > 0) ? $this->router->getAdminUrl('edit', ['id' => $user['id']], 'users-ctrl') : false;
            $path = $product->getItemPathLine($product['maindir']);
            
            $path_line = [];
            foreach($path as $itempath) {
                $path_line[] = $itempath['name'];
            }
            $path_line = implode(' > ', $path_line);
            $path_href = $this->router->getAdminUrl(false, ['dir' => $product['maindir']], 'catalog-ctrl');
            
            $eventTime = strtotime($event->getEventDate());
            $item = [
                'id' => $product['id'],
                'editUrl' => $this->router->getAdminUrl('edit', ['id' => $product['id']], 'catalog-ctrl'),
                'eventDate' => date(\RS\Helper\Tools::dateExtend('%k, H:i', $eventTime), $eventTime),
                'user' => ['href' => $user_href, 'name' => $user_name],
                'path' => ['href' => $path_href, 'line' => $path_line],
                'product' => $product
            ];
            
            $data[$n] = $item;
        }
        
        $this->view->assign([
            'list' => $data,
            'offset' => $offset,
            'total' => $total
        ]);
        
        return $this->result->setTemplate('widget/watchnow.tpl');
    }
}

