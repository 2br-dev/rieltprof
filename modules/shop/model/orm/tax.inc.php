<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Orm\Exception as OrmException;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use Users\Model\Orm\User;

/**
 * Orm объект - налог
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property string $alias Идентификатор (Английские буквы или цифры)
 * @property string $description Описание
 * @property integer $enabled Включен
 * @property string $user_type Тип плательщиков
 * @property integer $included Входит в цену
 * @property integer $is_nds Это НДС
 * @property integer $sortn Порядок применения
 * @property array $rates 
 * --\--
 */
class Tax extends OrmObject
{
    protected static
        $table = 'order_tax',
        $cache_rates = [];
        
    
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'description' => t('Название')
                ]),
                'alias' => new Type\Varchar([
                    'description' => t('Идентификатор (Английские буквы или цифры)')
                ]),
                'description' => new Type\Varchar([
                    'description' => t('Описание')
                ]),
                'enabled' => new Type\Integer([
                    'description' => t('Включен'),
                    'checkboxView' => [1,0]
                ]),
                'user_type' => new Type\Enum(['all', 'user', 'company'], [
                    'description' => t('Тип плательщиков'),
                    'listFromArray' => [[
                        'all' => t('Все'),
                        'user' => t('Физические лица'),
                        'company' => t('Юридические лица')
                    ]]
                ]),
                'included' => new Type\Integer([
                    'description' => t('Входит в цену'),
                    'checkboxView' => [1,0]
                ]),
                'is_nds' => new Type\Integer([
                    'description' => t('Это НДС'),  
                    'checkboxView' => [1,0],
                    'default' => 1
                ]),
                'sortn' => new Type\Integer([
                    'description' => t('Порядок применения')
                ]),
                
            t('Ставки налога'),
                '__rates__' => new Type\UserTemplate('%shop%/form/tax/rates.tpl'),
                'rates' => new Type\ArrayList()
        ]);
    }


    /**
     * Налог включен в цену товаров
     *
     * @return integer
     */

    function isIncluded(){
        return $this->included;
    }

    /**
    * Действия после записью объекта
    * 
    * @param string $flag - insert или update
    */
    function afterWrite($flag)
    {
        //Сохраняем ставки налогов
        if ($this->isModified('rates')) {
            \RS\Orm\Request::make()->delete()->from(new TaxRate())->where([
                'tax_id' => $this['id']
            ])->exec();

            foreach($this['rates'] as $region_id => $rate) {
                $rate_object = new TaxRate();
                $rate_object['tax_id'] = $this['id'];
                $rate_object['region_id'] = $region_id;
                $rate_object['rate'] = (float)$rate;
                $rate_object->insert();
            }
        }
    }
    
    function fillRates()
    {
        if ($this['rates'] === null) {
            $this['rates'] = (array)\RS\Orm\Request::make()
                ->from(new TaxRate())
                ->where(['tax_id' => $this['id']])
                ->exec()
                ->fetchSelected('region_id', 'rate');
        }
    }
    
    /**
    * Возвращает регионы, к которым возможно применение налогов
    * 
    * @return array
    */
    function getTaxRegions()
    {
        $region_api = new \Shop\Model\RegionApi();
        return $this->prepareTaxRegions( $region_api->getTreeList(0));
    }
    
    /**
    * Рекурсивно обходит список регионов и формирует плоский список
    * 
    * @param array $all
    * @return array
    */
    protected function prepareTaxRegions($tree, $parent = '')
    {
        $result = [];
        foreach($tree as $item) {
            $item['fields']['display_title'] = $parent.$item['fields']['title'];
            $result[$item['fields']['id']] = $item['fields'];
            if (!empty($item['child'])) {
                $result += $this->prepareTaxRegions($item['child'], $parent.$item['fields']['title'].' > ' );
            }
        }
        return $result;
    }

    /**
     * Возвращает ставку налога, которую нужно применить, если налог активен и к адресу можно применить налог, иначе false
     *
     * @param mixed $user
     * @param Address $address
     * @return mixed
     */
    function canApply(User $user, Address $address)
    {
        if (!$this['enabled']) return false;
        if ($this['user_type'] == 'company' && !$user['is_company']) return false;
        if ($this['user_type'] == 'user' && $user['is_company']) return false;
        return $this->getRate($address) !== false;
    }

    /**
     * Возвращает ставку налога по адресу или false, в случае, если к данному адресу нет ставки
     *
     * @param Address $address
     * @return string|false
     */
    function getRate(Address $address)
    {
        $address_id = $address['country_id'] . ':' . $address['region_id'];
        if (!isset(self::$cache_rates[$this['id']][$address_id])) {
            $q = \RS\Orm\Request::make()
                ->select('rate')
                ->from(new TaxRate())->where([
                    'tax_id' => $this['id']
                ]);

            if ($address['city_id'] > 0) {
                $q->where("(region_id = '#country_id' OR region_id = '#region_id' OR region_id = '#city_id')", [
                    'country_id' => $address['country_id'],
                    'region_id' => $address['region_id'],
                    'city_id' => $address['city_id'],
                ]);
            } else if ($address['region_id'] > 0) {
                $q->where("(region_id = '#country_id' OR region_id = '#region_id')", [
                    'country_id' => $address['country_id'],
                    'region_id' => $address['region_id']
                ]);
            } else {
                $q->where("(region_id = '#country_id')", [
                    'country_id' => $address['country_id']
                ]);
            }
            $tax_rate = $q->object();
            self::$cache_rates[$this['id']][$address_id] = $tax_rate ? $tax_rate['rate'] : false;
        }
        return self::$cache_rates[$this['id']][$address_id];
    }
    
    /**
    * Возвращает название налога для счетов
    * 
    * @return string
    */
    function getTitle()
    {
        return $this['title'].($this['included'] ? t('(включен в стоимость)') : '' );
    }
    
    /**
    * Удаляет налог со всеми ставками
    * @return bool
    */
    function delete()
    {
        if ($result = parent::delete()) {
            \RS\Orm\Request::make()
                ->delete()
                ->from(new TaxRate())
                ->where([
                    'tax_id' => $this['id']
                ])
                ->exec();
        }
        return $result;
    }
    
    /**
    * Возвращает клонированный объект налога
    * @return Tax
    */
    function cloneSelf()
    {
        $this->fillRates();
        /**
        * @var \Shop\Model\Orm\Tax
        */
        $clone = parent::cloneSelf();
        return $clone;
    }
}
