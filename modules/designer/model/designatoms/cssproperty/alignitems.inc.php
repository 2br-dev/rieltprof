<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class AlignItems - класс для свойства горизонтального позиционирования CSS
 */
class AlignItems extends Select {

    /**
     * Обычное свойство CSS, но c выбором из выпадающего списка
     *
     * @param string $property - название элемента CSS свойства. Например: margin
     * @param string $title - название для отображения CSS свойства. Наприимер: Задний фон. Если пусто будет взято из имени элемента
     * @param string $value - значение CSS свойства
     * @param array $data - дополниельные параметры для передачи в вместе со свойством 'ключ' => 'значение'
     */
    function __construct($property, $title, $value = null, $data = [])
    {
        parent::__construct($property, $title, $value, $data);
        $this->setOptions([
            'left' => t('Слева'),
            'center' => t('Центр'),
            'right' => t('Справа'),
        ]);
    }

}