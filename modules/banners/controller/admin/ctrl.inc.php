<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Controller\Admin;

use Banners\Model\BannerApi;
use Banners\Model\Orm\Banner;
use Banners\Model\ZoneApi as BannerZoneApi;
use RS\Controller\Admin\Crud;
use RS\Html\Category;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar;
use RS\Html\Table;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use RS\Router\Manager as RouterManager;

class Ctrl extends Crud
{
    public $zone;

    function __construct()
    {
        parent::__construct(new BannerApi());
        $this->setCategoryApi(new BannerZoneApi(), t('зону'));
    }

    function actionIndex()
    {
        //Если категории не существует, то выбираем пункт "Все"
        if ($this->zone > 0 && !$this->getCategoryApi()->getById($this->zone)) $this->zone = 0;
        if ($this->zone > 0) $this->api->setFilter('zone_id', $this->zone);
        $this->getHelper()->setTopTitle(t('Баннеры'));

        return parent::actionIndex();
    }

    function helperIndex()
    {
        $collection = parent::helperIndex();

        $this->zone = $this->url->request('zone', TYPE_STRING);
        //Параметры таблицы
        $collection->setTopHelp(t('Используйте баннеры, чтобы в яркой форме донести до ваших пользователей важную информацию.
                                    Баннеры - это картинки, которые могут размещаться в баннерных зонах. Баннерная зона - это группа банеров.
                                    На сайте можно размещать баннерные зоны в удобных местах с помощью раздела <i>Веб-сайт &rarr; Конструктора сайта</i>.
                                    Устанавливая различный вес баннерам, можно управлять их сортировкой или вероятностью их отображения в определенных случаях.'));
        $collection->setTopToolbar($this->buttons(['add'], ['add' => t('добавить баннер')]));
        $collection['topToolbar']->addItem(new ToolbarButton\Dropdown([
            [
                'title' => t('Импорт/Экспорт'),
                'attr' => [
                    'class' => 'button',
                    'onclick' => "JavaScript:\$(this).parent().rsDropdownButton('toggle')"
                ]
            ],
            [
                'title' => t('Экспорт зон в CSV'),
                'attr' => [
                    'href' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'banners-zone', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Экспорт баннеров в CSV'),
                'attr' => [
                    'href' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'banners-banner', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт зон из CSV'),
                'attr' => [
                    'href' => RouterManager::obj()->getAdminUrl('importCsv', ['schema' => 'banners-zone', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт баннеров из CSV'),
                'attr' => [
                    'href' => RouterManager::obj()->getAdminUrl('importCsv', ['schema' => 'banners-banner', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ]
        ]), 'import');

        $collection->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['ThAttr' => ['width' => '20'], 'TdAttr' => ['align' => 'center']]),
                new TableType\Text('title', t('Название'), ['href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit'], 'Sortable' => SORTABLE_BOTH]),
                new TableType\Yesno('public', t('Публичный'), ['toggleUrl' => $this->router->getAdminPattern('ajaxTogglePublic', [':id' => '@id']), 'Sortable' => SORTABLE_BOTH]),
                new TableType\Text('link', t('Ссылка')),
                new TableType\Text('weight', t('Вес'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray']]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]
                    ]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('клонировать баннер'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ]
                        ],
                    ])
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ]
        ]));

        $collection->setCategory(new Category\Element([
            'sortIdField' => 'id',
            'activeField' => 'id',
            'activeValue' => $this->zone,
            'rootItem' => [
                'id' => 0,
                'title' => t('Все'),
                'noOtherColumns' => true,
                'noCheckbox' => true,
                'noDraggable' => true,
                'noRedMarker' => true
            ],
            'noExpandCollapseButton' => true,
            'sortable' => false,
            'mainColumn' => new TableType\Text('title', t('Название'), ['href' => $this->router->getAdminPattern(false, [':zone' => '@id'])]),
            'tools' => new TableType\Actions('id', [
                new TableType\Action\Edit($this->router->getAdminPattern('categoryEdit', [':id' => '~field~']), null, [
                    'attr' => [
                        '@data-id' => '@id'
                    ]
                ]),
                new TableType\Action\DropDown([
                    [
                        'title' => t('клонировать зону'),
                        'attr' => [
                            'class' => 'crud-add',
                            '@href' => $this->router->getAdminPattern('categoryClone', [':id' => '~field~']),
                        ]
                    ],
                ])
            ]),
            'headButtons' => [
                [
                    'attr' => [
                        'title' => t('Создать зону'),
                        'href' => $this->router->getAdminUrl('categoryAdd'),
                        'class' => 'add crud-add'
                    ]
                ]
            ],
        ]), $this->getCategoryApi());

        $collection->setCategoryBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Multiedit($this->router->getAdminUrl('categoryMultiEdit')),
                new ToolbarButton\Delete(null, null, [
                    'attr' => ['data-url' => $this->router->getAdminUrl('categoryDel')]
                ]),
            ],
        ]));

        $collection->setBottomToolbar($this->buttons(['multiedit', 'delete']));

        $collection->viewAsTableCategory();
        return $collection;
    }

    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        $zone_id = $this->url->request('zone', TYPE_INTEGER);
        /** @var Banner $obj */
        $obj = $this->api->getElement();

        if ($primaryKey === null) {
            if ($zone_id) {
                $obj['xzone'] = [$zone_id];
            }
        } else {
            $obj->fillZones();
        }
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }

    function actionAjaxTogglePublic()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }

        $id = $this->url->get('id', TYPE_STRING);

        $banner = $this->api->getOneItem($id);
        if ($banner) {
            $banner['public'] = !$banner['public'];
            $banner->update();
        }
        return $this->result->setSuccess(true);
    }
}
