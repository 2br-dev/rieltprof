<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class ResetRootData - аттрибут для выбора значений смены данных установленных в корне
 */
class ResetRootData extends AbstractAttr {

    /**
     * Конструктор класса
     *
     * @param string $atom_field - название поля атома, в которое запишется значение
     * @param string $title - имя поля атома
     * @param array $fields - массив полей для сброса, вместо reset_attrs
     */
    function __construct($atom_field = null, $title = 'Сменить данные?', $fields = null)
    {
        if (!$atom_field){
            $atom_field = 'changerootdata';
        }
        parent::__construct($atom_field, $title, $fields);
    }

}