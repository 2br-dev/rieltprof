<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Debug\Tool;

/**
* Класс кнопки "настройка блока" в панели инструментов режима отладки
*/
class BlockOptions extends AbstractTool
{
    /**
    * Конструктор кнопки "настройка блока"
    * 
    * @param mixed $mod_name
    * @return BlockOptions
    */
    function __construct($block_id, array $options = null)
    {
        $this->options['attr'] = [
            'href' => \RS\Router\Manager::obj()->getAdminUrl('editModule', ['id' => $block_id], 'templates-blockctrl'),
            'title' => t('Настройки блока'),
            'class' => "crud-add debug-icon-blockoptions",
            'target' => '_blank'
        ];
        parent::__construct($options);
    }
}