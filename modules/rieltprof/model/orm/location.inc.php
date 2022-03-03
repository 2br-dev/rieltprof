<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Rieltprof\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use Shop\Model\ZoneApi;
use Shop\Model\Orm\Zone;

/**
 * Регион доставки
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property integer $parent_id Родитель
 * @property string $zipcode Индекс
 * @property integer $is_city Является городом?
 * @property string $area Муниципальный район
 * @property integer $sortn Порядок
 * @property string $kladr_id ID по КЛАДР
 * @property string $russianpost_arriveinfo Срок доставки Почтой России (строка)
 * @property string $russianpost_arrive_min Минимальное количество дней доставки Почтой России
 * @property string $russianpost_arrive_max Максимальное количество дней доставки Почтой России
 * --\--
 */
class Location extends OrmObject
{
    protected static $table = 'reiltprof_location';
    
    function _init()
    {
        parent::_init()->append(array(
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar(array(
                    'description' => t('Название'),
                    'index' => true
                )),
                'parent_id' => new Type\Integer(array(
                    'description' => t('Родитель'),
                    'tree' => array(array('\Rieltprof\Model\LocationApi', 'staticTreeList'), 0, array(0 => t('- Верхний уровень -')))
                )),
                'zipcode' => new Type\Varchar(array(
                    'maxLength' => 20,
                    'visible' => false,
                    'cityVisible' => true,
                    'description' => t('Индекс'),
                )),
                'is_city' => new Type\Integer(array(
                    'maxLength' => 1,
                    'description' => t('Является городом?'),
                    'checkboxview' => array(1,0),
                    'visible' => false,
                    'default' => 0,
                )),
//                'area' => new Type\Varchar(array(
//                    'description' => t('Муниципальный район'),
//                    'visible' => false,
//                    'cityVisible' => true,
//                )),
                'sortn' => new Type\Integer(array(
                    'description' => t('Порядок'),
                    'default' => 100,
                    'hint' => t('Чем меньше число, тем выше элемент в списке. Если у двух элементов одинаковый порядок, то сортировка происходит по Наименованию в алфавитном порядке')
                )),
//                'kladr_id' => new Type\Varchar(array(
//                    'description' => t('ID по КЛАДР'),
//                    'hint' => t('Узнать можно на сайте kladr-rf.ru'),
//                )),
//                'type_short' => new Type\Varchar(array(
//                    'description' => t('Тип субъекта, населенного пункта сокращенно'),
//                    'hint' => t('Заполняется обычно только при автоматическом импорте регионов. Может использоваться, внутри системы, чтобы получить имя населенного пункта без обозначения типа.'),
//                    'maxLength' => 30
//                )),
//                'processed' => new Type\Integer(array(
//                    'description' => t('Обновлено только что'),
//                    'visible' => false
//                )),
//            t('Срок доставки'),
//                'russianpost_arriveinfo' => new Type\Varchar(array(
//                    'description' => t('Срок доставки Почтой России (строка)'),
//                    'visible' => false,
//                    'cityVisible' => true,
//                )),
//                'russianpost_arrive_min' => new Type\Varchar(array(
//                    'description' => t('Минимальное количество дней доставки Почтой России'),
//                    'maxLength' => 10,
//                    'visible' => false,
//                    'cityVisible' => true,
//                )),
//                'russianpost_arrive_max' => new Type\Varchar(array(
//                    'description' => t('Максимальное количество дней доставки Почтой России'),
//                    'maxLength' => 10,
//                    'visible' => false,
//                    'cityVisible' => true,
//                ))
        ));
        
        $this->addIndex(array('site_id', 'parent_id', 'is_city'));
    }
    
    /**
    * Удаление региона
    * 
    */
    function delete()
    {
        //Удаляем вместе с вложенными элементами
        if (parent::delete()) {
            $childs_id = $this->getChildsRecursive($this['id']);
            if ($childs_id) {
                OrmRequest::make()->delete()
                ->from($this)
                ->whereIn('id', $childs_id)
                ->exec();
            }
            return true;
        }
        return false;
    }  
    
    function getChildsRecursive($parent)
    {
        $ids = OrmRequest::make()->select('id')->from($this)
            ->where(array('parent_id' => $parent))
            ->exec()->fetchSelected(null, 'id');
        
        $result = $ids;
        foreach ($ids as $id) {
            $result = array_merge($result, $this->getChildsRecursive($id));
        }
        return $result;
    }

    /**
     * Возвращает объект родителя
     *
     * @return self
     */
    function getParent()
    {
        return new self($this['parent_id']);
    }  
    
    /**
    * Возвращает магистральные зоны
    */
    function getZones()
    {
        $zoneApi = new ZoneApi();
        $zone_ids = $zoneApi->getZonesByRegionId($this['id']);
        if(empty($zone_ids)){
            return array();
        }
        return OrmRequest::make()
            ->from(new Zone())
            ->whereIn('id', $zone_ids)
            ->objects();
    }

    /**
     * Действия перед записью объекта
     *
     * @param string $flag - insert или update
     * @return void
     */
    function beforeWrite($flag)
    {
        //Посмотрим родителя, чтобы посмотреть нужно ли выставлять признак города или нет.
        $this['is_city'] = 0;
        $parent = new Location($this['parent_id']);
        if ($parent['parent_id']){ //Если родитель это регион
            $this['is_city'] = 1;
        }
    }
}
