<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class SelectFieldsForMenu - аттрибут для выбора значений поляей для атома меню
 */
class SelectFieldsForMenu extends SelectFieldValueAsTree {

    /**
     * Конструктор класса
     *
     * @param string $atom_field - название поля атома, в которое запишется значение
     * @param string $title - имя поля атома
     * @param array $urls_to_data - url'ы с данными для получения дерева для меню и категорий
     */
    function __construct($atom_field, $title, $urls_to_data)
    {
        parent::__construct($atom_field, $title, $urls_to_data);
    }

}