<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\PaymentType;

use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar\Element as ToolbarElement;
use RS\Http\Request as HttpRequest;
use RS\Orm\Type;
use RS\View\Engine as ViewEngine;
use Shop\Model\Exception as ShopException;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\SavedPaymentMethod;
use Shop\Model\Orm\UserStatus;
use Shop\Model\SavedPaymentMethodApi;
use Shop\Model\UserStatusApi;
use Users\Model\Orm\User;

/**
 * Трейт рекуррентных платежей
 * Используется вместе с интерфейсом Shop\Model\PaymentType\InterfaceRecurringPayments
 */
trait TraitInterfaceRecurringPayments
{
    /**
     * Возвращает общие настройки рекуррентных платежей
     *
     * @return Type\AbstractType[]
     */
    final protected function getFormRecurringPaymentsProperties()
    {
        return [
            'recurring_type' => (new Type\Varchar())
                ->setDescription('Режим работы "рекуррентных платежей"')
                ->setListFromArray([
                    self::RECURRING_TYPE_NONE => [
                        'title' => t('Выключены'),
                        'description' => t('Рекуррентные платежи не доступны'),
                    ],
                    self::RECURRING_TYPE_SAVE_METHOD => [
                        'title' => t('Сохранять способ платежа'),
                        'description' => t('Позволяет оплачивать первый заказ и сохранять способ платежа, чтобы повторно его использовать в следующий раз. Данный вариант удобен для пользователя, так как позволяет при повторных заказах не вводить заново параметры способа платежа.'),
                    ],
                    self::RECURRING_TYPE_ONLY_SAVE_METHOD => [
                        'title' => t('Только привязывать способ платежа'),
                        'description' => t('Позволяет привязать способ платежа(с помощью оплаты и возврата 1 рубля) и запрещает пользователю напрямую оплачивать заказы. Способ удобен, если вы продаете весовые товары. <br>* В этом режиме способ оплаты становится недоступен для пополнения лицевого счёта'),
                    ],
                ])
                ->setDefault(self::RECURRING_TYPE_NONE)
                ->setTemplate('%shop%/form/payment/field_recurring_type.tpl'),
            'forbid_receipt_on_bind_method' => (new Type\Integer())
                ->setDescription(t('Не отправлять чек при привязке способа платежа'))
                ->setCheckboxView(1, 0),
        ];
    }

    /**
     * Возвращает включены ли рекуррентные платежи
     *
     * @return bool
     */
    public function isRecurringPaymentsActive(): bool
    {
        return $this->getRecurringPaymentsType() != InterfaceRecurringPayments::RECURRING_TYPE_NONE;
    }

    /**
     * Возвращает режим работы "рекуррентных платежей" (возможные значения описаны в интерфейсе)
     *
     * @return string
     */
    public function getRecurringPaymentsType(): string
    {
        return $this->getOption('recurring_type', self::RECURRING_TYPE_NONE);
    }

    /**
     * Возвращает список сохранённых способов оплаты для указанного пользователя
     *
     * @param User $user - объект пользователя
     * @return SavedPaymentMethod[]
     */
    public function getSavedPaymentMethods(User $user): array
    {
        $saved_method_api = new SavedPaymentMethodApi();
        $saved_method_api->setFilter([
            'user_id' => $user['id'],
            'payment_type' => $this->getShortName(),
            'payment_type_unique' => $this->getTypeUnique(),
            'deleted' => 0,
        ]);
        /** @var SavedPaymentMethod[] $saved_methods */
        $saved_methods = $saved_method_api->queryObj()->objects(null, 'id');
        return $saved_methods;
    }

    /**
     * Возвращает дополнительный HTML для админ части в заказе
     *
     * @param Order $order - заказ
     * @return string
     * @throws \SmartyException
     */
    public function getAdminRecurringPaymentsHtml(Order $order): string
    {
        $view = new ViewEngine();
        $view->assign([
            'order' => $order,
            'payment_type' => $this,
        ]);
        return $view->fetch('%shop%/form/order/payment_recurring_payments.tpl');
    }

    /**
     * Исполняет действие "рекуррентных платежей" с указанным заказом
     *
     * @param Order $order - заказ
     * @param string $action - действие
     * @return array
     * @throws ShopException
     */
    public function executeInterfaceRecurringPaymentsAction(Order $order, string $action): array
    {
        switch ($action) {
            case InterfaceRecurringPayments::RECURRING_ACTION_SAVED_METHODS_FORM:
                $saved_payment_methods = $this->getSavedPaymentMethods($order->getUser());
                if ($saved_payment_methods) {
                    $assign = [
                        'order' => $order,
                        'saved_payment_methods' => $saved_payment_methods,
                    ];

                    return [
                        'view_type' => 'form',
                        'title' => t('Выберите способ платежа'),
                        'assign' => $assign,
                        'template' => '%shop%/form/order/select_payment_method.tpl',
                        'bottom_toolbar' => new ToolbarElement([
                            'Items' => [
                                'save' => new ToolbarButton\SaveForm(null, t('Выбрать')),
                            ],
                        ]),
                    ];
                } else {
                    return [
                        'view_type' => 'message',
                        'message' => t('У пользователя нет сохранённых способов платежа'),
                    ];
                }
            case InterfaceRecurringPayments::RECURRING_ACTION_SELECT_SAVED_METHOD:
                $saved_payment_method_id = HttpRequest::commonInstance()->request('saved_payment_method_id', TYPE_INTEGER);
                $saved_method = new SavedPaymentMethod($saved_payment_method_id);

                if (!$saved_method['id'] || $saved_method['user_id'] != $order['user_id'] || $saved_method['payment_type'] != $this->getShortName() || $saved_method['payment_type_unique'] != $this->getTypeUnique()) {
                    throw new ShopException(t('Указан некорректный сохранённый способ оплаты'));
                }

                $order['saved_payment_method_id'] = $saved_payment_method_id;
                $order['status'] = UserStatusApi::getStatusIdByType(UserStatus::STATUS_PAYMENT_METHOD_SELECTED);
                $order->update();

                return [
                    'view_type' => 'message',
                    'message' => t('Способ платежа выбран'),
                ];
            case InterfaceRecurringPayments::RECURRING_ACTION_PAY_WITH_SAVED_METHOD:
                $saved_method = new SavedPaymentMethod($order['saved_payment_method_id']);

                if (!$saved_method['id'] || $saved_method['user_id'] != $order['user_id'] || $saved_method['payment_type'] != $this->getShortName() || $saved_method['payment_type_unique'] != $this->getTypeUnique()) {
                    throw new ShopException(t('Указан некорректный сохранённый способ оплаты'));
                }

                $this->recurringPayOrder($order, $saved_method);
                return [
                    'view_type' => 'message',
                    'message' => t('Заказ успешно оплачен'),
                ];
            default:
                throw new ShopException(t('Указанного действия не существует'));
        }
    }
}
