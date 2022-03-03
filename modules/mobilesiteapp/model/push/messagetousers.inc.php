<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model\Push;
use ExternalApi\Model\Utils;

/**
* Push уведомление пользователям для получения уведомления с произвольным текстом
*/
class MessageToUsers extends \PushSender\Model\Firebase\Push\GoogleFCMPushNotice implements
                                    \PushSender\Model\InterfaceDirectPushTokensNotice
{
    public
        $ids,
        /**
        * @var \PushSender\Model\Orm\PushTokenMessage
        */
        $push_message,
        $action; //Имя класс действия для запуска в мобильном приложении
    
    /**
    * Инициализация PUSH уведомления
    * 
    * @param \PushSender\Model\Orm\PushTokenMessage $push_message - объект текстового сообщения
    * @param array $ids - массив с id токенов куда отправить
    */
    public function init(\PushSender\Model\Orm\PushTokenMessage $push_message, $ids)
    {                         
        $this->push_message = $push_message;
        $this->push_message['message'] = Utils::prepareHTML($this->push_message['message']);
        $this->ids = $ids;
    }
    
    /*
    * Возвращает описание уведомления для внутренних нужд системы и 
    * отображения в списках админ. панели
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Произвольное сообщение на устройство (пользователю)');
    }
    
    /**
    * Возвращает для какого приложения (идентификатора приложения в ReadyScript) предназначается push
    * 
    * @return string
    */
    public function getAppId()
    {
        return 'mobilesiteapp';
    }
        
    /**
    * Возвращает одного или нескольких получателей по пользователям
    * 
    * @return array
    */
    public function getRecipientUserIds()
    {
        return [];
    }
    
    /**
    * Возвращает массив PUSH токенов устройств, которым нужно отправить уведомление
    * @return array
    */
    public function getRecipientPushTokens()
    {
        if (!empty($this->ids)){
            return \RS\Orm\Request::make()
                        ->from(new \PushSender\Model\Orm\PushToken())
                        ->whereIn('id', $this->ids)
                        ->where([
                            'app' => $this->getAppId()
                        ])
                        ->objects();
        }
        return [];
    }
    
    /**
    * Возвращает Заголовок для Push уведомления
    * 
    * @return string
    */
    public function getPushTitle()
    {
        return $this->push_message['title'] ? $this->push_message['title'] : t('Новое сообщение');
    }
    
    /**
    * Возвращает текст Push уведомления
    * 
    * @return string
    */
    public function getPushBody()
    {   
        return $this->push_message['body'];
    }
    
    /**
    * Возвращает произвольные данные ключ => значение, которые должны быть переданы с уведомлением
    * 
    * @return array
    */
    public function getPushData()
    {
        $site = \RS\Site\Manager::getSite();
        
        $params = [   //Дополнительные параметры отправляемые открываемой странице
            'no_show_toast' => ($this->push_message['send_type'] == 'Simple') ? false : true,  //Флаг указывающий, чтобы всплывающая подсказка не показывалась
            'title' => $this->getPushTitle(),
            'message' => $this->getPushBody()
        ];
        
        //Допишем дополнительные параметры в зависимости от типа и назначим тип действия
        switch($this->push_message['send_type']){
            case 'Simple':
                $this->action = false;
                break;
            case 'Page':
                $params['html'] = $this->push_message['message'];
                $this->action = "MenuPage";
                break;
            case 'Product':
                $params['id'] = $this->push_message['product_id'];  
                $this->action = "ProductPage";
                break;
            case 'Category':
                $params['id'] = $this->push_message['category_id']; 
                $this->action = "CategoryPage";
                break;
        }
            
        return [
            'site_uid' => $site->getSiteHash(),   
            'soundname' => "default",    
            'content-available' => "1", //Включает отработку события когда приложение в спящем режиме
            'action' => $this->action, //Класс страницы в мобильном приложении, какую нужно открыть в внутри приложения    
            'params' => json_encode($params),
        ];
    }
    
    /**
    * Возвращает click_action для данного уведомления
    * 
    * @return string
    */
    public function getPushClickAction()
    {
        return false;
    }
}