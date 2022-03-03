<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Debug\Tool;

/**
* Класс кнопки "настройка модуля" в панели инструментов режима отладки
*/
class Options extends AbstractTool
{
    /**
    * Конструктор кнопки "настройка модуля"
    * 
    * @param mixed $mod_name
    * @return Options
    */
    function __construct($mod_name, array $options = null)
    {
        $this->options['attr'] = [
            'href' => \RS\Router\Manager::obj()->getAdminUrl('edit', ['mod' => $mod_name], 'modcontrol-control'),
            'title' => t('Настройки модуля'),
            'class' => " debug-icon-options",
            'target' => '_blank'
        ];
        parent::__construct($options);
    }
}

