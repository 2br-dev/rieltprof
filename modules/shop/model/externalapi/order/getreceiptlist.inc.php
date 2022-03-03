<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Receipt;
use Shop\Model\Orm\Transaction;
use Shop\Model\ReceiptApi;
use Shop\Model\TransactionApi;
use \ExternalApi\Model\Exception as ApiException;

/**
 * Возвращает список чеков по заказу
 */
class GetReceiptList extends AbstractAuthorizedMethod
{
    const
        RIGHT_LOAD = 1,
        RIGHT_COURIER = 2,
        RIGHT_SELF = 3;

    /**
     * Возвращает комментарии к кодам прав доступа
     *
     * @return [
     *     КОД => КОММЕНТАРИЙ,
     *     КОД => КОММЕНТАРИЙ,
     *     ...
     * ]
     */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка чеков по заказам'),
            self::RIGHT_COURIER => t('Загрузка только чеков заказов курьера'),
            self::RIGHT_SELF => t('Загрузка чеков только по своим заказам')
        ];
    }

    /**
     * Возвращает список прав, требуемых для запуска метода API
     * По умолчанию для запуска метода нужны все права, что присутствуют в методе
     *
     * @return [код1, код2, ...]
     */
    public function getRunRights()
    {
        return [self::RIGHT_LOAD];
    }

    /**
     * Возвращает информацию о всех чеках по одному заказу. Это могут быть чеки оплаты, возврата, отгрузки
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     * @return array Возвращает информацию по чекам заказа
     * response.receipts.0.title - подпись для операции чека
     * response.receipts.0.id - ID чека
     * response.receipts.0.type - Тип чека (продажа, возврат, коррекция)
     * response.receipts.0.fiscal_number - Номер чека в смене
     * response.receipts.0.shift_number - Номер смены
     * response.receipts.0.date_time - Время регистрации чека
     * response.receipts.0.amount - Сумма в рублях
     * response.receipts.0.fn_number - Номер фискального накопителя
     * response.receipts.0.kkt_number - Номер ККТ
     * response.receipts.0.doc_number - Фискальный номер документа
     * response.receipts.0.fp_doc_number - Фискальный признак документа
     * response.receipts.0.ofd_url - URL для проверки чека
     * response.receipts.0.qr_code - Ссылка на qr код чека
     *
     *
     *
     * @example GET /api/methods/order.getReceiptList?token=467371ec1ce8634b53339abca65746c09fecd545&order_id=332
     * <pre>
     *   {
     *       "response": {
     *           "receipts": [
     *               {
     *                   "title": "Оплата заказа №875558",
     *                   "id": "76",
     *                   "type": "sell",
     *                   "fiscal_number": 415,
     *                   "shift_number": 23,
     *                   "date_time": "10.03.2020 16:47:00",
     *                   "amount": 48479,
     *                   "fn_number": "9999078900006242",
     *                   "kkt_number": "0000000005017793",
     *                   "doc_number": 57024,
     *                   "fp_doc_number": 647988149,
     *                   "ofd_url": "https://lk.platformaofd.ru/web/noauth/cheque?fn=9999078900006242&fp=647988149&i=57024",
     *                   "qr_code": "http://full.readyscript.local/qrcode/?data=t%3D2020-03-27MSK18%3A17%26s%3D48479%26fn%3D9999078900006242%26i%3D57024%26fp%3D647988149%26n%3D1&option%5Bw%5D=200&option%5Bh%5D=200&sign=c3086c45194dbd283ac861fd874f93a1ea780518"
     *               },
     *               {
     *                   "title": "Отгрузка заказа №875558",
     *                   "id": "77",
     *                   "type": "sell",
     *                   "fiscal_number": 427,
     *                   "shift_number": 22,
     *                   "date_time": "10.03.2020 16:52:00",
     *                   "amount": 48479,
     *                   "fn_number": "9999078900006190",
     *                   "kkt_number": "0000000005064564",
     *                   "doc_number": 56771,
     *                   "fp_doc_number": 2489253791,
     *                   "ofd_url": "https://lk.platformaofd.ru/web/noauth/cheque?fn=9999078900006190&fp=2489253791&i=56771",
     *                   "qr_code": "http://full.readyscript.local/qrcode/?data=t%3D2020-03-27MSK18%3A17%26s%3D48479%26fn%3D9999078900006190%26i%3D56771%26fp%3D2489253791%26n%3D1&option%5Bw%5D=200&option%5Bh%5D=200&sign=2f8976a7c0a67f1104fd07abc9c21882bd2efb42"
     *               }
     *           ]
     *       }
     *   }
     * </pre>
     *
     * @throws ApiException
     */
    function process($token, $order_id)
    {
        $order = new Order();
        if (!$order->load($order_id)) {
            throw new APIException(t('Заказ с таким order_id не найден'), ApiException::ERROR_METHOD_ACCESS_DENIED);
        }

        if ($this->checkAccessError(self::RIGHT_COURIER) === true
            && $order['courier_id'] != $this->token['user_id'])
        {
            throw new APIException(t('Данный заказ не назначен на данного курьера'), ApiException::ERROR_METHOD_ACCESS_DENIED);
        }

        if ($this->checkAccessError(self::RIGHT_SELF) === true
            && $order['user_id'] != $this->token['user_id'])
        {
            throw new APIException(t('Доступ к заказу запрещен'), ApiException::ERROR_METHOD_ACCESS_DENIED);
        }

        $transaction_api = new TransactionApi();
        //Выбираем все транзакции, по которым есть чеки
        $transaction_api->setFilter([
            'order_id' => $order['id'],
            'status' => Transaction::STATUS_SUCCESS
        ]);

        $transaction_api->setFilter('receipt', [Transaction::RECEIPT_SUCCESS, Transaction::RECEIPT_REFUND_SUCCESS], 'in');
        /**
         * @var Transaction
         */
        $transactions = $transaction_api->getList();
        $result = [];
        foreach ($transactions as $transaction) {
            $receipt_api = new ReceiptApi();
            $receipt_api->setFilter([
                'transaction_id' => $transaction['id'],
                'status' => Receipt::STATUS_SUCCESS
            ]);

            /**
             * @var $receipts Receipt[]
             */
            $receipts = $receipt_api->getList();
            foreach($receipts as $n => $receipt) {
                $info = $receipt->getReceiptInfo();
                $receipt_number = count($receipts) > 1 ? t(' .Чек №%0', [$n+1]) : '';
                $result[] = [
                    'title' => $transaction['reason'].$receipt_number,
                    'id' => $receipt['id'],
                    'type' => $receipt['type'],
                    'fiscal_number' => $info->getFiscalReceiptNumber(),
                    'shift_number' => $info->getShiftNumber(),
                    'date_time' => $info->getReceiptDatetime(),
                    'amount' => $info->getTotal(),
                    'fn_number' => $info->getFnNumber(),
                    'kkt_number' => $info->getEcrRegistrationNumber(),
                    'doc_number' => $info->getFiscalDocumentNumber(),
                    'fp_doc_number' => $info->getFiscalDocumentAttribute(),
                    'ofd_url' => $info->getReceiptOfdUrl(),
                    'qr_code' => $info->getQrCodeImageUrl(300, 300, true)
                ];
            }
        }

        return [
            'response' => [
                'receipts' => $result
            ]
        ];
    }
}