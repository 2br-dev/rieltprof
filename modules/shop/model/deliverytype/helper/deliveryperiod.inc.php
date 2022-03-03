<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\DeliveryType\Helper;

/**
 * Класс отвечает за работу с диапазонами сроков доставки
 */
class DeliveryPeriod
{
    /**
     * @var integer Минимальное количество дней, требуемое для доставки. Задается в настройках доставки.
     * Используется для технических нужд и интеграций со сторонними сервисами
     */
    private $day_min;

    /**
     * @var integer Максимальное количество дней, требуемое для доставки. Задается в настройках доставки.
     * Используется для технических нужд и интеграций со сторонними сервисами
     */
    private $day_max;

    /**
     * @var string Диапазон в виде строки, задается в настройках доставки.
     * Используется для отображения покупателям.
     */
    private $period_as_text;


    function __construct($day_min = null, $day_max = null, $period_as_text = null)
    {
        $this->setDayMin($day_min);
        $this->setDayMax($day_max);
        $this->setPeriodAsText($period_as_text);
    }

    /**
     * Устанавливает минимальный срок доставки в днях
     *
     * @param integer $days Кол-во дней
     * @return void
     */
    public function setDayMin($days)
    {
        $this->day_min = $days;
    }

    /**
     * Устанавливает максимальный срк доставки в днях
     *
     * @param integer $days Кол-во дней
     * @return void
     */
    public function setDayMax($days)
    {
        $this->day_max = $days;
    }

    /**
     * Устанавливает период в виде текста
     *
     * @param string $period период доставки
     * @return string
     */
    public function setPeriodAsText($period)
    {
        $this->period_as_text = $period;
    }

    /**
     * Возвращает минимальный срок доставки, в днях
     *
     * @return int
     */
    public function getDayMin()
    {
        return $this->day_min;
    }

    /**
     * Возвращает максимальный срок доставки, в днях
     *
     * @return int
     */
    public function getDayMax()
    {
        return $this->day_max;
    }

    /**
     * Возвращает период доставки в виде строки
     *
     * @return string
     */
    public function getPeriodAsText()
    {
        $min = $this->getDayMin();
        $max = $this->getDayMax();

        if ($this->period_as_text) {
            return $this->period_as_text;
        }
        elseif ($min == $max) {
            return t('%0 [plural:%0:день|дня|дней]', [$min]);
        }
        elseif ($min && !$max) {
            return t('от %0 [plural:%0:дня|дней|дней]', [$min]);
        }
        elseif (!$min && $max) {
            return t('до %0 [plural:%0:дня|дней|дней]', [$max]);
        }
        elseif ($min && $max) {
            return t('от %0 до %1 [plural:%1:дня|дней|дней]', [$min, $max]);
        }

        return '';
    }

    /**
     * Возвращает true, если период задан. В противном случае false
     *
     * @return bool
     */
    public function hasPeriod()
    {
        return $this->day_min || $this->day_max || $this->period_as_text;
    }
}