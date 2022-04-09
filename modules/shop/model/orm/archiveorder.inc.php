<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use Catalog\Model\CurrencyApi;
use Catalog\Model\Orm\WareHouse;
use RS\Helper\CustomView;
use RS\Orm\AbstractObject;
use RS\Orm\Request as OrmRequest;
use Users\Model\Orm\User;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $order_num Уникальный идентификатор номера заказа
 * @property integer $user_id ID покупателя
 * @property string $currency Трехсимвольный идентификатор валюты на момент оформления заказа
 * @property double $currency_ratio Курс относительно базовой валюты
 * @property string $currency_stitle Символ валюты
 * @property string $ip IP
 * @property integer $notify_user Уведомлять пользователя об изменении в заказе
 * @property integer $manager_user_id Менеджер заказа
 * @property integer $create_refund_receipt Выбить чек возврата
 * @property string $dateof Дата заказа
 * @property string $dateofupdate Дата обновления
 * @property float $totalcost Общая стоимость
 * @property float $profit Доход
 * @property float $user_delivery_cost Стоимость доставки, определенная администратором
 * @property integer $is_payed Заказ полностью оплачен?
 * @property integer $status Статус
 * @property string $admin_comments Комментарии администратора (не отображаются пользователю)
 * @property string $user_text Текст для покупателя
 * @property string $_serialized Дополнительные сведения
 * @property string $userfields Дополнительные сведения
 * @property array $extra 
 * @property string $hash 
 * @property integer $is_exported Выгружен ли заказ
 * @property string $delivery_order_id Идентификатор заказа доставки
 * @property string $delivery_shipment_id Идентификатор партии заказов доставки
 * @property string $track_number Трек-номер
 * @property integer $saved_payment_method_id Выбранный "сохранённый метод оплаты"
 * @property integer $trigger_cart_change Применить обработчики "изменений в корзине"
 * @property array $special_params Специальные параметры
 * @property string $user_type 
 * @property string $reg_fio Ф.И.О
 * @property string $reg_name Имя
 * @property string $reg_surname Фамилия
 * @property string $reg_midname Отчество
 * @property string $reg_phone Телефон
 * @property string $reg_login Логин
 * @property string $reg_e_mail E-mail
 * @property integer $reg_autologin 
 * @property string $reg_openpass Пароль
 * @property string $reg_pass2 
 * @property string $reg_company Наименование компании
 * @property string $reg_company_inn ИНН
 * @property string $login 
 * @property string $password 
 * @property string $contact_person Контактное лицо
 * @property integer $use_addr ID адреса доставки
 * @property integer $only_pickup_points Использовать только самовывоз
 * @property string $addr_country_id Страна
 * @property string $addr_country Страна
 * @property string $addr_region_id Область/Край
 * @property string $addr_region Область/край
 * @property string $addr_city_id id города
 * @property string $addr_city Город
 * @property string $addr_zipcode Индекс
 * @property string $addr_address Адрес
 * @property string $addr_street Улица
 * @property string $addr_house Дом
 * @property string $addr_block Корпус
 * @property string $addr_apartment Квартира
 * @property string $addr_entrance Подъезд
 * @property string $addr_entryphone Домофон
 * @property string $addr_floor Этаж
 * @property string $addr_subway Станция метро
 * @property array $addr_extra Дополнительные данные
 * @property array $userfields_arr Дополнительные сведения
 * @property integer $delivery Доставка
 * @property integer $courier_id Курьер
 * @property integer $warehouse Склад
 * @property integer $payment Тип оплаты
 * @property string $comments Комментарий
 * @property integer $substatus Причина отклонения заказа
 * @property string $user_fio Ф.И.О.
 * @property string $user_email E-mail
 * @property string $user_phone Телефон
 * @property string $user_login Логин
 * @property integer $user_autologin 
 * @property string $user_openpass Пароль
 * @property string $user_pass2 
 * @property integer $is_mobile_checkout Оформлен через мобильное приложение?
 * @property integer $register_user Зарегистрировать пользователя
 * @property array $regfields Дополнительные сведения
 * @property integer $true_weight Фактический вес заказа в граммах
 * @property integer $partner_id ID партнера
 * @property integer $retailcrm_id Какому заказу соответствует в RetailCRM
 * @property integer $is_exported_to_retailcrm Заказ экспортирован в RetailCRM
 * @property integer $retailcrm_payment_id ID способа оплаты для заказа в RetailCRM
 * @property integer $source_id Источник перехода пользователя
 * @property string $utm_source Рекламная система UTM_SOURCE
 * @property string $utm_medium Тип трафика UTM_MEDIUM
 * @property string $utm_campaign Рекламная кампания UTM_COMPAING
 * @property string $utm_term Ключевое слово UTM_TERM
 * @property string $utm_content Различия UTM_CONTENT
 * @property string $utm_dateof Дата события
 * @property integer $id_yandex_market_cpa_order ID заказа в Яндекс.маркете
 * --\--
 */
class ArchiveOrder extends AbstractObject
{
    protected static $table = 'archive_order';

    protected $products_count;

    function _init()
    {
        $properties = $this->getProperties();

        foreach ((new Order())->getProperties() as $key => $property) {
            $properties->append([
                $key => $property,
            ]);
        }
    }

    public function getProductsCount()
    {
        if ($this->products_count === null) {
            $this->products_count = (new OrmRequest())
                ->from(ArchiveOrderItem::_getTable())
                ->where(['order_id' => $this['id']])
                ->count();
        }
        return $this->products_count;
    }

    /**
     * Возвращает пользователя, оформившего заказ
     *
     * @return User
     */
    function getUser()
    {
        if ($this['user_id'] > 0) {
            return new User($this['user_id']);
        }
        $user = new User();

        //Парсит строку так: первое слово - фамилия, второе - имя, все остальное - отчество
        //Необходимо для тюркских отчеств, например, для Мамедов Ильгар Натиг Оглы, где Натиг Оглы - отчество
        preg_match('/^([^\s]+)\s*([^\s]+)?\s*(.+)?$/u', trim($this['user_fio']), $match);
        $user['surname'] = isset($match[1]) ? $match[1] : '';
        $user['name'] = isset($match[2]) ? $match[2] : '';
        $user['midname'] = isset($match[3]) ? $match[3] : '';

        $user['e_mail'] = $this['user_email'];
        $user['phone'] = $this['user_phone'];
        return $user;
    }

    /**
     * Возвращает объект способа доставки
     *
     * @return Delivery
     */
    function getDelivery()
    {
        return new Delivery($this['delivery']);
    }

    /**
     * Возвращает объект способа оплаты
     *
     * @return Payment
     */
    function getPayment()
    {
        return new Payment($this['payment']);
    }

    /**
     * Возвращает объект статуса заказа
     *
     * @return UserStatus
     */
    function getStatus()
    {
        return new UserStatus($this['status']);
    }

    /**
     * Возвращает объект выбранного склада
     *
     * @return WareHouse
     */
    function getWarehouse()
    {
        return new WareHouse($this['warehouse']);
    }

    /**
     * Возвращает общую стоимость заказа
     *
     * @param bool $format - Если true, то стоимость будет отформатирована
     * @param bool $use_currency - Если true, то стоимость будет возвращена, в валюте в которой оформлялся заказ
     * @return float|string
     */
    function getTotalPrice($format = true, $use_currency = false)
    {
        $price = $this['totalcost'];
        if ($use_currency) {
            $price = $this->applyMyCurrency($price);
        }

        if ($format) {
            $currency = $use_currency ? $this['currency_stitle'] : CurrencyApi::getBaseCurrency()['stitle'];
            $price = CustomView::cost($price, $currency);
        }
        return $price;
    }
}
