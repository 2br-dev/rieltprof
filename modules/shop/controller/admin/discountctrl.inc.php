<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Html\Table;
use Shop\Model\DiscountApi;
use Shop\Model\Orm\Discount;

/**
 * Контроллер Управление скидочными купонами
 */
class DiscountCtrl extends Crud
{
    /** @var DiscountApi */
    protected $api;

    public function __construct()
    {
        parent::__construct(new DiscountApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('Скидочный купон - это уникальный набор символов, который можно указать в корзине для получения скидки на заданные товары. Скидочный купон может иметь срок действия, что позволяет создавать ограниченные по времени рекламные кампании.'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('Добавить купон')]));
        $helper->setBottomToolbar($this->buttons(['multiEdit', 'delete']));
        $helper->setTopTitle(t('Купоны на скидку'));
        $helper->addCsvButton('shop-discount');

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('code', t('Код'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Usertpl('endtime', t('Истекает'), '%shop%/discount/endtime.tpl', ['Sortable' => SORTABLE_BOTH]),

                new TableType\Text('descr', t('Описание'), ['hidden' => true]),
                new TableType\Yesno('active', t('Включен'), ['toggleUrl' => $this->router->getAdminPattern('toggleCoupon', [':id' => '@id']), 'Sortable' => SORTABLE_BOTH]),
                new TableType\Usertpl('discount', t('Скидка'), '%shop%/discount/discount.tpl', ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('wasused', t('Использована'), ['Sortable' => SORTABLE_BOTH]),

                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]
                    ]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('Клонировать купон на скидку'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ]
                        ],
                    ]),
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ]
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('code', t('Код'), ['attr' => ['class' => 'w100']]),
                        new Filter\Type\Text('discount', t('Скидка'), ['showType' => true]),
                        new Filter\Type\Date('endtime', t('Истекает'), ['showType' => true])
                    ]])
                ]
            ]),
            'Caption' => t('Поиск по купонам')
        ]));


        return $helper;
    }

    /**
     * Добавление купонов
     *
     * @param mixed $primaryKey - id редактируемой записи
     * @param boolean $returnOnSuccess - Если true, то будет возвращать === true при успешном сохранении, иначе будет вызов стандартного _successSave метода
     * @param CrudCollection $helper - текуй хелпер
     * @return \RS\Controller\Result\Standard|bool
     */
    public function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        if ($primaryKey === null) {
            $this->getHelper()->setTopTitle(t('Добавить купон'));
            /** @var Discount $elem */
            $elem = $this->api->getElement();
            $elem['__makecount']->setVisible(true);
            $elem['active'] = 1;
            $elem['code'] = $elem->generateCode();
            $elem['uselimit'] = 1;
            $elem['round'] = 1;
        } else {
            $this->getHelper()->setTopTitle(t('Редактировать купон') . ' {code}');
        }
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }

    /**
     * Включить/выключить купон
     */
    public function actionToggleCoupon()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        $update_status = false;
        if ($coupon = $this->api->getOneItem($id)) {
            $coupon['active'] = !$coupon['active'];
            $update_status = $coupon->update();
        }
        return ($this->result->setSuccess($update_status));
    }

    /**
     * Мультиредактирование купонов
     */
    public function actionMultiEdit()
    {
        $doedit = $this->url->request('doedit', TYPE_ARRAY);

        if (in_array('type', $doedit)) $doedit[] = 'products';
        if (in_array('period', $doedit)) $doedit[] = 'endtime';
        $this->url->set('doedit', $doedit, POST);

        return parent::actionMultiEdit();
    }
}
