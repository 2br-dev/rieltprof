<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Debug\Tool;

/**
* Класс кнопки "создать" в панели инструментов режима отладки
*/
class Create extends AbstractTool
{
    /**
    * Конструктор кнопки "создать"
    * 
    * @param string $href - ссылка кнопки
    * @param string $title - подсказка для кнопки
    * @param array $options - дополнительные аттрибуты html элемента
    */
    function __construct($href, $title = null, array $options = null)
    {
        if ($title === null) {
            $title = t('Создать');
        }
        $this->options['attr']['title'] = $title;
        $this->options['attr']['href'] = $href;
        $this->options['attr']['class'] = "debug-icon-create crud-add";
        parent::__construct($options);
    }
}