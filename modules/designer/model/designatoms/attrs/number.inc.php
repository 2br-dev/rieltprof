<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class Number - аттрибут типа число
 */
class Number extends AbstractAttr {
    protected $min = null; //Значение по умолчанию
    protected $max = null; //Значение по умолчанию
    protected $step = null; //Значение по умолчанию

    /**
     * AbstractCssProperty constructor.
     *
     * @param string $attribute - название аттрибута
     * @param string $title - имя аттрибута
     * @param mixed $value - значение аттрибута
     */
    function __construct($attribute, $title, $value = 0)
    {
        parent::__construct($attribute, $title, $value);
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
    function getInfo()
    {
        $data = parent::getInfo();
        $data['min']  = $this->getMin();
        $data['max']  = $this->getMax();
        $data['step'] = $this->getStep();
        return $data;
    }
}