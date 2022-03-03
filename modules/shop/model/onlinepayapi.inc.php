<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

class OnlinePayApi
{
    protected const SALT = 'p2yk7t';
    const TYPE_ORDER_PAY = 'order_pay';
    const TYPE_BALANCE_ADD_FOUNDS = 'balance_add_founds';
    const TYPE_SAVE_PAYMENT_METHOD = 'save_payment_method';

    public static function getPayParamsSign(array $params): string
    {
        return md5(serialize($params) . self::SALT . \Setup::$SECRET_KEY);
    }
}
