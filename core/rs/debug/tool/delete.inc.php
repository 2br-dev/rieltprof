<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Debug\Tool;

/**
* Класс кнопки "удалить" в панели инструментов режима отладки
*/
class Delete extends AbstractTool
{
    /**
     * Конструктор кнопки "удалить"
     *
     * @param string $href - ссылка для действия
     * @param null|string $title - название кнопки
     * @param array|null $options - дополнительные аттрибуты html элемента
     */
    function __construct($href, $title = null, array $options = null)
    {
        if ($title === null) {
            $title = t('удалить');
        }
        
        $this->options['attr'] = [
            'title' => $title,
            'class' => " debug-icon-delete crud-remove-one",
            'href' => $href
        ];
        parent::__construct($options);
    }
}