<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;

use Catalog\Model\Orm\Product;
use RS\Config\Loader as ConfigLoader;

/**
 * Класс для получения габаритьов товара
 */
class ProductDimensions
{
    const DIMENSION_LENGTH = 'l';
    const DIMENSION_WIDTH = 'w';
    const DIMENSION_HEIGHT = 'h';
    const DIMENSION_UNIT_MM = 'mm';
    const DIMENSION_UNIT_SM = 'sm';
    const DIMENSION_UNIT_M = 'm';

    const DIMENSION_COEFFICIENT = [
        self::DIMENSION_UNIT_MM => 1,
        self::DIMENSION_UNIT_SM => 10,
        self::DIMENSION_UNIT_M => 1000,
    ];

    protected $config;
    protected $product;
    protected $dimensions = [];
    protected $cache = [];

    /**
     * ProductDimensions constructor.
     *
     * @param Product $product - объект товара
     */
    public function __construct(Product $product)
    {
        $this->config = ConfigLoader::byModule($this);
        $this->product = $product;
    }

    /**
     * Возвращает объём товара
     *
     * @param string $unit - идентификатор единицы измерения, в которой вернётся результат
     * @return float
     */
    public function getVolume($unit = self::DIMENSION_UNIT_M)
    {
        return $this->getLength($unit) * $this->getWidth($unit) * $this->getHeight($unit);
    }

    /**
     * Возвращает длинну товара
     *
     * @param string $unit - идентификатор единицы измерения, в которой вернётся результат
     * @param bool $reload_dimensions - перезагрузить габариты из базы
     * @return float
     */
    public function getLength($unit = self::DIMENSION_UNIT_SM, $reload_dimensions = false)
    {
        return $this->getDimension(self::DIMENSION_LENGTH, $unit, $reload_dimensions);
    }

    /**
     * Возвращает ширину товара
     *
     * @param string $unit - идентификатор единицы измерения, в которой вернётся результат
     * @param bool $reload_dimensions - перезагрузить габариты из базы
     * @return float
     */
    public function getWidth($unit = self::DIMENSION_UNIT_SM, $reload_dimensions = false)
    {
        return $this->getDimension(self::DIMENSION_WIDTH, $unit, $reload_dimensions);
    }

    /**
     * Возвращает высоту товара
     *
     * @param string $unit - идентификатор единицы измерения, в которой вернётся результат
     * @param bool $reload_dimensions - перезагрузить габариты из базы
     * @return float
     */
    public function getHeight($unit = self::DIMENSION_UNIT_SM, $reload_dimensions = false)
    {
        return $this->getDimension(self::DIMENSION_HEIGHT, $unit, $reload_dimensions);
    }

    /**
     * Возвращает габарит товара по указанной стороне
     *
     * @param string $dimension - идентификатор габарита
     * @param string $unit - идентификатор единицы измерения, в которой вернётся результат
     * @param bool $reload_dimensions - перезагрузить габариты из базы
     * @return float
     */
    protected function getDimension($dimension, $unit, $reload_dimensions = false)
    {
        if ($reload_dimensions || !isset($this->dimensions[$dimension])) {
            $this->loadDimensions($reload_dimensions);
            $this->cache = [];
        }
        if (!isset($value[$dimension][$unit])) {
            $this->cache[$dimension][$unit] = $this->dimensions[$dimension] * self::DIMENSION_COEFFICIENT[$this->config['dimensions_unit']] / self::DIMENSION_COEFFICIENT[$unit];
        }

        return $this->cache[$dimension][$unit];
    }

    /**
     * Заполняет габариты данными из базы
     *
     * @param bool $reload_properties - перезагрузить характеристики из базы
     * @return void
     */
    protected function loadDimensions($reload_properties = false)
    {
        if ($reload_properties) {
            $this->product['properties'] = null;
        }
        $this->product->fillProperty();
        $this->dimensions[self::DIMENSION_LENGTH] = (float)$this->product->getPropertyValueById($this->config['property_product_length'], $this->config['default_product_length']);
        $this->dimensions[self::DIMENSION_WIDTH] = (float)$this->product->getPropertyValueById($this->config['property_product_width'], $this->config['default_product_width']);
        $this->dimensions[self::DIMENSION_HEIGHT] = (float)$this->product->getPropertyValueById($this->config['property_product_height'], $this->config['default_product_height']);
    }

    /**
     * Возвращает справочник единиц измерения габаритов
     *
     * @return string[]
     */
    public static function handbookDimensionsUnits()
    {
        return [
            self::DIMENSION_UNIT_MM => t('миллиметры'),
            self::DIMENSION_UNIT_SM => t('сантиметры'),
            self::DIMENSION_UNIT_M => t('метры'),
        ];
    }
}
