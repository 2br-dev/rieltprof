<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;

use Main\Model\FastLinkApi;
use RS\Html\Filter;
use RS\Html\Table\Type as TableType;
use RS\Html\Table;

/**
 * Контроллер управляет ссылками для виджета "Ссылки"
 */
class FastLinksCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        parent::__construct(new FastLinkApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Ссылки для быстрого перехода'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить ссылку')]));
        $helper->setTopHelp(t('В этом разделе вы можете управлять ссылками, которые будут отображаться в виджете "Ссылки". Создавайте ссылки, для быстрого перехода в нужный раздел сайта'));

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_ASC]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH,
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),
                    'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Usertpl('link', t('Ссылка'), '%main%/widget/fastlinks_link_column.tpl', ['Sortable' => SORTABLE_BOTH]),

                new TableType\Text('target', t('Окно'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Usertpl('icon', t('Иконка'), '%main%/widget/fastlinks_icon_column.tpl'),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]
        ]));

        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['items' => [
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('link', t('Ссылка'), ['SearchType' => '%like%']),
                    ]
                    ])
                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));

        return $helper;
    }

    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        $this->getHelper()->setTopTitle($primaryKey ? t('Редактировать ссылку {title}') : t('Добавить ссылку'));
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }

    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $direction = $this->url->request('flag', TYPE_STRING);
        return $this->result->setSuccess( $this->api->moveElement($from, $to, $direction) )->getOutput();
    }
}