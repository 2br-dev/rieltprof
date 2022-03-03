<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\Firebase\Provider;
use \PushSender\Model\Firebase\Push\RsPushNotice,
    \PushSender\Model\PushTokenApi,
    \PushSender\Model\AbstractProvider,
    \PushSender\Model\Exception;

/**
* Осуществляет отправку Push уведомлений через ReadyScript в Firebase Cloud Messenging.
* Применяется для мобильных приложений, опубликованных от имени ReadyScript
*/
class RsProvider extends AbstractProvider
{
    protected
        $last_error,
        $last_response,
        $api_url;
        
    function __construct()
    {
        parent::__construct();
        $this->setApiUrl(\Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_API_SERVER_DOMAIN.'/api/methods/push.sendfirebase');
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
    * @param array $data массив с параметрами, которые пойдут в запрос к API
    * @param \PushSender\Model\Firebase\Push\RsPushNotice $push
    * @return array
    */
    protected function fillTarget($data, $push)
    {
        $user_ids = PushTokenApi::filterUserIds($push->getRecipientUserIds(), 
                                                $push->getId(), 
                                                $push->getAppId());
                                                
        if ($tokens_data = PushTokenApi::getPushTokensByUserIds($user_ids, $push->getAppId())) {
            
            $tokens = [];
            foreach($tokens_data as $user_id => $user_tokens) {
                foreach($user_tokens as $token) {
                    $tokens[] = $token['push_token'];
                }
            }
            
            if (count($tokens) == 1) {
                $data['target']['to'] = reset($tokens);
            } else {
                $data['target']['registration_ids'] = $tokens;
            }
        } else {
            throw new Exception(t('Нет ни одного получателя для push уведомлений'));
        }
        
        return $data;
    }
    
    /**
    * Заполняет сведения уведомления
    * 
    * @param array $data массив с параметрами, которые пойдут в запрос к API
    * @param \PushSender\Model\Firebase\Push\RsPushNotice $push
    * @return array
    */
    protected function fillNotification($data, $push)
    {
        $data['notification']['title'] = $push->getPushTitle();
        $data['notification']['body'] = $push->getPushBody();
        $data['notification']['sound'] = 'default';

        if ($push->getPushClickAction()) {
            $data['notification']['click_action'] = $push->getPushClickAction();
        }
        
        return $data;
    }
    
    /**
    * Заполняет секцию настроек
    * 
    * @param array $data массив с параметрами, которые пойдут в запрос к API
    * @param \PushSender\Model\Firebase\Push\RsPushNotice $push
    * @return array
    */
    protected function fillOptions($data, $push)
    {
        $data['options']['data'] = $push->getPushData();

        //Дублируем для Android
        $data['options']['data']['title'] = $push->getPushTitle();
        $data['options']['data']['body']  = $push->getPushBody();
        $data['options']['sound']['body'] = 'default';

        return $data;
    }
    
    /**
    * Заполняет данные для авторизации на сервере ReadyScript
    * 
    * @param array $data массив с параметрами, которые пойдут в запрос к API
    * @return array
    */
    protected function fillRsAuth($data)
    {
        if (defined('CLOUD_UNIQ')) {
            //Для облачной сборки
            $data['auth_type'] = 'cloud';
            $data['main_license_hash'] = CLOUD_UNIQ;
        } else {
            //Для коробочной сборки
            $main_license = null;
            __GET_LICENSE_LIST($main_license);
            
            if ($main_license) {
                $data['auth_type'] = 'license';
                $data['main_license_hash'] = sha1(str_replace('-', '', $main_license['license_key']));
            } else {
                throw new Exception(t('Для отправки уведомлений необходимо установить лицензию'));
            }
        }
        return $data;
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
        
        $data = [];
        try {
            $data = $this->fillTarget($data, $push);
            $data = $this->fillNotification($data, $push);
            $data = $this->fillOptions($data, $push);
            $data = $this->fillRsAuth($data);
        } catch(Exception $e) {
            $this->last_error = $e->getMessage();
            
            $this->writeLog(t('Отправка отклонена по причине: %0', [$this->last_error]));
                
            return false;
        }
                
        //Формируем запрос на отправку уведомления
        $context = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded".PHP_EOL,
                'content' => http_build_query($data),
                'timeout' => 5
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];
        
        $this->writeLog(t('POST запрос на URL: %0 с параметрами: %1', [$this->api_url, $context['http']['content']]));
        
        $response = @file_get_contents($this->api_url, null, stream_context_create($context));
        if ($response === false) {
            $this->last_error = t('Не удалось соединиться с сервером API ReadyScript');

            $this->writeLog(t('Отправка отклонена по причине: %0', [$this->last_error]));
            
            return false;
        }

        $this->writeLog(t('Получен ответ от API: %0', [$response]));
        
        $this->last_response = json_decode($response, true);
        return true;
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
