<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\CashRegisterType;

/**
 * Абстрактный класс проксирующего класса для кассовых модулей.
 * Может применяться, если у одного модуля имеется несколько различных версий API.
 *
 * Текущий класс пробрасывает вызов всех методов в объект класса нужной версии API.
 */
abstract class AbstractProxy
{
    /**
     * @var \Shop\Model\CashRegisterType\AbstractType
     */
    protected $api_instance;


    function __construct()
    {
        $class_name = static::getApiVersionClass();
        $this->api_instance = new $class_name();

        if (!($this->api_instance instanceof AbstractType)) {
            throw new \RS\Exception(t('Класс, возвращаемый методом getApiVersionClass должен быть потомком класса Shop\Model\CashRegisterType\AbstractType'));
        }
    }

    /**
     * Возвращает имя класса, в котором реализована логика работы с необходимой версий API АТОЛ.
     * Класс должен быть обязтельно потомком Shop\Model\CashRegisterType\AbstractType
     *
     * @return string
     */
    protected static function getApiVersionClass()
    {
        throw new \RS\Exception(t('Необходимо реализовать метод getApiVersionClass в классе %0', [get_called_class()]));
    }

    /**
     * Проксирует вызов методов в конкретную версию API
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $func = [$this->api_instance, $name];
        if (is_callable($func)) {
            return call_user_func_array($func, $arguments);
        }

        throw new \RS\Exception(t('Не найден метод %0 в классе %1', [$name, get_class($this->api_instance)]));
    }

    /**
     * Проксирует вызов статических методов в конкретную версию API
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $class = static::getApiVersionClass();
        $func = [$class, $name];

        if (is_callable($func)) {
            return call_user_func_array($func, $arguments);
        }
        throw new \RS\Exception(t('Не найден статический метод %0 в классе %1', [$name, $class]));
    }
}