<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Controller\Block;

class NewMessages extends \RS\Controller\Block
{
    protected static
        $controller_title = 'Показ новых сообщений в поддержке',
        $controller_description = 'Показ новых сообщений в поддержке';
        
    protected
        $default_params = [
            'indexTemplate' => 'blocks/new_messages.tpl', //Должен быть задан у наследника
    ];
    
    function actionIndex()
    {
        $api = new \Support\Model\Api;
        $user_id = \RS\Application\Auth::getCurrentUser()->id;
        $this->view->assign('new_count', $api->getNewMessageCount($user_id));
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}