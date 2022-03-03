<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Debug\Tool;

/**
* Класс кнопки "Сортировка" в панели инструментов режима отладки
*/
class Sorting extends AbstractTool
{
    /**
    * Конструктор кнопки "создать"
    * 
    * @param string $href - ссылка кнопки
    * @param string $title - подсказка для кнопки
    * @param array $options - дополнительные аттрибуты html элемента
    */
    function __construct($href, $title, array $options = null)
    {
        if ($title === null) {
            $title = t('редактировать');
        }
        
        $this->options['attr'] = [
            'title' => $title,
            'class' => " debug-icon-sort crud-edit",
            'data-url' => $href
        ];
        parent::__construct($options);
    }
}