<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use Shop\Model\ZoneApi;

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
 * @property string $type_short Тип субъекта, населенного пункта сокращенно
 * @property integer $processed Обновлено только что
 * @property string $russianpost_arriveinfo Срок доставки Почтой России (строка)
 * @property string $russianpost_arrive_min Минимальное количество дней доставки Почтой России
 * @property string $russianpost_arrive_max Максимальное количество дней доставки Почтой России
 * @property integer $cdek_city_id ID населенного пункта в СДЭК
 * --\--
 */
class Region extends OrmObject
{
    protected static $table = 'order_regions';
    
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'description' => t('Название'),
                    'index' => true
                ]),
                'parent_id' => new Type\Integer([
                    'description' => t('Родитель'),
                    'tree' => [['\Shop\Model\RegionApi', 'staticTreeList'], 0, [0 => t('- Верхний уровень -')]]
                ]),
                'zipcode' => new Type\Varchar([
                    'maxLength' => 20,
                    'visible' => false,
                    'cityVisible' => true,
                    'description' => t('Индекс'),
                ]),
                'is_city' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Является городом?'),
                    'checkboxview' => [1,0],
                    'visible' => false,
                    'default' => 0,
                ]),
                'area' => new Type\Varchar([
                    'description' => t('Муниципальный район'),
                    'visible' => false,
                    'cityVisible' => true,
                ]),
                'sortn' => new Type\Integer([
                    'description' => t('Порядок'),
                    'default' => 100,
                    'hint' => t('Чем меньше число, тем выше элемент в списке. Если у двух элементов одинаковый порядок, то сортировка происходит по Наименованию в алфавитном порядке')
                ]),
                'fias_guid' => (new Type\Varchar())
                    ->setDescription('Идентификатор ФИАС')
                    ->setMaxLength(36),
                'kladr_id' => new Type\Varchar([
                    'description' => t('ID по КЛАДР'),
                    'hint' => t('Узнать можно на сайте kladr-rf.ru'),
                ]),
                'type_short' => new Type\Varchar([
                    'description' => t('Тип субъекта, населенного пункта сокращенно'),
                    'hint' => t('Заполняется обычно только при автоматическом импорте регионов. Может использоваться, внутри системы, чтобы получить имя населенного пункта без обозначения типа.'),
                    'maxLength' => 30
                ]),
                'processed' => new Type\Integer([
                    'description' => t('Обновлено только что'),
                    'visible' => false
                ]),
            t('Срок доставки'),
                'russianpost_arriveinfo' => new Type\Varchar([
                    'description' => t('Срок доставки Почтой России (строка)'),
                    'visible' => false,
                    'cityVisible' => true,
                ]),
                'russianpost_arrive_min' => new Type\Varchar([
                    'description' => t('Минимальное количество дней доставки Почтой России'),
                    'maxLength' => 10,
                    'visible' => false,
                    'cityVisible' => true,
                ]),
                'russianpost_arrive_max' => new Type\Varchar([
                    'description' => t('Максимальное количество дней доставки Почтой России'),
                    'maxLength' => 10,
                    'visible' => false,
                    'cityVisible' => true,
                ]),
            t('СДЭК'),
                'cdek_city_id' => new Type\Integer([
                    'description' => t('ID населенного пункта в СДЭК'),
                    'hint' => t('Используйте данное поле, чтобы принудительно установить связь со справочником СДЭК. В случае если поле пустое или значение 0, то будет происходить поиск соответствия по названию населенного пункта.'),
                    'visible' => false,
                    'cityVisible' => true
                ])
        ]);
        
        $this->addIndex(['site_id', 'parent_id', 'is_city']);
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
                //Удаляем вложенные регионы частями
                $ids_chunk = array_chunk($childs_id, 5000);
                foreach($ids_chunk as $ids) {
                    OrmRequest::make()->delete()
                        ->from($this)
                        ->whereIn('id', $ids)
                        ->exec();
                }

            }
            return true;
        }

        return false;
    }

    /**
     * Возвращает ID подрегионов. не более 3х уровней вложенности
     *
     * @param $parent
     * @param int $level
     * @return array
     */
    function getChildsRecursive($parent, $level = 0)
    {
        if ($level > 1) return []; //Нет смысла идти дальше 2-го уровня и запрашивать дочерние элементы у городов.

        $ids = OrmRequest::make()->select('id, is_city')->from($this)
            ->where(['parent_id' => $parent])
            ->exec()->fetchAll();
        
        $result = [];
        foreach ($ids as $data) {
            $result[] = $data['id'];
            if (!$data['is_city']) {
                $result = array_merge($result, $this->getChildsRecursive($data['id'], $level + 1));
            }
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
            return [];
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
        $parent = new Region($this['parent_id']);
        if ($parent['parent_id']){ //Если родитель это регион
            $this['is_city'] = 1;
        }
    }
}
