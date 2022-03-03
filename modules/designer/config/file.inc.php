<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Config;
use \RS\Orm\Type;


class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'ya_map_api_key' => new Type\Varchar([
                'visible' => false,
                'description' => t('Ключ для показа Яндекс карты'),
            ]),
            'designer_settings' => new Type\Text([
                'visible' => false,
                'description' => t('Настройки показа мобильного меню'),
            ])
        ]);
    }
}