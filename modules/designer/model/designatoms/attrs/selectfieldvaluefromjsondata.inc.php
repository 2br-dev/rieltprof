<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class SelectFieldValueFromJSONData - аттрибут выбора для поля атома используя список из подгруженных данных
 */
class SelectFieldValueFromJSONData extends AbstractAttr {

    /**
     * Конструктор класса
     *
     * @param string $atom_field - название поля атома, в которое запишется значение
     * @param string $title - имя поля атома
     * @param string $path_to_data - путь к данным из общего хранилища, которое храница общем простаранстведаных
     * ***************
     * Пространство данных задаётся в контореллере блока designer в свойствах, которые подаются на публичую часть как
     * JSON сданными с ключом 'designer'
     * Например:
     * data[designer][banners][zonesList] - содержит список зон баннеров
     * Здесь нужно указать лишь относительный путь от ключа designer.
     * Например для нашего случая:
     * banners/zonesList - эквивалентно data[designer][banners][zonesList]
     * ***************
     */
    function __construct($atom_field, $title, $path_to_data)
    {
        parent::__construct($atom_field, $title, $path_to_data);
    }
}