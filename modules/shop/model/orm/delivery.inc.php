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
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\AbstractType as AbstractDeliveryType;
use Shop\Model\ZoneApi;

/**
 * Способ доставки текущего сайта, присутствующий в списке выбора при оформлении заказа.
 * Содержит связь с модулем расчета.
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property string $admin_suffix Пояснение
 * @property string $description Описание
 * @property string $picture Логотип
 * @property integer $parent_id Категория
 * @property array $xzone Зоны
 * @property integer $free_price Сумма заказа для бесплатной доставки
 * @property integer $first_status Стартовый статус заказа
 * @property string $user_type Категория пользователей для данного способа доставки
 * @property double $extrachange_discount Наценка/скидка на доставку
 * @property integer $extrachange_discount_type Тип скидки или наценки
 * @property double $extrachange_discount_implementation Наценка/скидка расчитывается от стоимости
 * @property integer $public Публичный
 * @property integer $default По умолчанию
 * @property string $payment_method Признак способа расчета для чека
 * @property string $class Расчетный класс (тип доставки)
 * @property string $_serialized Параметры расчетного класса
 * @property array $data 
 * @property integer $sortn Сорт. индекс
 * @property string $min_price Минимальная сумма заказа
 * @property string $max_price Максимальная сумма заказа
 * @property string $min_weight Минимальный вес заказа
 * @property string $max_weight Максимальный вес заказа
 * @property integer $min_cnt Минимальное количество товаров в заказе
 * @property array $delivery_periods Сроки доставки в регионы
 * @property string $_delivery_periods Сроки доставки в регионы (Сохранение данных)
 * @property string $_tax_ids Налоги (сериализованные)
 * @property array $tax_ids Налоги
 * --\--
 */
class Delivery extends OrmObject
{
    protected static $table = 'order_delivery';

    protected $cache_delivery;

    public function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Название'),
                ]),
                'admin_suffix' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Пояснение'),
                    'hint' => t('Отображается только в диалогах административной части<br>
                                    используйте если у вас есть доставки с одинаковым названем')
                ]),
                'description' => new Type\Text([
                    'description' => t('Описание'),
                ]),
                'picture' => new Type\Image([
                    'max_file_size' => 10000000,
                    'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'],
                    'description' => t('Логотип'),
                ]),
                'parent_id' => new Type\Integer([
                    'description' => t('Категория'),
                    'default' => 0,
                    'allowEmpty' => false,
                    'list' => [['\Shop\Model\DeliveryDirApi', 'staticSelectList'], 0, [0 => t('- Без группы -')]]
                ]),
                'xzone' => new Type\ArrayList([
                    'description' => t('Зоны'),
                    'list' => [['\Shop\Model\DeliveryApi', 'getZonesList']],
                    'attr' => [[
                        'size' => 10,
                        'multiple' => true,
                    ]],
                ]),
                'free_price' => new Type\Integer([
                    'description' => t('Сумма заказа для бесплатной доставки'),
                    'hint' => t('Если указать "0" - условие применятся не будет'),
                    'default' => 0,
                    'allowEmpty' => false,
                ]),
                'first_status' => new Type\Integer([
                    'description' => t('Стартовый статус заказа'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('По умолчанию (как у способа оплаты)')]],
                ]),
                'user_type' => new Type\Enum(['all', 'user', 'company'], [
                    'allowEmpty' => false,
                    'description' => t('Категория пользователей для данного способа доставки'),
                    'listFromArray' => [[
                        'all' => t('Все'),
                        'user' => t('Физические лица'),
                        'company' => t('Юридические лица')
                    ]]
                ]),
                'extrachange_discount' => new Type\Real([
                    'description' => t('Наценка/скидка на доставку'),
                    'hint' => t('Положительное число - наценка, число с минусом, это скидка. <br/> Например: -100'),
                    'template' => '%shop%/form/delivery/extrachangediscount.tpl',
                    'maxLength' => 11,
                    'decimal' => 4,
                    'default' => 0,
                ]),
                'extrachange_discount_type' => new Type\Integer([
                    'description' => t('Тип скидки или наценки'),
                    'maxLength' => 1,
                    'listFromArray' => [[
                        0 => t('ед.'),
                        1 => '%',
                    ]],
                    'default' => 0,
                    'visible' => false
                ]),

                'extrachange_discount_implementation' => new Type\Real([
                    'description' => t('Наценка/скидка расчитывается от стоимости'),
                    'hint' => t('Актуально если наценка/скидка указана в %'),
                    'listFromArray' => [[
                        0 => t('Стоимости товаров в заказе'),
                        1 => t('Доставки'),
                        2 => t('Суммы Доставки и Cтоимости товаров в заказе'),
                    ]],
                    'default' => 1,
                ]),

                'public' => new Type\Integer([
                    'description' => t('Публичный'),
                    'maxLength' => 1,
                    'default' => 1,
                    'checkboxView' => [1, 0]
                ]),
                'default' => new Type\Integer([
                    'description' => t('По умолчанию'),
                    'maxLength' => 1,
                    'default' => 0,
                    'checkboxView' => [1, 0],
                    'hint' => t('Включение данной опции у доставки, требующей указания дополнительных параметров,
                                    совместно с настройкой "Не показывать шаг оформления заказа - доставка?"<br>
                                    может привести к ошибкам.')
                ]),
                'payment_method' => new Type\Varchar([
                    'description' => t('Признак способа расчета для чека'),
                    'hint' => t('Если будет задан, то будет перекрывать все остальные настройки (в оплате и настройках модуля)'),
                    'list' => [['\Shop\Model\CashRegisterApi', 'getStaticPaymentMethods'], [0 => t('По умолчанию')]],
                    'allowEmpty' => false,
                    'default' => 0,
                ]),
                'class' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Расчетный класс (тип доставки)'),
                    'meVisible' => false,
                    'template' => '%shop%/form/delivery/other.tpl',
                    'list' => [['\Shop\Model\DeliveryApi', 'getTypesAssoc']]
                ]),
                '_serialized' => new Type\Text([
                    'description' => t('Параметры расчетного класса'),
                    'visible' => false,
                ]),
                'data' => new Type\ArrayList([
                    'visible' => false
                ]),
                'sortn' => new Type\Integer([
                    'maxLength' => '11',
                    'allowEmpty' => true,
                    'description' => t('Сорт. индекс'),
                    'visible' => false,
                ]),
            t('Дополнительные условия показа'),
                'min_price' => new Type\Decimal([
                    'decimal' => 2,
                    'maxLength' => 20,
                    'description' => t('Минимальная сумма заказа'),
                    'phpType' => 'string',
                    'hint' => t('Условие при котором, будет показываться доставка.<br/>Пустое поле - условие не действует.'),
                ]),
                'max_price' => new Type\Decimal([
                    'decimal' => 2,
                    'maxLength' => 20,
                    'description' => t('Максимальная сумма заказа'),
                    'phpType' => 'string',
                    'hint' => t('Условие при котором, будет показываться доставка.<br/>Пустое поле - условие не действует.'),
                ]),
                'min_weight' => new Type\Real([
                    'description' => t('Минимальный вес заказа'),
                    'phpType' => 'string',
                    'hint' => t('Указывается в единицах измерения веса товаров, которые указаны в настройках модля Каталог. Условие при котором, будет показываться доставка.<br/>Пустое поле - условие не действует.'),
                ]),
                'max_weight' => new Type\Real([
                    'description' => t('Максимальный вес заказа'),
                    'phpType' => 'string',
                    'hint' => t('Указывается в единицах измерения веса товаров, которые указаны в настройках модля Каталог. Условие при котором, будет показываться доставка.<br/>Пустое поле - условие не действует.'),
                ]),
                'min_cnt' => new Type\Integer([
                    'description' => t('Минимальное количество товаров в заказе'),
                    'default' => 0,
                    'allowEmpty' => false,
                    'hint' => t('Условие при котором, будет показываться доставка.<br/>0 - условие не действует.'),
                ]),
            t('Срок доставки'),
                'delivery_periods' => new Type\ArrayList([
                    'description' => t('Сроки доставки в регионы'),
                    'list' => [['\Shop\Model\DeliveryApi', 'getZonesList']],
                    'meVisible' => false,
                    'template' => '%shop%/form/delivery/delivery_periods.tpl'
                ]),
                '_delivery_periods' => new Type\Text([
                    'description' => t('Сроки доставки в регионы (Сохранение данных)'),
                    'visible' => false
                ]),
            t('Налоги'),
                '_tax_ids' => new Type\Varchar([
                    'description' => t('Налоги (сериализованные)'),
                    'visible' => false,
                ]),
                'tax_ids' => new Type\ArrayList([
                    'description' => t('Налоги'),
                    'list' => [['\Shop\Model\TaxApi', 'staticSelectList']],
                    'attr' => [[
                        'size' => 5,
                        'multiple' => true,
                    ]],
                ]),
        ]);
    }

    /**
     * Действия перед записью объекта
     *
     * @param string $flag - insert или update
     * @return void
     */
    public function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = OrmRequest::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->where([
                        'site_id' => $this['site_id'],
                    ])
                    ->exec()->getOneField('max', 0) + 1;
        }
        if (empty($this['xzone'])) {
            $this['xzone'] = [0];
        }
        $this['_serialized'] = serialize($this['data']);

        $null_fields = ['min_price', 'max_price', 'min_weight', 'max_weight'];
        foreach($null_fields as $field) {
            if ($this[$field] === '') {
                $this[$field] = null;
            }
        }

        // Сохранить заданные строки доставки

        if ($this->isModified('delivery_periods')) {
            $this['_delivery_periods'] = serialize($this['delivery_periods']);
        }
        if ($this->isModified('tax_ids')) {
            $this['_tax_ids'] = serialize($this['tax_ids']);
        }
    }

    /**
     * Действия после записи объекта
     *
     * @param string $flag - insert или update
     * @return void
     */
    public function afterWrite($flag)
    {
        // Удаляем старые связи с зонами
        $this->deleteZones();

        // Записываем новые зоны
        if (is_array($this['xzone'])) {
            if (array_search(0, $this['xzone']) !== false) {
                $this['xzone'] = [0];
            }
            foreach ($this['xzone'] as $zone_id) {
                $link = new DeliveryXZone();
                $link['delivery_id'] = $this['id'];
                $link['zone_id'] = $zone_id;
                $link->insert();
            }
        }
    }

    /**
     * Удалить все связи этой доставки с зонами
     *
     * @return void
     */
    public function deleteZones()
    {
        OrmRequest::make()->delete()
            ->from(new DeliveryXZone())
            ->where(['delivery_id' => $this['id']])
            ->exec();
    }

    /**
     * Заполнить поле xzone массивом идентификаторов зон
     *
     * @return void
     */
    public function fillZones()
    {
        $zones = OrmRequest::make()->select('zone_id')
            ->from(new DeliveryXZone())
            ->where(['delivery_id' => $this['id']])
            ->exec()->fetchSelected(null, 'zone_id');
        $this['xzone'] = $zones;
        if (empty($zones)) {
            $this['xzone'] = [0];
        }
    }

    /**
     * Возвращает клонированный объект доставки
     *
     * @return Delivery
     */
    public function cloneSelf()
    {
        $clone = parent::cloneSelf();

        //Клонируем фото, если нужно
        if ($clone['picture']) {
            /** @var Type\Image $picture_field */
            $picture_field = $clone['__picture'];
            $clone['picture'] = $picture_field->addFromUrl($picture_field->getFullPath());
        }
        return $clone;
    }

    /**
     * Действия полсле загрузки объекта
     *
     * @return void
     */
    public function afterObjectLoad()
    {
        $this['data'] = @unserialize($this['_serialized']);
        $this['delivery_periods'] = @unserialize($this['_delivery_periods']);
        $this['tax_ids'] = @unserialize($this['_tax_ids']);
    }

    /**
     * Производит валидацию текущих данных в свойствах
     *
     * @return bool Возвращает true, если нет ошибок, иначе - false
     */
    public function validate()
    {
        $this->getTypeObject()->validate($this);
        return parent::validate();
    }

    /**
     * Возвращает объект расчетного класса (типа доставки)
     *
     * @return AbstractDeliveryType | false
     */
    public function getTypeObject()
    {
        if ($this->cache_delivery === null) {
            $this->cache_delivery = clone DeliveryApi::getTypeByShortName($this['class']);
            $this->cache_delivery->setDelivery($this)->loadOptions((array)$this['data']);
        }

        return $this->cache_delivery;
    }

    /**
     * Возвращает HTML для указазния параметров доставки
     *
     * @param Order $order - заказ
     * @return string
     * @throws \SmartyException
     */
    public function getDeliveryParamsHtml(Order $order): string
    {
        return $this->getTypeObject()->getDeliveryParamsHtml($order);
    }

    /**
     * Возвращает дополнительный HTML для публичной части,
     * если например нужен виджет с выбором для забора посылки
     *
     * @param Order $order - объект заказа
     * @return string
     */
    public function getAddittionalHtml(Order $order = null)
    {
        $delivery_type = $this->getTypeObject();
        return $delivery_type->getAddittionalHtml($this, $order);
    }

    /**
     * Возвращает стоимость доставки
     *
     * @param Order $order текущий заказ пользователя
     * @param Address $address объект адреса доставки
     * @param bool $use_currency применять валюту заказа
     * @return string
     * @throws RSException
     */
    public function getDeliveryCost(Order $order, Address $address = null, $use_currency = true)
    {
        return $this->getTypeObject()->getDeliveryFinalCost($order, $address, $use_currency);
    }

    /**
     * Возвращает стоимость доставки в текстовом виде всегда в валюте заказа
     *
     * @param Order $order текущий заказ пользователя
     * @param Address $address объект адреса доставки
     * @return string
     * @throws RSException
     */
    public function getDeliveryCostText(Order $order, Address $address = null)
    {
        return $this->getTypeObject()->getDeliveryCostText($order, $address, $this);
    }

    /**
     * Возвращает дополнительный произвольный текст для данной доставки (обычно срок доставки)
     *
     * @param Order $order
     * @param Address|null $address
     * @return string
     */
    public function getDeliveryExtraText(Order $order, Address $address = null)
    {
        $type_obj = $this->getTypeObject();
        $text = $type_obj->getDeliveryExtraText($order, $address, $this);

        //Если доставка не вернула из её типа дополнительной информации, то посмотрим на указанную конкретно у самой доставки
        //Если такая имеется
        if (empty($text) && !empty($this['delivery_periods'])) {
            //Получим все зоны
            $zone_api = new ZoneApi();
            $zones = $zone_api->getZonesByRegionId($address['region_id'], $address['country_id'], $address['city_id']);
            foreach ($this['delivery_periods'] as $delivery_period) {
                if (empty($zones) && ($delivery_period['zone'] == 0)) { //Если зона все
                    $text = $delivery_period['text'] . ' ' . $delivery_period['days_min'] . ' ' . $delivery_period['days_max'];
                } elseif (!empty($zones) && ($delivery_period['zone'] == 0)) {
                    $text = $delivery_period['text'] . ' ' . $delivery_period['days_min'] . ' ' . $delivery_period['days_max'];
                } else {
                    if (in_array($delivery_period['zone'], $zones)) {
                        $text = $delivery_period['text'] . ' ' . $delivery_period['days_min'] . ' ' . $delivery_period['days_max'];
                    }
                }
                if (!empty($text)) { //Если срок найден.
                    break;
                }
            }
        }

        return $text;
    }
}
