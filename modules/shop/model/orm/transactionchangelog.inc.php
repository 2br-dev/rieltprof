<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * Класс для логирования изменений в транзакциях
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $transaction_id id транзакции
 * @property string $date Дата изменения
 * @property string $change Изменение
 * @property string $entity_type Тип связанной сущности
 * @property integer $entity_id id связанной сущности
 * --\--
 */
class TransactionChangeLog extends OrmObject
{
    protected static $table = 'transaction_changelog';

    public function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'transaction_id' => (new Type\Integer())
                ->setDescription(t('id транзакции')),
            'date' => (new Type\Datetime())
                ->setDescription(t('Дата изменения')),
            'change' => (new Type\Varchar())
                ->setDescription(t('Изменение')),
            'entity_type' => (new Type\Varchar())
                ->setDescription(t('Тип связанной сущности')),
            'entity_id' => (new Type\Integer())
                ->setDescription(t('id связанной сущности')),
        ]);
    }

    /**
     * Статическое создание новой записи
     *
     * @param Transaction $transaction - транзакция
     * @param string $change - изменение
     * @param string|null $entity_type - тип связанной сущности
     * @param int|null $entity_id - id связанной сущности
     * @return void
     */
    public static function new(Transaction $transaction, string $change, string $entity_type = null, int $entity_id = null): void
    {
        $log = new self();
        $log['transaction_id'] = $transaction['id'];
        $log['date'] = date('Y:m:d H-i-s');
        $log['change'] = $change;
        $log['entity_type'] = $entity_type;
        $log['entity_id'] = $entity_id;
        $log->insert();
    }
}
