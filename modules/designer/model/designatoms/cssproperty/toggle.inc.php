<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class Toggle - класс элемента панели - переключатель стиля
 */
class Toggle extends Select {
    /**
     * Устанавливает список опций переключателя одного значения. Допускается всего 2 элемента ключ => значение для массива.
     *
     * @param array $options - массив опций параметров 'Ключ' => 'Значение'
     * @return Select
     * @throws \RS\Exception
     */
    function setOptions($options)
    {
        //Провери значения, чтобы не более 2-х
        if (count($options) != 2){
            throw new \RS\Exception(t('Количество значений ключ=>значение должно быть 2'));
        }
        return parent::setOptions($options);
    }
}