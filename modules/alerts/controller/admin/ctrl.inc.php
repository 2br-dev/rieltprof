<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Controller\Admin;

use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Filter,
    \RS\Html\Table;
    
class Ctrl extends \RS\Controller\Admin\Crud
{
    
    function __construct()
    {
        $api = new \Alerts\Model\Api;
        parent::__construct($api);
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('В данном разделе вы можете включить необходимые уведомления на Email, SMS или ПК (Desktop приложение ReadyScript). Вы можете изменить шаблон любого уведомления. Для отправки SMS уведомлений должен быть подключен соответствующий пакет SMS услуг.'));
        $helper->setTopTitle(t('Уведомления'));
        
        if($this->url->isPost()){
            $config_items = $this->api->getList();
            foreach ($config_items as $item) {
                $item_id = $item['id'];
                foreach($item as $field){
                    $param_arr = $this->url->post($field->name, TYPE_ARRAY);
                    if (is_array($param_arr) && isset($param_arr[$item_id])) {
                        $item[$field->name] = $param_arr[$item_id];
                    }
                }
                $item->update();
            } 
            $this->result
                ->setSuccessText(t('Изменения успешно сохранены'))
                ->setSuccess(true);
        }
        
        $helper->viewAsAny();
        $this->view->assign([
            'cfg' => \RS\Config\Loader::byModule($this),
            'alerts' => $this->api->getList(),
            'tfolders' => \RS\Module\Item::getResourceFolders('templates')
        ]);
        $helper['form'] = $this->view->fetch('notice_list.tpl');

        $helper->setTopToolbar(null);
        $helper->setBottomToolbar($this->buttons(['apply']));
        
        return $helper;
    }
    
    function actionAjaxTestSms()
    {
        $config = \RS\Config\Loader::getSiteConfig();
        try{
            \Alerts\Model\SMS\Manager::send($config['admin_phone'], '%alerts%/test_sms.tpl', null, false);
            $this->result->addMessage(t('SMS-сообщение успешно отправлено'));
        }
        catch(\Exception $e){
            $this->result->addEMessage( t('Ошибка %0: %1', [$e->getCode(), $e->getMessage()]) );
        }
        return $this->result->setSuccess(true);
    }
    
}
