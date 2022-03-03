<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model;

/**
* Базовый класс для одного Push уведомления
*/
abstract class AbstractPushNotice
{
    final function __construct() //Перегрузка конструктора невозможна
    {}
    
    //У наследника должен быть реализован метод Init для инициализации переменных
    
    /**
    * Возвращает уникальный идентификатор уведомления
    * 
    * @return string
    */
    public function getId()
    {
        $class = strtolower(get_class($this));
        return str_replace(['\\', '-model-push'], ['-', ''], $class);
    }
    
    /**
    * Возвращает объект класса уведомления по короткому ID уведомления
    * 
    * @return string
    */
    public static function getPushInstanceById($id)
    {
        
    }
    
    /**
    * Зарезервировано. Возвращает true, в случае если уведомление 
    * широковещательное, а не адресное
    * 
    * @return bool
    */
    public function isMulticast()
    {
        return false;
    }
    
    public function send()
    {
        //Проверяем, задекларировано ли Push уведомление в приложении
        $app = \RS\RemoteApp\Manager::getAppByType($this->getAppId());
        if (!($app instanceof App\InterfaceHasPush)) {
            throw new Exception(t('Приложение %0 должно имплементировать интерфейс PushSender\Model\App\InterfaceHasPush', [$this->getAppId()]));
        }
        
        if (!$this->isInList($app->getPushNotices())) {
            throw new Exception(t('Уведомление %0 должно быть задекларировано в методе getPushNotices в приложении %1', [get_class($this), get_class($app)]));
        }
        
        return $this->getProvider()->transfer($this);
    }
    
    /**
    * Возвращает true, если текущий класс находится в списке уведомлений $push_list
    * 
    * @param array $push_list
    * @return bool
    */
    private function isInList($push_list)
    {
        foreach($push_list as $notice) {
            if ($this instanceof $notice)
                return true;
        }
        return false;
    }
    
    /**
    * Возвращает описание уведомления для внутренних нужд системы и 
    * отображения в списках админ. панели
    * 
    * @return string
    */
    abstract public function getTitle();
    
    /**
    * Возвращает для какого приложения (идентификатора приложения в ReadyScript) предназначается push
    * 
    * @return string
    */
    abstract public function getAppId();
    
    /**
    * Возвращает провайдера, через которого нужно отправить данное уведомление
    * 
    * @return \PushSender\Model\AbstractProvider
    */
    abstract public function getProvider();
        
    /**
    * Возвращает одного или нескольких получателей
    * 
    * @return array
    */
    abstract public function getRecipientUserIds();
    
    /**
    * Возвращает Заголовок для Push уведомления
    * 
    * @return string
    */
    abstract public function getPushTitle();
    
    /**
    * Возвращает текст Push уведомления
    * 
    * @return string
    */
    abstract public function getPushBody();
    
    /**
    * Возвращает произвольные данные ключ => значение, которые должны быть переданы с уведомлением
    * 
    * @return array
    */
    abstract public function getPushData();
    
    /**
    * Возвращает click_action, который нужно передать в push уведомление
    * 
    * @return string
    */
    public function getPushClickAction()
    {}
}
