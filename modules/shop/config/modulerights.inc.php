<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Config;

use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Right;
use RS\AccessControl\RightGroup;

class ModuleRights extends DefaultModuleRights
{
    const RIGHT_ADD_FUNDS = 'add_funds';
    const RIGHT_TRANSACTION_ACTIONS = 'transaction_actions';
    const RIGHT_SEND_RECEIPT = 'send_receipt';
    const RIGHT_CORRECTION_RECEIPT = 'correction_receipt';
    const RIGHT_REFUND_RECEIPT = 'refund_receipt';

    /**
     * Возвращает древовидный список собственных прав модуля
     *
     * @return (Right|RightGroup)[]
     */
    protected function getSelfModuleRights()
    {
        return [
            new Right(self::RIGHT_READ, t('Чтение')),
            new Right(self::RIGHT_CREATE, t('Создание')),
            new Right(self::RIGHT_UPDATE, t('Изменение')),
            new Right(self::RIGHT_DELETE, t('Удаление')),
            new Right(self::RIGHT_ADD_FUNDS, t('Начисление средств')),
            new Right(self::RIGHT_TRANSACTION_ACTIONS, t('Действия с транзакциями')),
            new RightGroup('group_receipt', t('Операции с чеками'), [
                new Right(self::RIGHT_SEND_RECEIPT, t('Отправка чека')),
                new Right(self::RIGHT_CORRECTION_RECEIPT, t('Отправка чека коррекции')),
                new Right(self::RIGHT_REFUND_RECEIPT, t('Отправка чека возврата')),
            ]),
        ];
    }
}
