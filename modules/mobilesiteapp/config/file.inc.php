<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace MobileSiteApp\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;

/**
* Конфигурационный файл модуля Мобильный сайт приложение
*/
class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'default_theme' => new Type\Varchar([
                    'runtime' => false, 
                    'description' => t('Шаблон по умолчанию'),
                    'list' => [['\MobileSiteApp\Model\TemplateManager','staticTemplatesList']],
                ]),
                'allow_user_groups' => new Type\ArrayList([
                    'runtime' => false,            
                    'description' => t('Группы пользователей, для которых доступно данное приложение'),
                    'list' => [['\Users\Model\GroupApi','staticSelectList']],
                    'size' => 7,
                    'attr' => [['multiple' => true]]
                ]),
                'disable_buy' => new Type\Integer([
                    'description' => t('Скрыть корзину в приложении'),
                    'checkboxView' => [1,0]
                ]),
                'push_enable' => new Type\Integer([
                    'description' => t('Включить Push уведомления для данного приложения'),
                    'checkboxView' => [1,0]
                ]),
                'banner_zone' => new Type\Integer([
                    'description' => t('Баннерная зона'),
                    'list' => [['\Banners\Model\ZoneApi','staticAdminSelectList']],
                    'hint' => t('Баннерная зона, из которой будут выводиться баннеры на главной странице мобильного приложения')
                ]),
                'mobile_phone' => new Type\Varchar([
                    'description' => t('Номер телефона для отображения в приложении'),
                    'hint' => t('Если пусто, то отображаться на будет')
                ]),
                'root_dir' => new Type\Integer([
                    'description' => t('Корневая директория'),
                    'tree' => [['\Catalog\Model\DirApi','staticTreeList'], 0, [0 => t('- Корень категория -')]],
                    'hint' => t('На главной странице приложения будут отображены категории, являющиеся дочерними для указанной в данной опции')
                ]),
                'tablet_root_dir_sizes' => new Type\Varchar([
                    'description' => t('Размеры отображения категорий для главной на планшете'),
                    'hint' => t('M - middle, s - small. Категории будут отображаться последовательно согласно схеме'),
                ]),
                'products_pagesize' => new Type\Integer([
                    'description' => t('По сколько товаров показывать в категории'),
                    'hint' => t('Отвечает за количество подгружаемых единоразово товаров, остальные товары будут загружаться при прокрутке до последнего товара в списке')
                ]),
                'menu_root_dir' => new Type\Integer([
                    'description' => t('Корневой элемент для меню'),
                    'tree' => [['\Menu\Model\Api','staticTreeList'], 0, [0 => t('- Верхний уровень -')]],
                    'hint' => t('В мобильном приложении будут отображены дочерние к выбранному здесь пункту меню')
                ]),
                'top_products_dir' => new Type\Integer([
                    'description' => t('Категория топ товаров'),
                    'tree' => [['\Catalog\Model\DirApi','staticSpecTreeList'], 0, ['' => t('- Не выбрана -')]],
                    'hint' => t('Указывает категорию, из которой выбирать товары для отображения на главной активности приложения')
                ]),
                'top_products_pagesize' => new Type\Integer([
                    'description' => t('Сколько товаров показывать в топе'),
                ]),
                'top_products_order' => new Type\Varchar([
                    'description' => t('Поле сортировки топ товаров'),
                    'listFromArray' => [[
                        'id' => 'ID',
                        'title' => t('Название'),
                        
                        'num DESC' => t('По наличию'),
                        'id DESC' => t('ID обратн. порядок'),
                        'dateof DESC' => t('По новизне'),
                        'rating DESC' => t('По рейтингу'),
                    ]]
                ]),
                'mobile_products_size' => new Type\Integer([
                    'description' => t('Сколько товаров показывать на мобильном устройстве'),
                    'listFromArray' => [[
                        '12' => '1',
                        '6' => '2',
                        '4' => '3',
                        '3' => '4',
                    ]]
                ]),
                'tablet_products_size' => new Type\Integer([
                    'description' => t('Сколько товаров показывать на планшете'),
                    'listFromArray' => [[
                        '12' => '1',
                        '6' => '2',
                        '4' => '3',
                        '3' => '4',
                    ]]
                ]),
                'article_root_category' => new Type\Integer([
                    'description' => t('Корневой элемент новостей'),
                    'hint' => t('С какой категории выводить'),
                    'tree' => [['\Article\Model\CatApi', 'staticTreeList'], 0, [0 => t('- Верхний уровень -')]],
                ]),
                'enable_app_sticker' => new Type\Varchar([
                    'description' => t('Отображать стикер о том, что есть приложение у сайта'),
                    'checkboxView' => [1,0]
                ])
        ]);
    }
}
