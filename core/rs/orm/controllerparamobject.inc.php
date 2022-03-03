<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm;

use RS\Orm\Storage;

/**
 * ORM объект для конфигураций блочных контроллеров
 */
class ControllerParamObject extends AbstractObject
{
    protected $parent_object;
    protected $parent_param_method = 'Form';

    /**
     * Конструктор объекта параметров контроллера
     *
     * @param PropertyIterator $properties - список свойств, свойство "sectionmodule" зарезервировано.
     */
    function __construct(PropertyIterator $properties)
    {
        parent::__construct();
        $this->setPropertyIterator($properties);
    }

    function _init()
    {
    }

    /**
     * Возвращает объект хранилища
     * @return Storage\AbstractStorage
     */
    function getStorageInstance()
    {
        return new Storage\Stub($this);
    }

    /**
     * Возвращает родительский объект
     *
     * @return object
     */
    public function getParentObject()
    {
        return $this->parent_object;
    }

    /**
     * Устанавливает родительский объект
     *
     * @param object $parent_object
     */
    public function setParentObject($parent_object)
    {
        $this->parent_object = $parent_object;
    }

    /**
     * Возвращает имя метода родительского объекта, возвращающего экземпляр текущего класса
     * Имя родительского метода должно выглядеть как "get" . $parent_param_method ."Object"
     *
     * @return string
     */
    public function getParentParamMethod()
    {
        return $this->parent_param_method;
    }

    /**
     * Устанавливает имя метода родительского объекта, возвращающего экземпляр текущего класса
     * Имя родительского метода должно выглядеть как "get" . $parent_param_method ."Object"
     *
     * @param string $parent_param_method
     * @return void
     */
    public function setParentParamMethod(string $parent_param_method): void
    {
        $this->parent_param_method = $parent_param_method;
    }
}
