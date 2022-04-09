<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Model\OrmType;

use RS\Orm\Type;

/**
 * Тип поля ORM объекта - "Гео координаты"
 */
class GeoPoint extends Type\Decimal
{
    protected $decimal = 6;
    protected $form_template = '%main%/form/ormtype/geo_point.tpl';
    /** @var string */
    protected $field_longitude_name;

    /**
     * Конструктор свойства
     *
     * @param string $field_longitude_name - имя поля для сохранения координаты долготы (число с 6-ю знаками после точки)
     * @param array $options - массив для быстрой установки параметров
     */
    public function __construct(string $field_longitude_name, array $options = null)
    {
        $this->field_longitude_name = $field_longitude_name;
        parent::__construct($options);
    }

    /**
     * Возвращает имя поля для сохранения координаты долготы
     *
     * @return string
     */
    public function getFieldLongitudeName(): string
    {
        return $this->field_longitude_name;
    }
}
