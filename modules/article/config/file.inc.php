<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Config;
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
            'preview_list_pagesize' => new Type\Integer([
                'description' => t('Количество элементов на странице со списком новостей')

            ]),
            'search_fields' => new Type\ArrayList([
                'description' => t('Поля, которые должны войти в поисковый индекс статьи'),
                'hint' => t('После изменения, переиндексируйте статьи (ссылка справа)'),
                'Attr' => [['size' => 5, 'multiple' => 'multiple', 'class' => 'multiselect']],
                'ListFromArray' => [[
                    'title' => t('Название'),
                    'short_content' => t('Краткий текст'),
                    'content' => t('Содержание статьи'),
                    'meta_keywords' => t('Мета ключевые слова')
                ]],
                'CheckboxListView' => true,
                'runtime' => false,
            ]),
        ]);
    }
    
    /**
    * Возвращает значения свойств по-умолчанию
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('ajaxReIndexArticles', [], 'article-tools'),
                    'title' => t('Переиндексировать статьи'),
                    'description' => t('Построит заново поисковый индекс по статьям'),
                    'confirm' => t('Вы действительно хотите переиндексировать все статьи?')
                ]
            ]
            ];
    }      
}
