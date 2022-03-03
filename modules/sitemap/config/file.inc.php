<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Sitemap\Config;
use \RS\Orm\Type;

/**
* @defgroup Article Article(Статьи)
* Модуль позволяет создавать статьи по категориям. 
* Данный модуль можно использовать для организации информационных каналов (Например: новости, блог) или хранения одиночных текстовых материалов.
* У категории или у отдельной стати можно задать символьный идентификатор, который в дальнейшем можно использовать для выборки целого списка или одной статьи соответственно.
*/

/**
* Класс конфигурации модуля Статьи
*/
class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'priority' => new Type\Double([
                'description' => t('Приоритет страниц по-умолчанию')
            ]),
            'changefreq' => new Type\Varchar([
                'description' => t('Как часто меняется контент на страницах?'),
                'listFromArray' => [[
                    'disabled' => t('Не задано'),
                    'never' => t('Никогда'),
                    'yearly' => t('Ежегодно'),
                    'monthly' => t('Ежемесячно'),
                    'weekly' => t('Еженедельно'),
                    'daily' => t('Ежедневно'),
                    'hourly' => t('Каждый час'),
                    'always' => t('Всегда'),
                ]]
            ]),
            'set_generate_time_as_lastmod' => new Type\Integer([
                'description' => t('Устанавливать дату генерации sitemap в секцию lastmod по-умолчанию'),
                'checkboxView' => [1,0]
            ]),
            'lifetime' => new Type\Integer([
                'description' => t('Время жизни sitemap файла в минутах'),
                'hint' => t('После истечения данного периода файл будет создаваться заново, 0 - означает всегда создавать налету')
            ]),
            'add_urls' => new Type\Text([
                'description' => t('Добавить следующие адреса (каждый с новой строки)')
            ]),
            'exclude_urls' => new Type\Text([
                'description' => t('Исключить следующие адреса по маске (каждый с новой строки, применяются регулярные выражения)')
            ]),
            'max_chunk_item_count' => new Type\Integer([
                'description' => t('Максимальное количество страниц в одном файле sitemap'),
                'hint' => t('Если страниц будет больше, то основной файл sitemap будет содержать инструкции sitemapindex, в котором будут ссылки на небольшие sitemap файлы'),
            ])
        ]);
        
    }
}
