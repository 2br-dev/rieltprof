<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Controller\Admin;

use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use \RS\Html\Table\Type as TableType;
use \RS\Html\Filter;
use \RS\Html\Table;
use RS\Html\Toolbar;
use RS\Router\Manager as RouterManager;
use Shop\Model\CdekRegionApi;

class CdekRegionCtrl extends Crud
{
    /** @var CdekRegionApi $api */
    public $api;

    /**
     * ReturnsCtrl constructor.
     */
    function __construct()
    {
        parent::__construct(new CdekRegionApi());
    }

    /**
     * Хелпер для страницы регионов СДЭК
     *
     * @return CrudCollection
     */
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Загруженные регионы СДЭК'));
        $helper->setTopHelp(t('В данном разделе отображается список регионов из базы СДЭК. Список обновляется автоматически по крону или при нажатии на кнопку.'));

        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new Toolbar\Button\Button(RouterManager::obj()->getAdminUrl('updateCdekRegions', [], 'shop-tools'), t('Загрузить список регионов'), [
                    'attr' => [
                        'class' => 'btn-success crud-get',
                    ],
                ]),
            ],
        ]));
        $helper->setBottomToolbar(null);

        $helper -> setTable(new Table\Element([
            'Columns' => [
                new TableType\Text('code', t('Код СДЭК'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('city', t('Название населённого пункта'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('fias_guid', t('Уникальный идентификатор ФИАС'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('kladr_code', t('Код КЛАДР'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('country', t('Название страны'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('region', t('Название региона'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('sub_region', t('Название района региона'), ['Sortable' => SORTABLE_BOTH]),
            ],
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' =>  [
                    new Filter\Line([
                        'Items' => [
                            new Filter\Type\Text('code', t('Код СДЭК')),
                            new Filter\Type\Text('city', t('Название населённого пункта'), ['searchType' => '%like%']),
                            new Filter\Type\Text('fias_guid', t('Уникальный идентификатор ФИАС'), ['searchType' => '%like%']),
                            new Filter\Type\Text('kladr_code', t('Код КЛАДР'), ['searchType' => '%like%']),
                            new Filter\Type\Text('country', t('Название страны'), ['searchType' => '%like%']),
                            new Filter\Type\Text('region', t('Название региона'), ['searchType' => '%like%']),
                            new Filter\Type\Text('sub_region', t('Название района региона'), ['searchType' => '%like%']),
                        ],
                    ]),
                ],
            ]),
            'Caption' => t('Поиск'),
        ]));

        return $helper;
    }
}
