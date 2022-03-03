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
 * Тема, группирующая сообщения в поддержку
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Тема
 * @property integer $user_id Пользователь
 * @property string $updated Дата обновления
 * @property integer $msgcount Всего сообщений
 * @property integer $newcount Новых сообщений
 * @property integer $newadmcount Новых для администратора
 * @property string $_first_message_ Сообщение
 * --\--
 */
class Topic extends \RS\Orm\OrmObject
{
    protected static
        $table = 'support_topic';
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title'   => new Type\Varchar([
                'description' => t('Тема'),
                'checker' => ['chkEmpty', t('Не указана тема')]
            ]),
            'user_id' => new Type\User([
                'description' => t('Пользователь'),
                'checker' => ['chkEmpty', t('Не выбран пользователь')]
            ]),
            'updated' => new Type\Datetime([
                'visible' => false,
                'description' => t('Дата обновления'),
            ]),
            'msgcount' => new Type\Integer([
                'visible' => false,
                'description' => t('Всего сообщений'),
            ]),
            'newcount' => new Type\Integer([
                'visible' => false,
                'description' => t('Новых сообщений'),
            ]),
            'newadmcount' => new Type\Integer([
                'visible' => false,
                'description' => t('Новых для администратора'),
            ]),
            '_first_message_' => new Type\Text([
                'description' => t('Сообщение'),
                'runtime' => true,
            ])
        ]);
    }
    
    
    function afterWrite($flag)
    {
        if($this['_first_message_']){
            $support_message = new \Support\Model\Orm\Support;
            $support_message->topic_id  = $this->id;
            $support_message->user_id = $this->user_id;
            $support_message->message = $this['_first_message_'];
            $support_message->dateof  = date('Y-m-d H:i:s');
            
            if($this['_admin_creation_']){
                $support_message->user_id = \RS\Application\Auth::getCurrentUser()->id;
                $support_message->is_admin = 1;
            }
            
            $support_message->insert();
        }
    }
    
    
    
    function delete()
    {
        $q = new \RS\Orm\Request();
        $q->delete()
            ->from(new Support())
            ->where( ['topic_id' => $this['id']])
            ->exec();
        
        return parent::delete();
    }

    /**
     * Возвращает пользователя-автора темы обращения
     *
     * @return \Users\Model\Orm\User
     */
    function getUser()
    {
        return new \Users\Model\Orm\User($this['user_id']);
    }

    /**
     * Возвращает первое сообщение из переписки
     *
     * @return Support
     */
    function getFirstMessage()
    {
        $api = new \Support\Model\Api();
        $api->setFilter('topic_id', $this['id']);
        $api->setOrder('dateof');
        return $api->getFirst();
    }
}