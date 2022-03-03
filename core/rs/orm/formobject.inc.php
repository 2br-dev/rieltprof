<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm;

use RS\Orm\Storage\Stub as StorageStub;

/**
* ORM объект с динамической структурой, используемый исключительно для отображения форм
*/
class FormObject extends AbstractObject
{
    protected $parent_object;
    protected $parent_param_method = 'Form';

    /**
    * Конструктор объекта параметров контроллера
    *
    * @param PropertyIterator $properties - список свойств, свойство "sectionmodule" зарезервировано.
    */
    public function __construct(PropertyIterator $properties)
    {
        parent::__construct();
        $this->setPropertyIterator($properties);
    }

    public function _init()
    {}

    /**
    * Возвращает объект хранилища
    * @return \RS\Orm\Storage\AbstractStorage
    */
    public function getStorageInstance()
    {
        return new StorageStub($this);
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
     * Нужно для работы древовидных выпадающих списков
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
     * Нужно для работы древовидных выпадающих списков
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
