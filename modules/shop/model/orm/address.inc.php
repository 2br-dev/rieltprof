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
use RS\Site\Manager as SiteManager;
use Shop\Model\RegionApi;

/**
 * Адреса доставок пользователя
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $user_id Пользователь
 * @property integer $order_id Заказ пользователя
 * @property string $zipcode Индекс
 * @property string $country Страна
 * @property string $region Регион
 * @property string $city Город
 * @property string $address Адрес
 * @property string $street Улица
 * @property string $house Дом
 * @property string $block Корпус
 * @property string $apartment Квартира
 * @property string $entrance Подъезд
 * @property string $entryphone Домофон
 * @property string $floor Этаж
 * @property string $subway Станция метро
 * @property integer $city_id ID города
 * @property integer $region_id ID региона
 * @property integer $country_id ID страны
 * @property integer $deleted Удалён?
 * @property array $extra Дополнительные данные
 * @property string $_extra Дополнительные данные (упакованные)
 * --\--
 */
class Address extends OrmObject
{
    const EXTRA_CITY_KLADR_ID = 'city_kladr_id';
    const EXTRA_STREET_KLADR_ID = 'street_kladr_id';

    public static $city_selection_stage_fields = ['country_id', 'country', 'region_id', 'region', 'city_id', 'city'];

    protected static $table = 'order_address';

    protected $country_object;
    protected $region_object;
    protected $city_object;

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'user_id' => new Type\Integer([
                'maxLength' => '11',
                'default' => 0,
                'description' => t('Пользователь')
            ]),
            'order_id' => new Type\Integer([
                'maxLength' => '11',
                'default' => 0,
                'description' => t('Заказ пользователя')
            ]),
            'zipcode' => new Type\Varchar([
                'maxLength' => '20',
                'description' => t('Индекс')
            ]),
            'country' => new Type\Varchar([
                'maxLength' => '100',
                'description' => t('Страна')
            ]),
            'region' => new Type\Varchar([
                'maxLength' => '100',
                'description' => t('Регион')
            ]),
            'city' => new Type\Varchar([
                'maxLength' => '100',
                'description' => t('Город')
            ]),
            'address' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Адрес')
            ]),
            'street' => new Type\Varchar([
                'maxLength' => '100',
                'description' => t('Улица'),
            ]),
            'house' => new Type\Varchar([
                'maxLength' => 20,
                'description' => t('Дом')
            ]),
            'block' => new Type\Varchar([
                'maxLength' => 20,
                'description' => t('Корпус')
            ]),
            'apartment' => new Type\Varchar([
                'maxLength' => 20,
                'description' => t('Квартира')
            ]),
            'entrance' => new Type\Varchar([
                'maxLength' => 20,
                'description' => t('Подъезд')
            ]),
            'entryphone' => new Type\Varchar([
                'maxLength' => 20,
                'description' => t('Домофон')
            ]),
            'floor' => new Type\Varchar([
                'maxLength' => 20,
                'description' => t('Этаж')
            ]),
            'subway' => new Type\Varchar([
                'maxLength' => 20,
                'description' => t('Станция метро')
            ]),
            'city_id' => new Type\Integer([
                'description' => t('ID города')
            ]),
            'region_id' => new Type\Integer([
                'description' => t('ID региона')
            ]),
            'country_id' => new Type\Integer([
                'description' => t('ID страны')
            ]),
            'deleted' => new Type\Integer([
                'maxLength' => 1,
                'description' => t('Удалён?'),
                'default' => 0,
                'CheckboxView' => [1, 0],
            ]),
            'extra' => new Type\ArrayList([
                'description' => t('Дополнительные данные'),
            ]),
            '_extra' => new Type\Varchar([
                'description' => t('Дополнительные данные (упакованные)'),
                'maxLength' => 1000,
            ]),
        ]);
    }

    function beforeWrite($flag)
    {
        if ($this->isModified('extra')) {
            $this['_extra'] = json_encode($this['extra'], JSON_UNESCAPED_UNICODE);
        }
        $this->updateAddressTitles();
        $this->updateCityId();
    }

    /**
     * Вызывается после загрузки объекта
     *
     * @return void
     */
    public function afterObjectLoad()
    {
        $this['extra'] = json_decode($this['_extra'], true) ?? [];
    }

    /**
     * Корректирует название страны и региона в соответствии с id
     */
    function updateAddressTitles()
    {
        $regionApi = new RegionApi();
        $country = $this['country_id'] ? $regionApi->getOneItem($this['country_id']) : null;
        $region = $regionApi->getOneItem($this['region_id']);
        // Скорректируем название страны
        $this['country'] = $country ? $country['title'] : null;
        // Скорректируем область
        if (!empty($this['region_id']) && $region['parent_id'] == $country['id']) {
            $this['region'] = $region['title'];
        } else {
            $this['region_id'] = 0;
        }
    }

    /**
     * Корректирует id города в соответствии с его названием
     *
     * @return void
     */
    function updateCityId()
    {
        $regionApi = new RegionApi();

        $regionApi->setFilter('title', $this['city']);
        $regionApi->setFilter('parent_id', $this['region_id']);
        $regionApi->setFilter('site_id', SiteManager::getSiteId());
        $regionApi->setFilter('is_city', 1);
        $city = $regionApi->getFirst();
        $this['city_id'] = $city ? $city['id'] : null;
    }

    /**
     * Возвращает полный адрес в одну строку
     *
     * @param bool $full - если true, то возвращается полный адрес с индексом, страной, регионом, городом, полный адрес, ...
     * В противном случае возвращается только полный адрес, дом, корпус, подъезд, этаж, домофон.
     * @return string
     */
    function getLineView($full = true)
    {
        if ($full) {
            $keys = ['zipcode', 'country', 'region', 'city', 'address'];

            $parts = [];
            foreach ($this->getValues() as $key => $val) {
                if (in_array($key, $keys) && !empty($val)) $parts[] = $val;
            }
            $address_base = trim(implode(', ', $parts), ',');
        } else {
            $address_base = $this['address'];
        }

        $parts2 = [$address_base];
        if ($this['street']) $parts2[] = 'ул. ' . $this['street'];
        if ($this['house']) {
            $parts2[] = $this['house'] . ($this['block'] ? '/' . $this['block'] : '');
        }

        if ($this['apartment']) $parts2[] = 'кв./офис ' . $this['apartment'];
        if ($this['entrance']) $parts2[] = t('подъезд %0', [$this['entrance']]);
        if ($this['floor']) $parts2[] = t('этаж %0', [$this['floor']]);
        if ($this['entryphone']) $parts2[] = t('домофон %0', [$this['entryphone']]);

        return trim(implode(', ', $parts2), "\t\n\r,");
    }

    /**
     * Возвращает объект адреса созданный на основе региона
     *
     * @param Region $source - исходный регион
     * @return static
     */
    public static function createFromRegion(Region $source)
    {
        $address = new static();
        $city = null;
        $region = null;
        $country = null;

        if (!empty($source['id'])) {
            if ($source['is_city']) {
                $city = $source;
            } elseif ($source['parent_id'] == 0) {
                $country = $source;
            } else {
                $region = $source;
            }

            if ($city) {
                $region = $city->getParent();
                $address['city_id'] = $city['id'];
                $address['city'] = $city['title'];
                $address['zipcode'] = $city['zipcode'];
            }
            if ($region) {
                $country = $region->getParent();
                $address['region_id'] = $region['id'];
                $address['region'] = $region['title'];
            }
            $address['country_id'] = $country['id'];
            $address['country'] = $country['title'];
        }

        return $address;
    }

    /**
     * Возвращает название крайнего региона в адресе
     *
     * @return string
     */
    public function getLastRegionTitle(): string
    {
        if (!empty($this->getCity()['title'])) {
            return $this->getCity()['title'];
        } elseif (!empty($this->getRegion()['title'])) {
            return $this->getRegion()['title'];
        } elseif (!empty($this->getCountry()['title'])) {
            return $this->getCountry()['title'];
        }
        return t('Регион не выбран');
    }

    /**
     * Возвращает объект страны
     *
     * @return Region
     */
    function getCountry(): Region
    {
        if ($this->country_object === null || $this->country_object['id'] != $this['country_id']) {
            $this->country_object = new Region($this['country_id']);
            if (!$this->country_object['id']) {
                $this->country_object['title'] = $this['country'];
            }
        }
        return $this->country_object;
    }

    /**
     * Возвращает объект региона
     *
     * @return Region
     */
    function getRegion(): Region
    {
        if ($this->region_object === null || $this->region_object['id'] != $this['region_id']) {
            $this->region_object = new Region($this['region_id']);
            if (!$this->region_object['id']) {
                $this->region_object['title'] = $this['region'];
            }
        }
        return $this->region_object;
    }

    /**
     * Возвращает объект города
     *
     * @return Region
     */
    function getCity(): Region
    {
        if ($this->city_object === null || $this->city_object['id'] != $this['city_id']) {
            $this->city_object = new Region($this['city_id']);
            if (!$this->city_object['id']) {
                $this->city_object['title'] = $this['city'];
            }
        }
        return $this->city_object;
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
     * @param $value
     */
    public function setExtra(string $key, $value)
    {
        $this['extra'][$key] = $value;
    }
}
