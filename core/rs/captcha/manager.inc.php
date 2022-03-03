<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Captcha;

/**
* Менеджер каптчи.
*/
class Manager
{
    static protected
        $types; // кэшированный список доступных в сисеме классов капчи
    
    /**
    * Возвращает список доступных в сисеме классов капчи
    * 
    * @return array
    */
    static function getTypes()
    {
        if (self::$types === null) {
            // В списке доступных классов всегда есть заглушка
            $event_result = \RS\Event\Manager::fire('captcha.gettypes', [new \RS\Captcha\Stub()]);
            $list = $event_result->getResult();
            self::$types = [];
            foreach($list as $type_object) {
                if (!($type_object instanceof AbstractCaptcha)) {
                    throw new \RS\Exception(t('Класс какпчи должен быть наследником \RS\Captcha\AbstractCaptcha'));
                }
                self::$types[$type_object->getShortName()] = $type_object;
            }
        }
        
        return self::$types;
    }
    
    /**
    * Возвращает список доступных в сисеме классов капчи
    * 
    * @return array
    */
    static function getCaptchaList()
    {
        $result = [];
        foreach(self::getTypes() as $key => $object) {
            $result[$key] = $object->getTitle();
        }
        return $result;
    }
    
    /**
    * Возвращает объект типа капчи по идентификатору
    * 
    * @param string $name - короткий идентификатор класса онлайн касс
    */
    static function getTypeByShortName($name)
    {
        $list = self::getTypes();
        return isset($list[$name]) ? $list[$name] : new \RS\Captcha\Stub();
    }
    
    /**
    * Возвращает объект текущей капчи.
    * В административной панели никогда нет капчи
    * 
    * @return \RS\Captcha\AbstractCaptcha
    */
    static function currentCaptcha()
    {
        $router = \RS\Router\Manager::obj();
        if ($router->isAdminZone()) {
            return new \RS\Captcha\Stub();
        } else {
            $system_config = \RS\Config\Loader::getSystemConfig();
            return self::getTypeByShortName($system_config['captcha_class']);
        }
    }
}