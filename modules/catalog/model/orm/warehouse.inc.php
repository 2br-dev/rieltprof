<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Orm;

use Catalog\Model\WareHouseApi;
use RS\Debug\Action as DebugAction;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;

/**
 * ORM Объект - склад
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Короткое название
 * @property string $alias URL имя
 * @property integer $group_id Группа
 * @property string $image Картинка
 * @property string $description Описание
 * @property string $adress Адрес
 * @property string $phone Телефон
 * @property string $work_time Время работы
 * @property double $coor_x Координата X магазина
 * @property double $coor_y Координата Y магазина
 * @property integer $default_house Склад по умолчанию
 * @property integer $public Показывать склад в карточке товара
 * @property integer $checkout_public Показывать склад как пункт самовывоза
 * @property integer $dont_change_stocks Не списывать остатки с данного склада
 * @property integer $use_in_sitemap Добавлять в sitemap
 * @property string $xml_id Идентификатор в системе 1C
 * @property integer $sortn Индекс сортировки
 * @property string $meta_title Заголовок
 * @property string $meta_keywords Ключевые слова
 * @property string $meta_description Описание
 * --\--
 */
class WareHouse extends OrmObject
{
    protected static $table = 'warehouse';

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Короткое название'),
                    'Checker' => ['chkEmpty', t('Укажите название склада')],
                    'attr' => [[
                        'data-autotranslit' => 'alias'
                    ]]
                ]),
                'alias' => new Type\Varchar([
                    'maxLength' => '150',
                    'index' => true,
                    'unique' => true,
                    'description' => t('URL имя'),
                    'hint' => t('Могут использоваться только английские буквы, цифры, знак подчеркивания, точка и минус'),
                    'meVisible' => false,
                    'checker' => ['chkAlias', null],
                ]),
                'group_id' => new Type\Integer([
                    'description' => t('Группа'),
                    'list' => [['\Catalog\Model\WareHouseGroupApi', 'staticSelectList'], [0 => t('- Без группы -')]],
                    'default' => 0,
                    'allowEmpty' => false,
                ]),
                'image' => new Type\Image([
                    'description' => t('Картинка'),
                    'maxLength' => '255',
                    'max_file_size' => 10000000,
                    'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'],
                    'meVisible' => false,
                ]),
                'description' => new Type\Richtext([
                    'description' => t('Описание'),
                ]),
                'adress' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Адрес'),
                    'Attr' => [[
                        'autocomplete' => 'off',
                        'placeholder' => t('Введите адрес склада'),
                        'class' => 'autocomplete',
                    ]],
                    'template' => '%catalog%/form/warehouse/address.tpl',
                    'allowempty' => true,
                    'meVisible' => false,
                ]),
                'phone' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Телефон'),
                    'allowempty' => true,
                ]),
                'work_time' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Время работы'),
                    'allowempty' => true,
                ]),
                'coor_x' => new Type\Real([
                    'maxLength' => '255',
                    'description' => t('Координата X магазина'),
                    'hidden' => true,
                    'meVisible' => false,
                    'default' => '55.7533',
                    'allowempty' => true,
                ]),
                'coor_y' => new Type\Real([
                    'maxLength' => '255',
                    'description' => t('Координата Y магазина'),
                    'hidden' => true,
                    'meVisible' => false,
                    'default' => '37.6226',
                    'allowempty' => true,
                ]),
                'default_house' => new Type\Integer([
                    'description' => t('Склад по умолчанию'),
                    'hint' => t('Склад, который будет выбран, если не найден склад необходимый пользователю. <br/>
                            Склад по умолчанию может быть только один.'),
                    'maxLength' => '1',
                    'meVisible' => false,
                    'CheckboxView' => [1, 0],
                ]),
                'public' => new Type\Integer([
                    'maxLength' => '1',
                    'index' => true,
                    'description' => t('Показывать склад в карточке товара'),
                    'CheckboxView' => [1, 0],
                ]),
                'checkout_public' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Показывать склад как пункт самовывоза'),
                    'CheckboxView' => [1, 0],
                ]),
                'dont_change_stocks' => new Type\Integer([
                    'description' => t('Не списывать остатки с данного склада'),
                    'hint' => t('При списании/возврате остатков вместо данного склада будет использован склад по умолчанию.<br/><br/>
                                Опция не может быть включена у склада по умалчанию.'),
                    'CheckboxView' => [1, 0],
                ]),
                'use_in_sitemap' => new Type\Integer([
                    'description' => t('Добавлять в sitemap'),
                    'default' => 0,
                    'checkboxView' => [1, 0]
                ]),
                'xml_id' => new Type\Varchar([
                    'maxLength' => '255',
                    'allowEmpty' => true,
                    'meVisible' => false,
                    'description' => t('Идентификатор в системе 1C'),
                ]),
                'sortn' => new Type\Integer([
                    'description' => t('Индекс сортировки'),
                    'visible' => false
                ]),
            t('Мета тэги'),
                'meta_title' => new Type\Varchar([
                    'maxLength' => 1000,
                    'description' => t('Заголовок'),
                ]),
                'meta_keywords' => new Type\Varchar([
                    'maxLength' => 1000,
                    'description' => t('Ключевые слова'),
                ]),
                'meta_description' => new Type\Varchar([
                    'maxLength' => 1000,
                    'viewAsTextarea' => true,
                    'description' => t('Описание'),
                ]),
        ]);

        $this->addIndex(['site_id', 'xml_id'], self::INDEX_UNIQUE);
        $this->addIndex(['site_id', 'alias'], self::INDEX_UNIQUE);
        $this->addIndex(['coor_x', 'coor_y'], self::INDEX_KEY);
    }

    /**
     * Возвращает отладочные действия, которые можно произвести с объектом
     *
     * @return \RS\Debug\Action\AbstractAction[]
     */
    public function getDebugActions()
    {
        return [
            new DebugAction\Edit(RouterManager::obj()->getAdminPattern('edit', [':id' => '{id}'], 'catalog-warehousectrl')),
            new DebugAction\Delete(RouterManager::obj()->getAdminPattern('del', [':chk[]' => '{id}'], 'catalog-warehousectrl'))
        ];
    }

    /**
     * Функция срабатывает перед записью объекта
     *
     * @param string $flag - флаг текущего действия. update или insert.
     * @return void
     */
    function beforeWrite($flag)
    {
        //Если задан склад по умолчанию
        if ($this['default_house'] == 1) {
            OrmRequest::make()
                ->update(new WareHouse())
                ->set([
                    'default_house' => 0,
                ])
                ->where([
                    'site_id' => $this['site_id']
                ])->exec();
        }

        //Если insert
        if ($flag == self::INSERT_FLAG) {
            //Добавим сортировку
            $this['sortn'] = OrmRequest::make()
                ->from(new WareHouse())
                ->select("MAX(sortn)+1 as sortn")
                ->where([
                    'site_id' => $this['site_id'],
                ])
                ->exec()->getOneField('sortn', 0);
        }

        //Проверим сколько у нас складов по умолчанию
        //Если нет, то назначим текущий
        $default_warehouse = WareHouseApi::getDefaultWareHouse();
        if (!$default_warehouse['id']) {
            $this['default_house'] = 1;
        }
        if ($this['default_house']) {
            $this['dont_change_stocks'] = 0;
        }

        if (empty($this['xml_id'])) {
            unset($this['xml_id']);
        }
    }

    /**
     * Получает координаты склада скленные с строку
     *
     * @param string $glue - строка, которой склеить координаты по умолчанию ";"
     * @return string
     */
    function getGlueCoords($glue = ';')
    {
        return $this['coor_x'] . $glue . $this['coor_y'];
    }

    /**
     * Удаление склада, возвращает флаг успеха
     *
     * @return bool
     */
    function delete()
    {
        //Удалим записи об остатках у товаров
        OrmRequest::make()
            ->delete()
            ->from(new Xstock())
            ->where([
                'warehouse_id' => $this['id']
            ])->exec();

        //Обновим остатки у комплектаций на складах
        $stock_sql = OrmRequest::make()
            ->select('SUM(stock) as stock')
            ->from(new Xstock(), 'XS')
            ->where('XS.offer_id = O.id')
            ->toSql();

        OrmRequest::make()
            ->from(new Offer(), "O")
            ->update()
            ->set('O.num = (' . $stock_sql . ')')
            ->exec();

        //Обновим общий остаток у товара на складах
        $stock_sql = OrmRequest::make()
            ->select('SUM(num) as num')
            ->from(new Offer(), 'O')
            ->where('O.product_id = P.id')
            ->where([
                'site_id' => SiteManager::getSiteId()
            ])
            ->where('O.`num` > 0')
            ->toSql();

        OrmRequest::make()
            ->from(new Product(), 'P')
            ->update()
            ->set('P.num = (' . $stock_sql . ')')
            ->exec();

        return parent::delete();
    }


    /**
     * Возвращает ссылку на страницу склада
     * @return string
     */
    function getUrl()
    {
        $alias = $this['alias'] ?: $this['id'];
        return RouterManager::obj()->getUrl('catalog-front-warehouse', ['id' => $alias]);
    }

    /**
     * Возвращает клонированный объект оплаты
     * @return Warehouse
     */
    function cloneSelf()
    {
        /** @var \Catalog\Model\Orm\Warehouse $clone */
        $clone = parent::cloneSelf();

        //Клонируем фото, если нужно
        if ($clone['image']) {
            /** @var \RS\Orm\Type\Image */
            $clone['image'] = $clone['__image']->addFromUrl($clone['__image']->getFullPath());
        }
        $clone['default_house'] = 0;
        unset($clone['xml_id']);
        unset($clone['alias']);
        return $clone;
    }
}
