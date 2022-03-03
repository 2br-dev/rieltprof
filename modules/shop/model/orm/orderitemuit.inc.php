<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Orm\AbstractObject;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use Shop\Model\Marking\MarkingApi;
use Shop\Model\Marking\MarkingException;

/**
 * Позиция в корзине
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $order_id ID заказа
 * @property string $order_item_uniq ID позиции в рамках заказа
 * @property string $gtin Глобальный номер предмета торговли (GTIN)
 * @property string $serial Серийный номер
 * --\--
 */
class OrderItemUIT extends OrmObject
{
    protected static $table = 'order_item_uit';

    function _init()
    {
        parent::_init()->append([
            'order_id' => (new Type\Integer())
                ->setDescription(t('ID заказа')),
            'order_item_uniq' => (new Type\Varchar())
                ->setDescription(t('ID позиции в рамках заказа')),
            'gtin' => (new Type\Varchar())
                ->setDescription(t('Глобальный номер предмета торговли (GTIN)'))
                ->setMaxLength(14),
            'serial' => (new Type\Varchar())
                ->setDescription(t('Серийный номер'))
                ->setMaxLength(30),
        ]);
    }

    /**
     * Возвращает объект УИТ на основе массива данных
     *
     * @param string[] $data - данные УИТ
     * @return self
     * @throws MarkingException
     */
    public static function loadFromData(array $data)
    {
        $object = new self();
        $object['gtin'] = $data[MarkingApi::USE_ID_GTIN];
        $object['serial'] = $data[MarkingApi::USE_ID_SERIAL];
        $object->selfCheck();
        return $object;
    }

    /**
     * Возвращает данные УИТ в виде массива
     *
     * @return string[]
     */
    public function asArray()
    {
        return [
            MarkingApi::USE_ID_GTIN => $this['gtin'],
            MarkingApi::USE_ID_SERIAL => $this['serial'],
        ];
    }

    /**
     * Проверяет корректность данных кода
     *
     * @return void
     * @throws MarkingException
     */
    protected function selfCheck(): void
    {
        if (empty($this['gtin'])) {
            throw new MarkingException(t('Код не содержит "%0"', [MarkingApi::handbookUseIdTitleStr(MarkingApi::USE_ID_GTIN)]), MarkingException::ERROR_SINGLE_CODE_PARSE);
        } elseif (strlen($this['gtin']) < 8 || strlen($this['gtin']) > 14 || !is_numeric($this['gtin'])) {
            throw new MarkingException(t('Код содержит некорректный "%0" (%1)', [MarkingApi::handbookUseIdTitleStr(MarkingApi::USE_ID_GTIN), $this['gtin']]), MarkingException::ERROR_SINGLE_CODE_PARSE);
        }

        if (empty($this['serial'])) {
            throw new MarkingException(t('Код не содержит "%0"', [MarkingApi::handbookUseIdTitleStr(MarkingApi::USE_ID_SERIAL)]), MarkingException::ERROR_SINGLE_CODE_PARSE);
        } elseif (strlen($this['serial']) > 20) {
            throw new MarkingException(t('Код содержит некорректный "%0" (%1)', [MarkingApi::handbookUseIdTitleStr(MarkingApi::USE_ID_SERIAL), $this['serial']]), MarkingException::ERROR_SINGLE_CODE_PARSE);
        }
    }
}
