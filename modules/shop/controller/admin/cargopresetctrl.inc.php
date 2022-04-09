<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Controller\Admin\Helper\CrudCollection;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Filter,
    \RS\Html\Table;
use RS\Html\Toolbar;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use \RS\Html\Toolbar\Button as ToolbarButton;
use Shop\Model\CargoPresetApi;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Transaction;


/**
 * Контроллер управления
 */
class CargoPresetCtrl extends \RS\Controller\Admin\Crud
{

    /**
     * @var \Shop\Model\ProductsReturnApi $api
     */
    public $api;

    /**
     * ReturnsCtrl constructor.
     */
    function __construct()
    {
        parent::__construct(new CargoPresetApi());
    }

    /**
     * Хелпер для страницы возвратов
     *
     * @return CrudCollection
     * @throws \RS\Exception
     */
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Справочник упаковок'));
        $helper->setTopHelp(t('В данном разделе можно создавать стандартные коробки, в которые вы обычно упаковывете товар'));

        $helper->setTopToolbar($this->buttons(['add']));
        $helper -> setTable(new Table\Element([
                'Columns' => [
                    new TableType\Checkbox('id', ['showSelectAll' => true]),
                    new TableType\Sort('sortn',  t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC, 'CurrentSort' => SORTABLE_ASC, 'ThAttr' => ['width' => '20']]),
                    new TableType\Text('title', t('Название коробки'), [
                        'Sortable' => SORTABLE_BOTH,
                        'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']
                    ]),
                    new TableType\Text('width', t('Ширина, мм')),
                    new TableType\Text('length', t('Длина, мм')),
                    new TableType\Text('height', t('Высота, мм')),
                    new TableType\Text('weight', t('Вес, грамм')),
                    new TableType\Text('id', t('№'), [
                        'Sortable' => SORTABLE_BOTH,
                        'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']
                    ]),
                    new TableType\Actions('id', [
                        //Опишем инструменты, которые нужно отобразить в строке таблицы пользователю
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                            'attr' => [
                                '@data-id' => '@id',
                            ]]),
                    ],
                        //Включим отображение кнопки настройки колонок в таблице
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    ),
                ],
                'TableAttr' => [
                    'data-sort-request' => $this->router->getAdminUrl('move')
                ]]
        ));

        //Опишем фильтр, который следует добавить
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container( [ //Контейнер визуального фильтра
                'Lines' =>  [
                    new Filter\Line( ['Items' => [ //Одна линия фильтров
                        new Filter\Type\Text('id', '№'), //Фильтр по ID
                        new Filter\Type\Text('title', t('Название'), ['searchType' => '%like%']),
                    ]
                    ]),
                ]
            ]),
            'Caption' => t('Поиск')
        ]));

        return $helper;
    }
}