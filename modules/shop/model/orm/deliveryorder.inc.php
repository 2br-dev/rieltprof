<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Exception as RSException;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\InterfaceDeliveryOrder;

/**
 * Заказ на доставку
 */
class DeliveryOrder extends OrmObject
{
    protected static $table = 'delivery_order';

    function _init()
    {
        parent::_init()->append([
            'external_id' => (new Type\Varchar())
                ->setDescription(t('Внешний идентификатор')),
            'order_id' => (new Type\Integer())
                ->setDescription(t('id заказа')),
            'delivery_type' => (new Type\Varchar())
                ->setDescription(t('Расчётный клас доставки'))
                ->setList('Shop\Model\DeliveryApi::getTypesAssoc'),
            'number' => (new Type\Varchar())
                ->setDescription(t('Номер заказа на доставку')),
            'data' => (new Type\ArrayList())
                ->setDescription(t('Сохранённые данные')),
            '_data' => (new Type\Varchar())
                ->setDescription(t('Сохранённые данные (сериализованные)'))
                ->setMaxLength(10000),
            'extra' => (new Type\ArrayList())
                ->setDescription(t('Дополнительные данные')),
            '_extra' => (new Type\Varchar())
                ->setDescription(t('Дополнительные данные (сериализованные)'))
                ->setMaxLength(5000),
            'creation_date' => (new Type\Datetime())
                ->setDescription(t('Дата создания')),
            'address' => (new Type\Varchar())
                ->setDescription(t('Адрес на который создан заказ')),
        ]);
    }

    /**
     * Возвращает значение для поля "Адрес на который создан заказ"
     *
     * @param Order $order - заказ
     * @return string
     */
    public function getAddressValue(Order $order): string
    {
        return $order->getAddress()->getLineView();
    }

    /**
     * Возвращает список данных заказа на доставку
     *
     * @return array
     * @throws RSException
     */
    public function getDataLines(): array
    {
        $delivery_type = DeliveryApi::getTypes()[$this['delivery_type']] ?? null;

        if (!$delivery_type || !($delivery_type instanceof InterfaceDeliveryOrder)) {
            return [];
        }

        return $delivery_type->getDeliveryOrderDataLines($this);
    }

    /**
     * Возвращает список действий, доступных для заказа на доставку
     *
     * @return array
     * @throws RSException
     */
    public function getActions()
    {
        $delivery_type = DeliveryApi::getTypes()[$this['delivery_type']] ?? null;

        if (!$delivery_type || !($delivery_type instanceof InterfaceDeliveryOrder)) {
            return [];
        }

        return $delivery_type->getDeliveryOrderActions($this);
    }

    /**
     * Возвращает трек-номер заказа на доставку
     *
     * @return string|null
     * @throws RSException
     */
    public function getDeliveryOrderTrackNumber(): ?string
    {
        $delivery_type = DeliveryApi::getTypes()[$this['delivery_type']] ?? null;

        if (!$delivery_type || !($delivery_type instanceof InterfaceDeliveryOrder)) {
            return null;
        }

        return $delivery_type->getDeliveryOrderTrackNumber($this);
    }

    /**
     * Вызывается после загрузки объекта
     *
     * @return void
     */
    public function afterObjectLoad()
    {
        $this['data'] = @unserialize($this['_data']) ?: [];
        $this['extra'] = @unserialize($this['_extra']) ?: [];
    }

    /**
     * Вызывается перед сохранением объекта в storage
     * Если возвращено false, то сохранение не произойдет
     *
     * @param string $save_flag - тип операции (insert|update|replace)
     * @return void|false
     */
    public function beforeWrite($save_flag)
    {
        if ($this->isModified('data')) {
            $this['_data'] = serialize($this['data']);
        }
        if ($this->isModified('extra')) {
            $this['_extra'] = serialize($this['extra']);
        }

        if ($save_flag === self::INSERT_FLAG) {
            $this['creation_date'] = date('Y-m-d H:i:s');
        }
    }

    /**
     * Возвращает дополнительные данные
     *
     * @param string $key - ключ данных
     * @param mixed $default - значение по умолчанию
     * @return mixed
     */
    public function getExtra(string $key, $default = null)
    {
        return $this['extra'][$key] ?? $default;
    }

    /**
     * Устанавливает дополнительные данные
     *
     * @param string $key - ключ данных
     * @param mixed $value - значение
     * @return void
     */
    public function setExtra(string $key, $value): void
    {
        $extra = $this['extra'];
        $extra[$key] = $value;
        $this['extra'] = $extra;
    }

    /**
     * Удаляет дополнительные данные
     *
     * @param string $key - ключ данных
     * @return void
     */
    public function removeExtra(string $key): void
    {
        if (isset($this['extra'][$key])) {
            $extra = $this['extra'];
            unset($extra[$key]);
            $this['extra'] = $extra;
        }
    }
}
