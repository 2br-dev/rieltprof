<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class Select - класс для обычного свойства CSS, но с выбором в виде выпадающего списка
 */
class Select extends AbstractCssProperty {

    //Тип выпадающего списка
    const SELECT_TYPE_DEFAULT        = 'default'; //Обычный список
    const SELECT_TYPE_SELECT_WITH_MY = 'with_my_item'; //Список с возможностью указания своего значения

    protected $selector_type = 'default';

    /**
     * Обычное свойство CSS, но c выбором из выпадающего списка
     *
     * @param string $property - название элемента CSS свойства. Например: margin
     * @param string $title - название для отображения CSS свойства. Например: Задний фон. Если пусто будет взято из имени элемента
     * @param string $value - значение CSS свойства
     * @param array $data - дополниельные параметры для передачи в вместе со свойством 'ключ' => 'значение'
     */
    function __construct($property, $title, $value = null, $data = [])
    {
        $this->setAdditionDataByKey('options', []);
        parent::__construct($property, $title, $value, $data);
    }

    /**
     * Устанавливает список опций выпадающего списка
     *
     * @param array $options - массив опций параметров 'Ключ' => 'Значение'
     * @return $this
     */
    function setOptions($options)
    {
        //Проверим, если ключи не заданы, чтобы значения были равны ключам
        if (!empty($options)){
            $keys   = array_keys($options);
            $values = array_values($options);
            if ($keys[0] === 0){
                $options = array_combine($values, $values);
            }
        }
        $this->setAdditionDataByKey('options', (array)$options);
        return $this;
    }

    /**
     * Устанавливает тип выпадающего списка
     *
     * @param string $select_type - тип выпадающего списка
     */
    function setSelectorType($select_type)
    {
        $this->selector_type = $select_type;
    }

    /**
     * Возвращает тип выпадающего списка
     *
     * @return string
     */
    function getSelectorType()
    {
        return $this->selector_type;
    }


    /**
     * Возвращает информацию по CSS свойства для хранилища в публичной части
     *
     * @return array
     */
    function getPropertyInfo()
    {
        $data = parent::getPropertyInfo();
        $data['selector_type'] = $this->getSelectorType();
        return $data;
    }
}