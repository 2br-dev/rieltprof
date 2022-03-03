<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Controller\Admin;

use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Rights;
use RS\Controller\Admin\Crud;
use RS\Controller\Result\Standard;
use RS\Exception as RSException;
use \RS\Html\Table\Type as TableType;
use \RS\Html\Filter;
use \RS\Html\Table;
use Shop\Model\SavedPaymentMethodApi;


/**
 * Class Возврат товара
 * @package Shop\Controller\Admin
 */
class SavedPaymentMethodsCtrl extends Crud
{
    /**
     * ReturnsCtrl constructor.
     */
    function __construct()
    {
        parent::__construct(new SavedPaymentMethodApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Сохранённые способы платежа'));
        $helper->setTopHelp(t('Здесь отображаются все сохраненные пользователями карты ли иные способы платежа. Если вы установите флажок Удален напротив карты, то карта исчезнет из списка доступных у пользователя.'));
        $helper->setTopToolbar(null);
        $helper->setBottomToolbar(null);

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Text('id', t('№'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Usertpl('user_id', t('Пользователь'), '%shop%/order_user_cell.tpl', [
                    'allowLinks' => true,
                ]),
                new TableType\Text('type', t('Тип'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('subtype', t('Подтип'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('title', t('Номер'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\StrYesno('is_default', t('По умолчанию'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Yesno('deleted', t('Удалён'), ['Sortable' => SORTABLE_BOTH, 'toggleUrl' => $this->router->getAdminPattern('ajaxToggleDeleted', [':id' => '@id'])]),
            ],
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('id', '№'),
                        new Filter\Type\User('user_id', t('Пользователь')),
                        new Filter\Type\Text('title', t('Номер'), ['searchType' => '%like%']),
                        new Filter\Type\Select('deleted', t('Удален'), [
                            '' => t('Не важно'),
                            1 => t('Да'),
                            0 => t('Нет')
                        ]),

                    ]]),
                ],
            ]),
            'Caption' => t('Поиск по способам платежа'),
        ]));

        return $helper;
    }

    /**
     * Переключает флаг "Удалён"
     *
     * @return Standard
     * @throws RSException
     */
    function actionAjaxToggleDeleted()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }
        $id = $this->url->get('id', TYPE_STRING);

        $saved_payment_method = $this->api->getOneItem($id);
        if ($saved_payment_method) {
            $saved_payment_method['deleted'] = !$saved_payment_method['deleted'];
            $saved_payment_method->update();
        }
        return $this->result->setSuccess(true);
    }
}
