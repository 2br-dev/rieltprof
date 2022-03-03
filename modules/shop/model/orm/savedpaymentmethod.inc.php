<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;
use RS\Orm\Request as OrmRequest;
use Shop\Model\SavedPaymentMethodApi;
use Users\Model\Orm\User;

/**
 * Сохранённый способ платежа
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $external_id Внешний идентификатор способа платежа
 * @property string $type Тип способа платежа
 * @property string $subtype Подтип способа платежа
 * @property string $title Имя способа платежа
 * @property integer $user_id id полпользователя
 * @property string $payment_type Класс типа оплаты
 * @property string $payment_type_unique Идентификатор в рамках класса
 * @property string $save_date Дата сохранения
 * @property array $data Данные способа платежа
 * @property string $_data Данные способа платежа (сериализованные)
 * @property integer $is_default Способ по умолчанию
 * @property integer $deleted Удалён
 * --\--
 */
class SavedPaymentMethod extends OrmObject
{
    const TYPE_CARD = 'card';

    protected static $table = 'order_payment_saved_method';

    protected function _init()
    {
        parent::_init()->append([
            'external_id' => (new Type\Varchar())
                ->setDescription(t('Внешний идентификатор способа платежа')),
            'type' => (new Type\Varchar())
                ->setDescription(t('Тип способа платежа'))
                ->setAllowEmpty(false),
            'subtype' => (new Type\Varchar())
                ->setDescription(t('Подтип способа платежа'))
                ->setAllowEmpty(false),
            'title' => (new Type\Varchar())
                ->setDescription(t('Имя способа платежа'))
                ->setAllowEmpty(false),
            'user_id' => (new Type\Integer())
                ->setDescription(t('id полпользователя')),
            'payment_type' => (new Type\Varchar())
                ->setDescription(t('Класс типа оплаты')),
            'payment_type_unique' => (new Type\Varchar())
                ->setDescription(t('Идентификатор в рамках класса')),
            'save_date' => (new Type\Datetime())
                ->setDescription('Дата сохранения'),
            'data' => (new Type\ArrayList())
                ->setDescription(t('Данные способа платежа')),
            '_data' => (new Type\Varchar())
                ->setDescription(t('Данные способа платежа (сериализованные)'))
                ->setMaxLength(1000),
            'is_default' => (new Type\Integer())
                ->setDescription(t('Способ по умолчанию'))
                ->setCheckboxView(1, 0)
                ->setMaxLength(1)
                ->setDefault(0)
                ->setAllowEmpty(false),
            'deleted' => (new Type\Integer())
                ->setDescription(t('Удалён'))
                ->setCheckboxView(1, 0)
                ->setMaxLength(1)
                ->setDefault(0)
                ->setAllowEmpty(false),
        ]);
    }

    /**
     * Вызывается после сохранения объекта в storage
     *
     * @param string $save_flag - тип операции (insert|update|replace)
     * @return void
     */
    public function afterWrite($save_flag)
    {
        if ($this['is_default']) {
            (new OrmRequest())
                ->update($this)
                ->set(['is_default' => 0])
                ->where([
                    'user_id' => $this['user_id'],
                    'payment_type' => $this['payment_type'],
                    'payment_type_unique' => $this['payment_type_unique'],
                ])
                ->where('id != #0', [$this['id']])
                ->exec();

            if ($this['deleted']) {
                $this->setOtherDefaultPayementType(true);
            }
        }
    }

    /**
     * Вызывается перед сохранением объекта в storage
     * Если возвращено false, то сохранение не произойдет
     *
     * @param string $save_flag - тип операции (insert|update|replace)
     * @return null|false
     */
    public function beforeWrite($save_flag)
    {
        if ($this->isModified('data')) {
            $this['_data'] = serialize($this['data']);
        }

        if ($save_flag == self::INSERT_FLAG) {
            $this['save_date'] = date('Y-m-d H:i:s');
        }

        $api = SavedPaymentMethodApi::getInstance();
        $api->setFilter([
            'user_id' => $this['user_id'],
            'payment_type' => $this['payment_type'],
            'payment_type_unique' => $this['payment_type_unique'],
            'deleted' => 0,
            'is_default' => 1,
        ]);
        if (!$api->getListCount()) {
            $this['is_default'] = 1;
        }

        return null;
    }

    /**
     * Вызывается после загрузки объекта
     *
     * @return void
     */
    public function afterObjectLoad()
    {
        if (!empty($this['_data'])) {
            $this['data'] = unserialize($this['_data']) ?: [];
        }
    }

    /**
     * Возвращает пользователя, у которому привязан данный способ платежа
     *
     * @return User
     */
    public function getUser(): User
    {
        static $users = [];
        if (!isset($users[$this['user_id']])) {
            $users[$this['user_id']] = new User($this['user_id']);
        }
        return $users[$this['user_id']];
    }

    /**
     * Возвращает тип спосба платежа
     *
     * @return string
     */
    public function getType(): string
    {
        static $type_list;
        if ($type_list === null) {
            $type_list = [
                self::TYPE_CARD => t('Банковская карта'),
            ];
        }
        return $type_list[$this['type']] ?? $this['type'];
    }

    /**
     * Удаляет запись
     *
     * @return bool|void
     */
    public function delete()
    {
        $is_default = $this['is_default'];

        if ($result = parent::delete() && $is_default) {
            $this->setOtherDefaultPayementType();
        }

        return $result;
    }


    /**
     * Устанавливает другой способ оплаты в качестве способа по умолчанию
     *
     * @param bool $update_this Убрать is_default у текущего элемента
     */
    public function setOtherDefaultPayementType($update_this = false)
    {
        Request::make()
            ->update($this)
            ->set('is_default = 1')
            ->where('id != "#id"', ['id' => $this['id']])
            ->where([
                'payment_type_unique' => $this['payment_type_unique'],
                'deleted' => 0
            ])
            ->limit(1)
            ->exec();

        if ($update_this) {
            Request::make()
                ->update($this)
                ->set('is_default = 0')
                ->where(['id' => $this['id']])
                ->exec();
        }
    }
}
