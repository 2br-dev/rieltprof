<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\RemoteApp;

/**
* Базовый класс для внешних приложений, которым нужен backend в виде ReadyScript. 
* Это могут быть мобильные приложения, Desktop приложения, браузерные приложения.
* Ядро ReadyScript предоставляет общий механизм регистрации и получения всех приложений в системе.
* 
* Для регистрации приложения достаточно обработать событие getapps. Для получения списка приложений 
* нужно воспользоваться классом \RS\RemoteApp\Manager
* 
* Далее, приложение может имплементировать интерфейсы различных модулей, тем 
* самым приобретать поведение. Например, при имплементации \ExternalApi\Model\App\InterfaceHasApi
* приложение получает возможность пользоваться внешними API.
* При имплементации \PushSender\Model\App\InterfaceHasPush, приложение получает 
* возможность отправлять Push уведомления.
*/
abstract class AbstractAppType
{                
    /**
    * Возвращает  строковый идентификатор приложения
    * 
    * @return string
    */
    abstract public function getId();
    
    /**
    * Возвращает название приложения
    * 
    * @return string
    */
    abstract public function getTitle();
}
