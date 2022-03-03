<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Notes\Config;
use RS\Orm\Type;

/**
* Класс конфигурации модуля
*/
class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        return parent::_init()->append([
            'widget_notes_page_size' => new Type\Integer([
                'description' => t('Количество элементов, отображаемых в виджете')
            ])
        ]);
    }
}