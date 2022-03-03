<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Orm;
use \RS\Orm\Type;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $topic Тема
 * @property integer $user_id Пользователь
 * @property string $dateof Дата отправки
 * @property string $message Сообщение
 * @property integer $processed Флаг прочтения
 * @property integer $is_admin Это администратор
 * @property integer $topic_id ID темы
 * --\--
 */
class Support extends \RS\Orm\OrmObject
{
    protected static
        $table = 'support';
    
    protected static $user_cache;
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'topic'   => new Type\Varchar([
                'description' => t('Тема'),
                'checker' => [[__CLASS__, 'checkTopic'], t('Укажите, пожалуйста, тему')],
                'visible' => false
            ]),
            'user_id' => new Type\Integer([
                'description' => t('Пользователь'),
                'visible' => false,
            ]),
            'dateof' => new Type\Datetime([
                'description' => t('Дата отправки'),
            ]),
            'message' => new Type\Text([
                'description' => t('Сообщение'),
                'checker' => ['chkEmpty', t('Не задан вопрос')]
            ]),
            'processed' => new Type\Integer([
                'maxLength'   => 1,
                'description' => t('Флаг прочтения'),
                'visible' => false
            ]),
            'is_admin' => new Type\Integer([
                'maxLength'   => 1,
                'description' => t('Это администратор'),
                'visible' => false
            ]),
            'topic_id' => new Type\Integer([
                'description' => t('ID темы'),
                'visible' => false
            ]),
        ]);
    }
    
    public static function checkTopic($_this, $value, $error)
    {
        if ($_this['topic_id'] == 0 && !$_this['topic']) {
            return $error;
        }
        return true;
    }
    
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
                $this['dateof'] = date('Y-m-d H:i:s');
                $this['processed'] = 0;
        }
        
        if ($this['topic_id'] == 0 && !empty($this['topic'])) {
            //Сздаем тему, при необходимости
            $topic = new \Support\Model\Orm\Topic();
            $topic['title'] = $this['topic'];
            $topic['user_id'] = $this['user_id'];
            $topic['updated'] = $this['dateof'];
            $topic['msgcount'] = 1;
            $topic['newcount'] = 0;
            $topic->insert();
            
            $this['topic_id'] = $topic['id'];
        }
    }
    
    function afterWrite($flag)
    {
        $this->updateTopicCounts();                                         
        if (!$this->is_admin) { //Отправка сообщения админу от пользователя
            $notice = new \Support\Model\Notice\Post();
            $notice->init($this);
            \Alerts\Model\Manager::send($notice); 
        }else{ //Отправка сообщения пользователю от админа
            $notice = new \Support\Model\Notice\User();
            $notice->init($this);
            $notice->setUser(new \Users\Model\Orm\User($this->getTopic()->user_id)); //Установка пользователя
            \Alerts\Model\Manager::send($notice); 
        } 
    }
    
    function updateTopicCounts()
    {
        //Обновляем счетчики у темы
        $q = new \RS\Orm\Request();
        //Общее количество
        $total_msg = $q->from($this)
                        ->where( ['topic_id' => $this['topic_id']])
                        ->count();
        
        //Новые для пользователей (которые написал администратор)
        $q = new \RS\Orm\Request();
        $new_msg = $q->from($this)
                    ->where([
                        'topic_id' => $this['topic_id'],
                        'is_admin' => '1',
                        'processed' => '0'
                    ])->count();
                    
        //Новые для администратора
        $q = new \RS\Orm\Request();
        $new_admin_msg = $q->from($this)
                    ->where([
                        'topic_id' => $this['topic_id'],
                        'is_admin' => '0',
                        'processed' => '0'
                    ])->count();
                    
        $q = new \RS\Orm\Request();
        $updated = $q->select("dateof")
                        ->from($this)
                        ->where( ['topic_id' => $this['topic_id']])
                        ->orderby("dateof DESC")
                        ->limit(1)
                        ->exec()
                        ->getOneField("dateof");
        
        $topic_obj = new Topic();
        $sql = "UPDATE ".$topic_obj->_getTable()." 
                  SET 
                    msgcount='{$total_msg}',
                    newcount='{$new_msg}',
                    newadmcount='{$new_admin_msg}',
                    updated = '{$updated}'
              WHERE id='{$this['topic_id']}'";
              
        \RS\Db\Adapter::sqlExec($sql);        
    }
    
    function delete($updateCounter = true)
    {
        if (empty($this['topic_id']) && !empty($this['id'])) $this->load($this['id']);
        if ($ret = parent::delete() && $updateCounter) {
            $this->updateTopicCounts();
        }
        return $ret;
    }
    
    function getTopic()
    {
        $topic = new Topic();
        $topic->load($this['topic_id']);
        return $topic;
    }
    
    function getUser()
    {
        if (!isset(self::$user_cache[$this['user_id']])) {
            self::$user_cache[$this['user_id']] = new \Users\Model\Orm\User($this['user_id']);
        }
        return self::$user_cache[$this['user_id']]; 
    }
    
}

