<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

/**
* Константы общего пользования для сбора статистики
*/
class StatisticEvents
{
    const TYPE_SALES_CART_SUBMIT            = 'SALES_CART_SUBMIT';
    const TYPE_SALES_FILL_ADDRESS           = 'SALES_FILL_ADDRESS';
    const TYPE_SALES_SELECT_DELIVERY        = 'SALES_SELECT_DELIVERY';
    const TYPE_SALES_SELECT_PAYMENT_METHOD  = 'SALES_SELECT_PAYMENT_METHOD';
    const TYPE_SALES_CONFIRM_ORDER          = 'SALES_CONFIRM_ORDER';

    /**
     * Возвращает список типов событий
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_SALES_CART_SUBMIT            => t('Начало оформления заказа'),
            self::TYPE_SALES_FILL_ADDRESS           => t('Указание адреса'),
            self::TYPE_SALES_SELECT_DELIVERY        => t('Выбор способа доставки'),
            self::TYPE_SALES_SELECT_PAYMENT_METHOD  => t('Выбор способа оплаты'),
            self::TYPE_SALES_CONFIRM_ORDER          => t('Подтверждение заказа'),
        ];
    }

}
