<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model;

/**
* API для работы с токенами push уведомлений
*/
class PushTokenApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\PushToken(), [
            'defaultOrder' => 'dateofcreate DESC'
        ]);
    }

    /**
     * Регистрирует token пользователя в локальной базе
     *
     * @param string $push_token - Токен устройства-получателя Push уведомлений
     * @param string $app - Идентификатор приложения
     * @param integer $user_id - ID пользователя
     * @param array $device - Массив сведений об устройстве
     * @param boolean $update_user_id - Флаг отвечающий за принудительную перезапись id пользователя
     * @throws \RS\Orm\Exception
     * @return Orm\PushToken
     * @throws \RS\Orm\Exception
     */
    public static function registerUserToken($push_token, $app, $user_id = null, $device = [], $update_user_id = null)
    {
        //Найдем предыдущую запись если есть
        $where['app'] = $app;  
        if (isset($device['uuid']) && !empty($device['uuid'])){ //найдём по устройству
            $where['uuid'] = $device['uuid']; 
        }else{
            $where['user_id'] = $user_id; 
        }
        
        $token = \RS\Orm\Request::make()
                ->from(new Orm\PushToken())
                ->where($where)
                ->object(); 
        if (!$token){
            $token = new Orm\PushToken();
        }
        
        if (!$update_user_id && (is_null($token['user_id']) && $user_id)){   
            $token['user_id'] = $user_id;    
        }else{
            $token['user_id'] = $user_id;
        }
        
        $token['push_token']   = $push_token;
        $token['app']          = $app;
        $token['dateofcreate'] = date('c');
        $token['ip']           = $_SERVER['REMOTE_ADDR'];
        if (!empty($device)){
            $token->getFromArray($device);
        }
        
        if (!$token['id']){
            $token->insert();
        }else{
            $token->update();
        }
        
        return $token;
    }
    
    /**
    * Возвращает список push токенов для списка пользователей.
    * Если для какого либо пользователя нет токена, то данный пользователь игнорируется.
    * 
    * @param array $user_ids
    * @param string $app
    * @return array
    */
    public static function getPushTokensByUserIds($user_ids, $app)
    {
        if ($user_ids) {
            $user_ids = (array)$user_ids;
            
            return \RS\Orm\Request::make()
                ->from(new Orm\PushToken())
                ->where([
                    'app' => $app
                ])
                ->whereIn('user_id', $user_ids)
                ->exec()
                ->fetchSelected('user_id', null, true);
        }
        return [];
    }
    
    /**
    * Исключает из списка user_ids пользователей, которые запретили получение данного уведомления
    * 
    * @param array $user_ids список ID пользователей
    * @param string $push_id ID уведомления
    * @param string $app_id ID приложения
    * @return array
    */
    public static function filterUserIds($user_ids, $push_id, $app_id)
    {
        if (!$user_ids) return [];
        $user_ids = (array)$user_ids;        
        
        $locked = \RS\Orm\Request::make()
            ->from(new Orm\PushLock())
            ->whereIn('user_id', $user_ids)
            ->where([
                'site_id' => \RS\site\Manager::getSiteId(),
                'app' => $app_id
            ])
            ->where("(push_class='#push_id' OR push_class='".Orm\PushLock::PUSH_CLASS_ALL."')")
            ->exec()
            ->fetchSelected(null, 'user_id');
        
        return array_diff($user_ids, $locked);
    }

    /**
     * Отправка PUSH уведомлений на телефоны пользователей, по PUSH токенам
     *
     * @param \PushSender\Model\Orm\PushTokenMessage $push_message - объект cообщение PUSH уведомления
     * @param array $ids - массив c id получателей
     * @param $offset - индекс первого отправляемого элемента
     * @param int $limit - количество сообщени, которое отправляется за 1 шаг
     *
     * @return bool|int
     */
    function sendPushMessageToUsers(\PushSender\Model\Orm\PushTokenMessage $push_message, $ids, $offset, $limit = 100)
    {                                                          
        $ids_chunk = array_slice($ids, $offset, $limit);

        if ($ids_chunk) {
            $push = new \MobileSiteApp\Model\Push\MessageToUsers();
            $push->init($push_message, $ids_chunk);
            $push->send();
        }

        $next_offset = $offset + $limit;
        if ($next_offset > count($ids) -1) {
            return true;
        } else {
            return $next_offset;
        }
    }
}