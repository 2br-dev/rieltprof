<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

/**
* Тип - координаты на карте.
*/
class Coordinates extends ArrayList
{
    protected $form_template = '%system%/coreobject/type/form/coordinates.tpl';

    /**
     * Возвращает значение поля в базе по-умолчанию
     *
     * @param bool $db_format - привести результат к хранимому в БД виду
     * @return mixed
     */
    public function getDefault($db_format = false)
    {
        return [
            'address' => '',
            'lat' => 55.7533,
            'lng' => 37.6226,
        ];
    }
}
