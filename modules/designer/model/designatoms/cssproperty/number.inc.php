<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class Number - аттрибут типа число
 */
class Number extends AbstractCssProperty {
    protected $min = null; //Значение по умолчанию
    protected $max = null; //Значение по умолчанию
    protected $step = null; //Значение по умолчанию

    /**
     * Обычное свойство CSS
     *
     * @param string $property - название элемента CSS свойства. Например: margin
     * @param string $title - название для отображения CSS свойства. Наприимер: Задний фон. Если пусто будет взято из имени элемента
     * @param string $value - значение CSS свойства
     * @param array $data - дополниельные параметры для передачи в вместе со свойством 'ключ' => 'значение'
     */
    function __construct($property, $title, $value = null, $data = [])
    {
        parent::__construct($property, $title, $value, $data);
    }

    /**
     * Возращает минимальное значение
     *
     * @return string
     */
    function getMin()
    {
        return $this->min;
    }

    /**
     * Установка минимального значения
     *
     * @param integer $min - минимальное значние
     * @return $this
     */
    function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * Возращает максимальное значение
     *
     * @return string
     */
    function getMax()
    {
        return $this->max;
    }

    /**
     * Установка максимального значения
     *
     * @param integer $max - максимальное значние
     * @return $this
     */
    function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * Возращает значение шага
     *
     * @return string
     */
    function getStep()
    {
        return $this->step;
    }

    /**
     * Установка значения шага
     *
     * @param integer $step - шаг перехода
     * @return $this
     */
    function setStep($step)
    {
        $this->step = $step;
        return $this;
    }


    /**
     * Возвращает данные для хранлища для публичной части
     *
     * @return array
     */
    function getPropertyInfo()
    {
        $data = parent::getPropertyInfo();
        $data['min']  = $this->getMin();
        $data['max']  = $this->getMax();
        $data['step'] = $this->getStep();
        return $data;
    }
}