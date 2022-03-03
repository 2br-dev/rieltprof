<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Controller\Admin;

use RS\Application\Application;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Table;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

class SupportCtrl extends \RS\Controller\Admin\Crud
{
    protected
        $api;
        
    function __construct()
    {
        parent::__construct(new \Support\Model\Api);
    }
    
    function helperIndex()
    {
        $topic_id = $this->url->request('id', TYPE_INTEGER);
        $topic = new \Support\Model\Orm\Topic($topic_id);
        $this->api->setFilter('topic_id', $topic_id);
        

        $helper = parent::helperIndex();
        $helper->setTopTitle($topic->title);             
        
        $helper->setTopToolbar(null);
        $helper->setListFunction('getReverseList');
        $this->api->setOrder('id desc');
        
        $helper->setTable(new Table\Element([
            'Columns' => [
                new \RS\Html\Table\Type\Text('dateof', t('Дата'), ['Sortable' => SORTABLE_BOTH, 'ThAttr' => ['width' => 150]]),
                new \RS\Html\Table\Type\Usertpl('is_admin', '', '%support%/user_type_cell.tpl', ['ThAttr' => ['width' => 15]]),
                new \RS\Html\Table\Type\Text('message', t('Сообщение')),
                new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                                'attr' => ['@data-id' => '@id']
                            ]
                        ),
                        new TableType\Action\DropDown([
                                [
                                    'title' => t('удалить'),
                                    'attr' => [
                                        '@href' => $this->router->getAdminPattern('del', [':id' => '~field~']),
                                        'class' => 'crud-remove-one'
                                    ]
                                ],
                            ]
                        ),
                    ]
                ),
            ]]
        ));
        
        $helper->setBottomToolbar(null);
        return $helper;
    } 
    
    function actionIndex()
    {
        $helper = $this->getHelper();
        
        $this->app->addCss($this->mod_css.'/support.css', null, BP_ROOT);
        
        $topic_id = $this->url->request('id', TYPE_INTEGER);
        $topic = new \Support\Model\Orm\Topic($topic_id);
        
        
        if($this->url->isPost()){

            // Помечаем как прочитанные
            if (Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE) === false ) 
            {
                $this->api->markViewedList($topic_id, false);

                $msg = trim($this->url->post('msg', TYPE_STRING));
                if(empty($msg)){
                    Application::getInstance()->redirect($this->url->selfUri());
                }
                $support_message = new \Support\Model\Orm\Support;
                $support_message->message = \RS\Helper\Tools::toEntityString($msg);
                $support_message->user_id = \RS\Application\Auth::getCurrentUser()->id;
                $support_message->dateof  = date('Y-m-d H:i:s');
                $support_message->topic_id  = $topic_id;
                $support_message->is_admin = 1;
                $support_message->insert();
            }
            Application::getInstance()->redirect($this->url->selfUri());
        }
       
        $this->view->assign('topic', $topic);
        $this->view->assign('elements', $helper->active());
        return $this->result->setTemplate('adminview.tpl');
    }
}
