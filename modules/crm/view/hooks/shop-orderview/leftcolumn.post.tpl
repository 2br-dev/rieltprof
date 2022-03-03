{moduleinsert name="Crm\Controller\Admin\Block\CrmBlock" tabs=[
'deal' => [
    'link_id' => $order.id,
    'link_type' => 'crm-linktypeorder'
],
'interaction' => [
    'link_id' => $order.id,
    'link_type' => 'crm-linktypeorder'
],
'userInteraction' => [
    'link_id' => $order->getUser()->id,
    'link_type' => 'crm-linktypeuser'
],
'task' => [
    'link_id' => $order.id,
    'link_type' => 'crm-linktypeorder'
]]}