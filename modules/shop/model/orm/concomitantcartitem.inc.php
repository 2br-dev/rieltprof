<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare (strict_types=1);

namespace Shop\Model\Orm;

use RS\Exception as RSException;
use RS\Orm\Storage\AbstractStorage;
use RS\Orm\Storage\Stub as StorageStub;

/**
 * Сопутствующий товар в корзине
 * --/--
 * @property string $uniq ID в рамках одной корзины
 * @property string $type Тип записи товар, услуга, скидочный купон
 * @property string $entity_id ID объекта type
 * @property integer $offer Комплектация
 * @property string $multioffers Многомерные комплектации
 * @property float $amount Количество
 * @property string $title Название
 * @property string $extra Дополнительные сведения (сериализованные)
 * @property array $extra_arr Дополнительные сведения
 * @property integer $site_id ID сайта
 * @property string $session_id ID сессии
 * @property string $dateof Дата добавления
 * @property integer $user_id Пользователь
 * --\--
 */
class ConcomitantCartItem extends CartItem
{
    const SAVE_KEY_PRODUCT_ID = 'id';
    const SAVE_KEY_AMOUNT = 'amount';
    const SAVE_KEY_EXTRA_ARR = 'extra_arr';

    protected static $table = null;

    /**
     * Возвращает объект хранилища
     *
     * @return AbstractStorage
     */
    protected function getStorageInstance()
    {
        return new StorageStub($this);
    }

    /**
     * Загружает себя из массива
     *
     * @param CartItem $owner - товарная позиция, которой принадлежит данный сопутствующий товар
     * @param array $array - данные сопутствующего товара
     * @return ConcomitantCartItem
     * @throws RSException
     */
    public static function loadFromArray(CartItem $owner, array $array)
    {
        if (!isset($array[self::SAVE_KEY_PRODUCT_ID])) {
            throw new RSException(t('В данных для загрузки отсутствует id товара'));
        }

        $object = new self();
        $object['type'] = self::TYPE_PRODUCT;
        $object['entity_id'] = $array[self::SAVE_KEY_PRODUCT_ID];
        $object['amount'] = $array[self::SAVE_KEY_AMOUNT] ?? 1;
        $object['extra_arr'] = $array[self::SAVE_KEY_EXTRA_ARR] ?? [];
        $object['uniq'] = "concomitant-{$owner['uniq']}-{$object['entity_id']}";

        return $object;
    }

    /**
     * Сохраняет себя в виде массива
     *
     * @return array
     */
    public function saveInArray()
    {
        $this->saveDiscounts();
        $result = [
            self::SAVE_KEY_PRODUCT_ID => $this['entity_id'],
            self::SAVE_KEY_AMOUNT => $this['amount'],
            self::SAVE_KEY_EXTRA_ARR => $this['extra_arr'],
        ];

        return $result;
    }
}
