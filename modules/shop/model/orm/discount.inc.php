<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use Catalog\Model\CurrencyApi;
use Catalog\Model\ProductDialog;
use RS\Config\Loader as ConfigLoader;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\Helper\CustomView;
use RS\Helper\Tools as HelperTools;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * Скидочный купон
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property array $products Продукты
 * @property string $code Код
 * @property string $descr Описание скидки
 * @property integer $active Включен
 * @property string $sproducts Список товаров, на которые распространяется скидка
 * @property string $period Срок действия
 * @property string $endtime Время окончания действия скидки
 * @property float $min_order_price Минимальная сумма заказа
 * @property float $discount Скидка
 * @property string $discount_type Скидка указана в процентах или в базовой валюте?
 * @property integer $round Округлять скидку до целых чисел?
 * @property integer $uselimit Лимит использования, раз
 * @property integer $oneuserlimit Лимит использования одним пользователем, раз
 * @property integer $wasused Была использована, раз
 * @property integer $makecount Сгенерировать купонов
 * --\--
 */
class Discount extends OrmObject
{
    const PERIOD_FOREVER = 'forever';
    const PERIOD_TIMELIMIT = 'timelimit';
    const DISCOUNT_TYPE_PERCENT = '%';
    const DISCOUNT_TYPE_BASE_CURRENCY = 'base';

    protected static $table = 'order_discount';

    protected $serialized_products_field = 'sproducts';
    protected $products_field = 'products';


    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'products' => new Type\ArrayList([
                'description' => t('Продукты'),
                'template' => '%shop%/form/discount/products.tpl',
            ]),
            'code' => new Type\Varchar([
                'maxLength' => '50',
                'description' => t('Код'),
                'hint' => t('Данный код можно будет ввести в корзине и получить заданную скидку. Оставьте поле пустым, чтобы код был сгенерирован автоматически'),
                'Attr' => [['size' => '30']],
                'meVisible' => false,
            ]),
            'descr' => new Type\Varchar([
                'maxLength' => '2000',
                'description' => t('Описание скидки'),
            ]),
            'active' => new Type\Integer([
                'maxLength' => '1',
                'description' => t('Включен'),
                'CheckboxView' => ['1', '0'],
            ]),
            'sproducts' => new Type\Text([
                'description' => t('Список товаров, на которые распространяется скидка'),
                'visible' => false,
            ]),
            'period' => new Type\Enum(['timelimit', 'forever'], [
                'template' => '%shop%/form/discount/period.tpl',
                'description' => t('Срок действия'),
                'listFromArray' => [[
                    self::PERIOD_TIMELIMIT => t('Ограничен по времени'),
                    self::PERIOD_FOREVER => t('Вечный')
                ]]
            ]),
            'endtime' => new Type\Datetime([
                'description' => t('Время окончания действия скидки'),
                'visible' => false,
                'allowempty' => true,
            ]),
            'min_order_price' => new Type\Decimal([
                'maxLength' => 20,
                'decimal' => 2,
                'description' => t('Минимальная сумма заказа')
            ]),
            'discount' => new Type\Decimal([
                'template' => '%shop%/form/discount/discount.tpl',
                'maxLength' => 20,
                'decimal' => 2,
                'description' => t('Скидка'),
                'Attr' => [['size' => '8']],
                'checker' => ['chkEmpty', t('Укажите скидку')]
            ]),
            'discount_type' => new Type\Enum(['', self::DISCOUNT_TYPE_PERCENT, self::DISCOUNT_TYPE_BASE_CURRENCY], [
                'description' => t('Скидка указана в процентах или в базовой валюте?'),
                'listFromArray' => [[
                    self::DISCOUNT_TYPE_PERCENT => '%',
                    self::DISCOUNT_TYPE_BASE_CURRENCY => t('в базовой валюте'),
                ]],
                'visible' => false
            ]),
            'round' => new Type\Integer([
                'description' => t('Округлять скидку до целых чисел?'),
                'maxLength' => 1,
                'checkboxView' => [1, 0]
            ]),
            'uselimit' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Лимит использования, раз'),
                'hint' => t('Количество раз, которое можно использовать купон, 0 - неограниченно'),
                'Attr' => [['size' => '5']],
            ]),
            'oneuserlimit' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Лимит использования одним пользователем, раз'),
                'hint' => t('Количество раз, которое можно использовать купон, 0 - неограниченно<br/>
                           Действует только для зарегистрированых пользователей.<br/>
                           Если пользователь не зарегистрирован, то будет выдано <br/>
                           сообщение о авторизации'),
                'Attr' => [['size' => '5']],
            ]),
            'wasused' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Была использована, раз'),
                'Attr' => [['size' => '5']],
                'default' => 0,
                'allowempty' => false
            ]),
            'makecount' => new Type\Integer([
                'maxLength' => '11',
                'description' => t('Сгенерировать купонов'),
                'runtime' => true,
                'visible' => false,
                'hint' => t('Сгенерировать указанное число купонов с теми же параметрами, но разными кодами'),
                'Attr' => [['size' => '4']],
            ]),
        ])->addMultieditKey(['products', 'endtime']);

        $this->addIndex(['site_id', 'code'], self::INDEX_UNIQUE);
    }

    /**
     * Действия перед записью объекта
     *
     * @param string $flag - insert или update
     * @return boolean
     * @throws RSException
     */
    function beforeWrite($flag)
    {
        if (empty($this['code'])) {
            $this['code'] = $this->generateCode();
        }
        $this[$this->serialized_products_field] = serialize($this[$this->products_field]);
        return true;
    }

    /**
     * Генерирует код купона
     *
     * @return string
     * @throws RSException
     */
    function generateCode()
    {
        $config = ConfigLoader::byModule($this);
        return HelperTools::generatePassword($config['discount_code_len'], 'abcdefghkmnpqrstuvwxyz123456789');
    }

    function afterObjectLoad()
    {
        if (!empty($this[$this->serialized_products_field]) && $unserialize = unserialize($this[$this->serialized_products_field])) {
            $this[$this->products_field] = $unserialize;
        }
    }

    /**
     * Возвращает объект, с помошью которого можно визуализировать выбор товаров
     *
     * @return ProductDialog
     */
    function getProductDialog()
    {
        return new ProductDialog('products', false, $this['products']);
    }

    /**
     * Возвращает true, если активен, иначе - текст ошибки
     */
    function isActive()
    {
        //Скидка считается активной, если:
        //Она включена, срок действия еще не истек, количество использвания - не истекло.
        if ($this['active'] == 0) return t('Скидка не активна');
        if ($this['period'] == 'timelimit' && $this['endtime'] < date('Y-m-d H:i:s')) return t('Срок действия скидки истек');
        if ($this['uselimit'] && ($this['wasused'] >= $this['uselimit'])) return t('Достигнут лимит использования скидки');
        return true;
    }

    /**
     * Возвращает true, если купон распространяется на все товары
     */
    function isForAll()
    {
        return empty($this['products']) || @in_array('0', (array)$this['products']['group']);
    }

    /**
     * Возвращает сумму минимального заказа, к которому может быть прикрепленн купон
     *
     * @return float
     */
    function getMinOrderPrice()
    {
        return $this['min_order_price'];
    }

    /**
     * Увеличивает в базе счетчик использования на 1
     *
     * @return void
     */
    function incrementUse()
    {
        OrmRequest::make()
            ->update($this)
            ->set('wasused = wasused + 1')
            ->where("id = '#id'", ['id' => $this['id']])
            ->exec();
    }


    /**
     * Возвращает сумму скидки на цену $price
     *
     * @param float $price - сумма
     * @param bool $use_currency - скорректировать цену с учетом курса валюты относительно базовой валюты
     * @return float
     */
    function getDiscountValue($price, $use_currency)
    {
        //Определяем сколько вычитать.
        if ($this['discount_type'] == '%') {
            $delta = ($price * $this['discount'] / 100);
        } else {
            $delta = $this['discount'];
            if ($use_currency) {
                $delta = CurrencyApi::applyCurrency($delta);
            }
        }
        if ($this['round']) {
            $delta = round($delta);
        }

        return $delta;
    }

    /**
     * Возвращает величину скидки, отформатированную для отображения (всегда с учетом текущей валюты)
     *
     * @return string
     */
    function getDiscountTextValue()
    {
        if ($this['discount_type'] == '%') {
            $discount = (float)$this['discount'] . "%";
        } else {
            $discount = CurrencyApi::applyCurrency($this['discount']);
            $discount = CustomView::cost($discount) . ' ' . CurrencyApi::getCurrentCurrency()->stitle;
        }
        return $discount;
    }

    /**
     * Возвращает клонированный объект купона
     *
     * @return Discount
     * @throws EventException
     */
    function cloneSelf()
    {
        /** @var Discount $clone */
        $clone = parent::cloneSelf();
        unset($clone['wasused']);
        return $clone;
    }
}
