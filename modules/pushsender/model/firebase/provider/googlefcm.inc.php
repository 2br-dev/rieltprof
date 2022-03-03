<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\Firebase\Provider;
use PushSender\Model\Firebase\Push\RsPushNotice;
use \PushSender\Model\PushTokenApi,
    \PushSender\Model\AbstractProvider,
    \PushSender\Model\Exception,
    RS\Event\Manager;

/**
* Осуществляет отправку Push уведомлений напрямую через Firebase Cloud Messenging.
* Применяется для мобильных приложений
*/
class GoogleFCM extends AbstractProvider
{
    const
        API_URL = 'https://fcm.googleapis.com/fcm/send'; //API
    
    protected
        $last_error,
        $last_response,
        $targets = [], //Массив из устройств для отправки
        $api_url;
        
    function __construct()
    {
        parent::__construct();
        $this->setApiUrl(self::API_URL);
    }
        
    /**
    * Устанвливает URL для API запросов
    * 
    * @param mixed $url
    */
    function setApiUrl($url)
    {
        $this->api_url = $url;
    }
    
    /**
    * Возвращает URL для API запросов
    * 
    * @return string
    */
    function getApiUrl()
    {
        return $this->api_url;
    }
    
    /**
    * Заполняет поля получателя уведомлений
    * 
    * @param array $tokens - Массив с токенами или один токен со сведениями
    * @return array
    */
    protected function fillTarget($tokens)
    {                                                    
        foreach ($tokens as $token){
            switch (mb_strtolower($token['platform'])){
                case 'ios':
                    $this->targets['ios'][] = $token['push_token'];
                    break; 
                case 'android':
                    $this->targets['android'][] = $token['push_token'];
                    break;
                case 'windows':
                    $this->targets['windows'][] = $token['push_token'];
                    break;
            }
        }                        
    }
    
    /**
    * Заполняет сведения уведомления
    * 
    * @param string $platform платформа для которой отправляются уведомления
    * @param array $data массив с параметрами, которые пойдут в запрос к API
    * @param \PushSender\Model\Firebase\Push\RsPushNotice $push
    * @return array
    */
    protected function fillNotification($platform, $data, $push)
    {
        $push_body = $push->getPushBody();
        $push_title = $push->getPushTitle();
        $push_click_action = $push->getPushClickAction();

        $event = Manager::fire('pushsender.fillnotification.googlefcm', [
            'push' => $push,
            'push_body' => $push_body,
            'push_title' => $push_title,
            'push_click_action' => $push_click_action,
        ]);

        list($push, $push_body, $push_title, $push_click_action) = $event->extract();

        $data['notification']['sound'] = 'default';
        $data['notification']['body']  = $push_body;

        if ($platform == 'ios'){
            $data['notification']['title'] = $push_title;
        } else {
            $data['data']['title']   = $push_title;
            $data['data']['body'] = $push_body;
        }

        if ($push_click_action) {
            $data['notification']['click_action'] = $push_click_action;
        }

        return $data;
    }
    
    /**
    * Заполняет секцию настроек
    * 
    * @param string $platform платформа для которой отправляются уведомления
    * @param array $data массив с параметрами, которые пойдут в запрос к API
    * @param \PushSender\Model\Firebase\Push\RsPushNotice $push
    * @return array
    */
    protected function fillOptions($platform, $data, $push)
    {
        if ($platform == 'ios'){
           $data['aps']['content_available'] = 1; //Для старта приложения из заднего фона (для iOS)
           $data['aps']['sound']             = "default";//(для iOS) 
        }
        
        if (!empty($data['data'])){
            $data['data'] += $push->getPushData(); 
        }else{
            $data['data'] = $push->getPushData();
        }
        $data['data']['notId'] = $data['notId'] = time(); //Уникальный id генерируемого сообщения
        $data['priority'] = "high";  //Приоритет для некоторых случаев неполучения уведомлений

        $this->writeLog(t('Тело PUSH уведомления:\n\r %0', [var_export($data, true)]));
        
        return $data;
    }
    
    /**
    * Возвращает ключ сервера Google FireBase Cloud Messaging
    *
    * @throws Exception
    * @return string
    */
    protected function getServerKey()
    {
       $config = \RS\Config\Loader::byModule($this); 
       if (!$config['googlefcm_server_key']){
           throw new Exception(t('Не указан серверный ключ Google Firebase Cloud Messaging'));
       }
       return $config['googlefcm_server_key'];
    }
    
    /**
    * Выполняет отправку Push уведомления 
    * 
    * @param RsPushNotice $push
    * @return bool
    */
    function transfer($push)
    {
        $this->writeLog(t('Начало отправки уведомления %0 через провайдер %1', [$push->getId(), get_class($this)]));
            
        $this->last_error = null;
        $this->last_response = null;
        
        
        try {                                
            $server_key = $this->getServerKey();     
            
            
            $data = [];
            if (in_array('PushSender\Model\InterfaceDirectPushTokensNotice', class_implements( $push ))){
                $tokens = $push->getRecipientPushTokens();
                //Соберём направления на которые нужно отправить уведомления  
                $this->fillTarget($tokens);
            }else{
                //Получим пользователей, для которых отправляем
                $user_ids = PushTokenApi::filterUserIds($push->getRecipientUserIds(), 
                                                    $push->getId(), 
                                                    $push->getAppId()); 
                $tokens = PushTokenApi::getPushTokensByUserIds($user_ids, $push->getAppId());
                //Соберём направления на которые нужно отправить уведомления  
                foreach ($tokens as $token_id_list){
                    $this->fillTarget($token_id_list);
                }
            }
                                                
            if ($tokens){
                //Сообщения для iOS
                if (isset($this->targets['ios'])){ //Если есть цели для ios
                   $data['registration_ids'] = (array)$this->targets['ios'];
                   $this->sendQuery($server_key, 'ios', $push, $data);  //Отправка запроса  
                }
                
                //Сообщения для Android
                if (isset($this->targets['android'])){ //Если есть цели для ios
                   $data['registration_ids'] = (array)$this->targets['android'];
                   $this->sendQuery($server_key, 'android', $push, $data);  //Отправка запроса  
                }
                
                //Сообщения для Windows Phone
                if (isset($this->targets['windows'])){ //Если есть цели для ios
                   $data['registration_ids'] = (array)$this->targets['android'];
                   $this->sendQuery($server_key, 'android', $push, $data);  //Отправка запроса  
                }  
            } else {
                throw new Exception(t('Нет ни одного получателя для push уведомлений'));
            }
            
        } catch(\Exception $e) {
            $this->last_error = $e->getMessage();
            
            $this->writeLog(t('Отправка отклонена по причине: %0', [$this->last_error]));
                
            return false;
        }
        
        return true;
    }
    
    /**
    * Отправка запроса в Google Firebase Google Cloud
    * 
    * @param string $server_key - ключ сервера от ЛК в Google FCM
    * @param string $platform - платформа для которой отправляются уведомления
    * @param \PushSender\Model\Firebase\Push\RsPushNotice $push - объект PUSH уведомления
    * @param array $data - массив данных
    */
    function sendQuery($server_key, $platform, $push, $data)    
    {
        //Заполняем данные
        $data = $this->fillNotification($platform, $data, $push);
        $data = $this->fillOptions($platform, $data, $push);
        
        //Формируем запрос на отправку уведомления
        $context = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json;charset=utf-8\r\n".
                "Authorization: key={$server_key}\r\n",
                'content' => json_encode($data),
                'timeout' => 10
            ]
        ];

        $this->writeLog(t('Тело запроса к серверу:\n\r %0', [var_export($context, true)]));
        
        $result = file_get_contents($this->api_url, null, stream_context_create($context));
        if ($result === false) {
            $this->writeLog(t('Не удалось соединиться с сервером Firebase'));
        } else {
            $response = json_decode($result, true);
        }
        
        $this->writeLog(t('Получен ответ от API: %0', [$result]));
        $this->last_response = $response;
    }
    
    /**
    * Возвращает последний ответ от сервера после отправки уведомления
    * 
    * @return array
    */
    function getResponse()
    {
        return $this->last_response;
    }
    
    /**
    * Возвращает ошибку отправки уведомления. Возникает в случае, 
    * если не было попытки отправки, например, нет получателей.
    * 
    * @return string
    */
    function getError()
    {
        return $this->last_error;
    }
}
