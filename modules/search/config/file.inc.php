<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Search\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;

class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'search_service' => new Type\Varchar([
                'description' => t('Поисковый сервис'),
                'list' => [['\Search\Model\SearchApi', 'getEnginesNames']]
            ]),
            'search_type' => new Type\Varchar([
                'attr' => [['size' => 1]],
                'listFromArray' => [[
                    'like' => 'like',
                    'likeplus' => 'like +',
                    'fulltext' => t('Полнотекстовый')
                ]],
                'description' => t('Тип поиска'),
                'hint' => t('Актуально для поискового сервиса Mysql;<br>После изменения типа поиска требуется переиндексировать товары и другие объекты поиска'),
                'template' => '%search%/form/sysoptions/search_type.tpl',
            ]),
            'search_type_likeplus_spell_checker_enable' => new Type\Integer([
                'description' => t('Искать с учетом грамматических ошибок'),
                'hint' => t('Для работы данной функции будет использоваться внешний сервис Яндекс.Спеллер. Актуально только для поиска like+'),
                'checkboxView' => [1, 0]
            ]),
            'search_type_likeplus_ignore_symbols' => new Type\Varchar([
                'description' => t('Игнорируемые при поиске символы'),
                'hint' => t('Данные символы будут считаться разделителями слов')
            ]),
        ]);
    }
}
