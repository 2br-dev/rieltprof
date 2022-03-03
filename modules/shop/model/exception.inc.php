<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

/**
 * Исключения, связанные с модулем магазин
 */
class Exception extends \RS\Exception
{
    //Обработка транзакций
    const ERR_ORDER_NOT_FOUND = 1;
    const ERR_ORDER_ALREADY_PAYED = 2;
    const ERR_TRANSACTION_CREATION = 3;
    const ERR_BAD_PAYMENT_TYPE = 4;
    const ERR_BAD_PAYMENT_PARENT = 5;
    const ERR_NOT_ONLINE_PAYMENT = 6;
    const ERR_NO_TRANSACTION_ID = 7;
    const ERR_TRANSACTION_NOT_FOUND = 8;
    const ERR_ORDER_BAD_STATUS = 9;
    const ERR_DELIVERY_OTHER_ERROR = 100;
    const ERR_DELIVERY_CHECK_DATA_FAIL = 101;
    const ERR_DELIVERY_RESULT_ERROR = 102;
    const ERR_DELIVERY_API_ERROR = 200;
    const ERR_DELIVERY_API_CONNECT_ERROR = 201;
    const ERR_DELIVERY_API_AUTH_ERROR = 202;

    const ERROR_LIST_DELIVERY_API = [
        self::ERR_DELIVERY_API_ERROR,
        self::ERR_DELIVERY_API_CONNECT_ERROR,
        self::ERR_DELIVERY_API_AUTH_ERROR,
    ];
}
