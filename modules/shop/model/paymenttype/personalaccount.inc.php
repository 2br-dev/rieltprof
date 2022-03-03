<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\PaymentType;

use RS\Http\Request as HttpRequest;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Router\Manager as RouterManager;
use RS\View\Engine as ViewEngine;
use Shop\Model\Exception as ShopException;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Transaction;
use Shop\Model\TransactionApi;

/**
 * Способ оплаты - Лицевой счет
 */
class PersonalAccount extends AbstractType
{
    const SHORT_NAME = 'personalaccount';

    /**
     * Возвращает название расчетного модуля (типа доставки)
     *
     * @return string
     */
    function getTitle()
    {
        return t('Лицевой счет');
    }

    /**
     * Возвращает описание типа оплаты. Возможен HTML
     *
     * @return string
     */
    function getDescription()
    {
        return t('Оплата с лицевого счета');
    }

    /**
     * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
     *
     * @return string
     */
    function getShortName()
    {
        return self::SHORT_NAME;
    }

    /**
     * Возвращает ORM объект для генерации формы или null
     *
     * @return FormObject|null
     */
    function getFormObject()
    {
        $properties = new PropertyIterator([]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }

    /**
     * Возвращает true, если данный тип поддерживает проведение платежа через интернет
     *
     * @return bool
     */
    function canOnlinePay()
    {
        return true;
    }

    /**
     * Возвращает URL для перехода на сайт сервиса оплаты
     *
     * @param Transaction $transaction
     * @return string
     */
    function getPayUrl(Transaction $transaction)
    {
        $router = RouterManager::obj();
        return $router->getUrl('shop-front-mybalance', ['Act' => 'confirmpay', 'transaction_id' => $transaction->id]);
    }

    /**
     * Возвращает ID заказа исходя из REQUEST-параметров соотвествующего типа оплаты
     * Используется только для Online-платежей
     *
     * @param HttpRequest $request - входящий запрос
     * @return mixed
     */
    function getTransactionIdFromRequest(HttpRequest $request)
    {
        return $request->request('transaction_id', TYPE_INTEGER, false);
    }

    /**
     * Возвращает дополнительный персональный HTML для админ части в заказе
     *
     * @param Order $order - объект заказа
     * @return string
     * @throws \SmartyException
     */
    function getAdminHTML(Order $order)
    {
        $view = new ViewEngine();
        $view->assign([
            'order' => $order,
        ]);

        return $view->fetch('%shop%/form/payment/personalaccount_admin.tpl');
    }

    /**
     * Действие с запросами к заказу для исполнения определённой операции
     *
     * @param Order $order - объект заказа
     * @return array
     * @throws ShopException
     */
    function actionOrderQuery(Order $order)
    {
        $url = HttpRequest::commonInstance();
        $operation = $url->request('operation', TYPE_STRING);
        $cost = $order['totalcost'];

        if ($operation == 'orderpay') {
            if ($order['user_id'] <= 0) {
                $error = t('Заказ должен быть привязан к пользователю системы');
            } elseif ($order['is_payed']) {
                $error = t('Заказ уже был оплачен');
            } elseif ($order->getUser()->getBalance() < $cost) {
                $error = t('Недостаточно средств для оплаты заказа на лицевом счете пользователя');
            } else {
                $transaction_api = new TransactionApi();
                $transaction = $transaction_api->createTransactionFromOrder($order['order_num']);
                $result_text = $transaction->onResult($url);
                if ($transaction['status'] == Transaction::STATUS_SUCCESS) {

                    $order['is_payed'] = 1;
                    $order->update();

                    return [
                        'success' => true,
                        'messages' => [[
                            'text' => t('Успешно списано %summ с лицевого счета пользователя', [
                                'summ' => $cost
                            ]),
                        ]]
                    ];
                } else {
                    $error = $result_text;
                }
            }

            return [
                'success' => false,
                'messages' => [[
                    'text' => $error,
                    'options' => [
                        'theme' => 'error',
                    ],
                ]]
            ];
        }

        return [
            'success' => false,
            'messages' => [[
                'text' => t('Вызвана несуществующая операция'),
                'options' => [
                    'theme' => 'error',
                ],
            ]]
        ];
    }
}
