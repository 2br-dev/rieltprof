<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Controller\Front;

use RS\Application\Application;

class Support extends \RS\Controller\AuthorizedFront
{
    protected
        $api,
        $topic_api;


    function init()
    {
        $this->api = new \Support\Model\Api();
        $this->topic_api = new \Support\Model\TopicApi();
        
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Личный кабинет'));
    }
    
    function actionIndex()
    {        
        $this->app->breadcrumbs->addBreadCrumb(t('Поддержка'));
        $support_item = $this->api->getNewElement();            
                
        if ($this->url->isPost()) {
            $addpost = [
                'user_id' => $this->user['id'], 
                'is_admin' => 0
            ];
            if ($support_item->save(null, $addpost)) {
                //Тема создана, перходим в тему
                Application::getInstance()->redirect( $this->router->getUrl('support-front-support', ['Act' => 'viewTopic', 'id' => $support_item['topic_id']]) );
            }
        }

        $this->topic_api->setOrder('updated DESC');
        $this->topic_api->setFilter('user_id', $this->user['id']);
        $list = $this->topic_api->getList();            
        
        if (!$this->url->isPost() && count($list)>0) {
            $support_item['topic_id'] = $list[0]['id'];
        }
        
        $this->view->assign([
            'supp' => $support_item, 
            'list' => $list,
        ]);
        
        return $this->result->setTemplate( 'topics.tpl' );        
    }
    
    function actionViewTopic()
    {
        $topic_id = $this->url->get('id', TYPE_INTEGER);
        $topic = $this->topic_api->getOneItem($topic_id);
        
        if (!$topic || $topic['user_id'] != $this->user['id']) {
            $this->e404(t('Такой темы не существует'));
        }
        
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Поддержка'), $this->router->getUrl('support-front-support'))
            ->addBreadCrumb($topic['title']);
        
        $support_item = $this->api->getNewElement();
        $support_item->escapeAll(true);        
        
        if ($this->url->isPost()) {
            $addpost = [
                'user_id' => $this->user['id'], 
                'is_admin' => 0,
                'topic_id' => $topic_id
            ];
            
            if ($support_item->save(null, $addpost)) {
                //Сообщение сохранено
                Application::getInstance()->redirect($this->url->selfUri());
            }
        }        
        
        $this->api->setFilter('topic_id', $topic_id);
        $this->api->setOrder('dateof');
        $list = $this->api->getList();
        $this->api->markViewedList($topic_id, true);
        
        $this->view->assign([
            'supp' => $support_item,
            'topic' => $topic,
            'list' => $list
        ]);
        return $this->result->setTemplate( 'support.tpl' );        
    }
    
    
    function actionDelTopic()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        $this->topic_api->setFilter('id', $id);
        $this->topic_api->setFilter('user_id', $this->user['id']);
        $topic = $this->topic_api->getFirst();
        
        if ($topic) {
            $topic->delete();
        }
        
        return $this->result
                        ->setSuccess(true)
                        ->setNoAjaxRedirect($this->router->getUrl('support-front-support'));
    }    
    
}