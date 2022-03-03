<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Controller\Admin;

use RS\Controller\Admin\Crud;
use \RS\Html\Table\Type as TableType;
use \RS\Html\Filter;
use \RS\Html\Table;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryOrderApi;

/**
 * Контроллер заказов на доставку
 */
class DeliveryOrderCtrl extends Crud
{
    /**
     * ReturnsCtrl constructor.
     */
    function __construct()
    {
        parent::__construct(new DeliveryOrderApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Заказы на доставку'));
        $helper->setTopHelp(t('Здесь отображаются заказы на доставку. Здсь вы можете фильтровать и массово удалять ненужные заказы на доставку. Массовое удаление заказов не производит операций на стороне сервиса доставки.'));
        $helper->setTopToolbar(null);

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Text('id', t('№'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Text('number', t('Номер заказа на доставку'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('delivery_type', t('Способ доставки'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('creation_date', t('Дата создания'), ['Sortable' => SORTABLE_BOTH]),
            ],
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('id', '№'),
                        new Filter\Type\Text('number', t('Номер заказа на доставку'), ['searchType' => '%like%']),
                        new Filter\Type\Select('delivery_type', t('Способ доставки'), ['' => t('- Не важно -')] + DeliveryApi::getTypesAssoc()),
                        new Filter\Type\DateRange('creation_date', t('Дата создания')),
                    ]]),
                ],
            ]),
            'Caption' => t('Поиск по заказам'),
        ]));

        return $helper;
    }
}
