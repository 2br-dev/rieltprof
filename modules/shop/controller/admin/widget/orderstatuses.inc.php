<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin\Widget;
use \Shop\Model\Orm\UserStatus;

/**
* Виджет - статусы заказов на круговой диаграмме
*/
class OrderStatuses extends \RS\Controller\Admin\Widget
{
    protected
        $orderapi,
        $info_title = 'Диаграмма статусов заказов',
        $info_description = 'Позволяет отслеживать статистику по выполнению заказов';
    
    
    function actionIndex()
    {
        $api = new \Shop\Model\OrderApi();
        $total = $api->getListCount();
        $counts = $api->getStatusCounts();

        $finished = 0;
        $cancelled = 0;
        
        $status_api = new \Shop\Model\UserStatusApi();
        $statuses = $status_api->getAssocList('id');


        $success_status = $status_api->getStatusesIdByType(UserStatus::STATUS_SUCCESS);
        foreach($success_status as $status_id) {
            $finished += isset($counts[$status_id]) ? $counts[$status_id] : 0;
        }

        $cancelled_status = $status_api->getStatusesIdByType(UserStatus::STATUS_CANCELLED);
        foreach($cancelled_status as $cancelled_id) {
            $cancelled += isset($counts[$cancelled_id]) ? $counts[$cancelled_id] : 0;
        }
        
        $json_data = [];
        foreach($counts as $id => $count) {
            $json_data[] = [
                'label' => (isset($statuses[$id]) ? $statuses[$id]['title'] : t('статус удален'))."($count)",
                'data' => (int)$count,
                'color' => isset($statuses[$id]) ? $statuses[$id]['bgcolor'] : ''
            ];

        }
        
        $this->view->assign([
            'total' => $total,
            'counts' => $counts,
            'statuses' => $statuses,
            'inwork' => $total - $finished - $cancelled,
            'finished' => $finished,
            'json_data' => json_encode($json_data)
        ]);
        
        return $this->view->fetch('widget/orderstatuses.tpl');
    }
}