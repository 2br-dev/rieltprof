<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model;

class Api extends \RS\Module\AbstractModel\EntityList
{
        
    function __construct()
    {
        parent::__construct(new \Support\Model\Orm\Support, 
        [
            'defaultOrder' => 'dateof DESC',
            'multisite' => true
        ]);
    }
        
    function addAdminAnswer($userid, $message)
    {
        if ($this->noWriteRights()) return false;
        
		$support = new $this->obj();
		$support['user_id'] = $userid;
		$support['dateof'] = date('Y-m-d H:i:s');
		$support['message'] = $message;
		$support['processed'] = 1;
		$support['is_admin'] = 1;		
		$return = $support->insert();
		return $return;
    }
    
    function markAnswered($userid)
    {
        $sql = "UPDATE ".$this->obj_instance->_getTable()." SET processed=1 WHERE user_id='{$userid}'";
        \RS\Db\Adapter::sqlExec($sql);
    }
    
    /**
    * Помечает сообщения прочитанными.
    */
    function markViewedList($topic_id, $admin_messages)
    {
        $is_admin = (int)$admin_messages;
        $a = $this->defAlias();
        $sql = "UPDATE ".$this->obj_instance->_getTable()." as $a SET $a.processed = 1 WHERE is_admin='$is_admin' AND topic_id='$topic_id'";
        \RS\Db\Adapter::sqlExec($sql);
            
        $field = $is_admin ? 'newcount' : 'newadmcount';
        //Обновляем счетчики
        $topic = new \Support\Model\Orm\Topic();
        $sql = "UPDATE ".$topic->_getTable()." ST 
                    SET ST.$field = (SELECT COUNT(*) FROM ".$this->obj_instance->_getTable()." S WHERE S.topic_id=ST.id AND S.processed=0 AND is_admin='$is_admin') 
                    WHERE ST.`id`='".$topic_id."'";
        \RS\Db\Adapter::sqlExec($sql);
    }
    
    function getReverseList($page = null, $page_size = null, $order = null){
        $list = $this->getList($page, $page_size, $order);
        return array_reverse($list);
    }
    
    function getNewMessageCount($user_id)
    {
        $q = new \RS\Orm\Request();
        $sum = $q->select('SUM(newcount) as sum')
            ->from(new \Support\Model\Orm\Topic())
            ->where( ['user_id' => $user_id])
            ->exec()
            ->getOneField('sum', 0);
        
        return $sum;
    }
}

