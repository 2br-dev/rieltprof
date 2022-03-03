<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Model\Orm\Status;
use Crm\Model\StatusApi;
use RS\Controller\Admin\Crud;
use RS\Html\Category;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Filter;
use RS\Html\Table;

/**
 * Контроллер управления статусами объектов CRM
 */
class StatusCtrl extends Crud
{
    protected $dir;

    function __construct()
    {
        parent::__construct(new StatusApi());
        $this->setCategoryApi(new StatusApi());

        $dir_keys = array_keys(Status::getObjectTypeAliases());
        $this->dir = $this->url->convert($this->url->request('dir', TYPE_STRING, $dir_keys[0]), $dir_keys);
    }

    /**
     * Формирует хелпер для отображения списка статусов
     *
     * @return \RS\Controller\Admin\Helper\CrudCollection
     */
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Статусы'));

        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить статус')]));

        $helper->setTopHelp(t('Управляйте статусами объектов CRM на этой странице.'));
        $helper->setBottomToolbar($this->buttons(['delete']));
        $helper->viewAsTableCategory();

        $helper->addCsvButton('crm-status');

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC, 'CurrentSort' => SORTABLE_ASC, 'ThAttr' => ['width' => '20']]),

                new TableType\Usertpl('title', t('Название'), '%crm%/admin/table/status_color.tpl', ['LinkAttr' => ['class' => 'crud-edit'], 'Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id', 'dir' => $this->dir])]),
                new TableType\Text('alias', t('Идентификатор'), ['LinkAttr' => ['class' => 'crud-edit'], 'Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id', 'dir' => $this->dir])]),

                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~', 'dir' => $this->dir]), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]
                    ]),
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]
        ]));

        $helper->setCategoryListFunction('getObjectTypesCategoryList');
        $helper->setCategory(new Category\Element([
            'noCheckbox' => true,
            'activeField' => 'id',
            'activeValue' => $this->dir,
            'sortable' => false,
            'mainColumn' => new TableType\Text('title', t('Название'), ['href' => $this->router->getAdminPattern(false, [':dir' => '@id', 'c' => $this->url->get('c', TYPE_ARRAY)])]),
            'headButtons' => [
                [
                    'text' => t('Объекты'),
                    'tag' => 'span',
                    'attr' => [
                        'class' => 'lefttext'
                    ]
                ],
            ],
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                    ]
                    ])
                ]]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()],
            'AddParam' => ['hiddenfields' => ['dir' => $this->dir]],
            'Caption' => t('Поиск по статусам')
        ]));

        return $helper;
    }

    /**
     * Отображает список статусов
     *
     * @return mixed
     */
    function actionIndex()
    {
        $this->api->setFilter('object_type_alias', $this->dir);
        return parent::actionIndex();
    }

    /**
     * Добавляет статус
     *
     * @param null $primaryKeyValue
     * @param bool $returnOnSuccess
     * @param null $helper
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        /** @var @var Status $element */
        $status = $this->api->getElement();
        $status['object_type_alias'] = $this->dir;
        $this->user_post_data = [
            'object_type_alias' => $this->dir
        ];

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * Перемещает элемент
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $direction = $this->url->request('flag', TYPE_STRING);
        return $this->result->setSuccess($this->api->moveElement($from, $to, $direction));
    }
}
