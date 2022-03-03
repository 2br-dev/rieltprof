<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Controller\Admin\Widget;

class NewTopics extends \RS\Controller\Admin\Widget
{
    protected
        $page_size = 5,
        $info_title = 'Новые сообщения в поддержку',
        $info_description = 'Отображает темы, в которых появились новые сообщения';
    
    function actionIndex()
    {
        $page = $this->myRequest('p', TYPE_INTEGER, 1);
        $api = new \Support\Model\TopicApi();
        $api->setFilter('newadmcount', 0, '>');
        $total = $api->getListCount();
        $topics = $api->getList($page, $this->page_size, 'updated DESC');
        $paginator = new \RS\Helper\Paginator($page, $total, $this->page_size, 'main.admin', ['mod_controller' => $this->getUrlName()]);
        $this->view->assign([
            'topics' => $topics,
            'paginator' => $paginator
        ]);
        return $this->result->setTemplate( 'widget/newtopics.tpl' );
    }
}
?>
