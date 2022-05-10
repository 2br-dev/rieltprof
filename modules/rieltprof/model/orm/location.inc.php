<?php
namespace Rieltprof\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

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
                'is_city' => new Type\Integer(array(
                    'maxLength' => 1,
                    'description' => t('Является городом?'),
                    'checkboxview' => array(1,0),
                    'visible' => false,
                    'default' => 0,
                )),
                'is_county' => new Type\Integer(array(
                    'maxLength' => 1,
                    'description' => t('Является городом?'),
                    'checkboxview' => array(1,0),
                    'visible' => false,
                    'default' => 0,
                )),
                'is_district' => new Type\Integer(array(
                    'maxLength' => 1,
                    'description' => t('Является городом?'),
                    'checkboxview' => array(1,0),
                    'visible' => false,
                    'default' => 0,
                )),
                'sortn' => new Type\Integer(array(
                    'description' => t('Порядок'),
                    'default' => 100,
                    'hint' => t('Чем меньше число, тем выше элемент в списке. Если у двух элементов одинаковый порядок, то сортировка происходит по Наименованию в алфавитном порядке')
                )),
                'has_county' => new Type\Integer([
                    'description' => t('Есть округа'),
                    'maxLength' => 1,
                    'checkboxView' => [1,0],
                    'default' => 0
                ]),
                'no_district' => new Type\Integer([
                    'description' => t('Нет районов'),
                    'maxLength' => 1,
                    'checkboxView' => [1,0],
                    'default' => 0
                ]),
                'public' => new Type\Integer([
                    'description' => t('Отображать для выбора'),
                    'maxLength' => 1,
                    'checkboxView' => [1,0],
                    'default' => 1
                ])
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
     * Действия перед записью объекта
     *
     * @param string $flag - insert или update
     * @return void
     */
    function beforeWrite($flag)
    {
        //Посмотрим родителя, чтобы посмотреть нужно ли выставлять соответствующи признак (город, округ, район).
        $this['is_city'] = 0;
        $parent = new Location($this['parent_id']);
        // Если у родителя нет родителя - значить это город
        if(count($parent->getValues())){ //Если есть родитель. А если нет - значит это самый первый уровень - Регионы
            if (!$parent['parent_id']) {
                $this['is_city'] = 1;
            } else {
                // Если у родителя есть признак город и отметка что у города есть округа - то выставляем отметку - это округ
                if ($parent['is_city'] && $parent['has_county']) {
                    $this['is_county'] = 1;
                }
                //Если у родителя отметка - Регион или Города (но без отметки имеет региодны) - выставляем отметку - это округ
                if ($parent['is_county'] || ($parent['is_city'] && !$parent['has_county'])) {
                    $this['is_district'] = 1;
                }
            }
        }

//        if($flag == self::INSERT_FLAG){
//            if($this->isRegion() || $this['is_city']){
//                $dir_api = new \Catalog\Model\DirApi();
//                // 1. Получаем директории верхнего уровня
//                $dirs_type_action = $dir_api->setFilter('parent', 0)->getList();
//                foreach ($dirs_type_action as $dir_type_action) {
//                    $dir_api->clearFilter();
//                    $dirs_type_object = $dir_api->setFilter('parent', $dir_type_action['id'])->getList();
//                    foreach ($dirs_type_object as $dir_type_object) {
//                        $new_dir = new \Catalog\Model\Orm\Dir();
//                        $new_dir['name'] = $this['title'];
//                        $new_dir['alias'] = \RS\Helper\Transliteration::str2url($new_dir['name']);
//                        if ($dir_type_action['alias'] == 'arenda') {
//                            $new_dir['alias'] = $new_dir['alias'] . '-rent';
//                        }
//                        $new_dir['alias'] = $new_dir['alias'] . '_' . $dir_type_object['id'];
//                        //Если это регион
//                        if ($this->isRegion()) {
//                            $new_dir['parent'] = $dir_type_object['id'];
//                            $new_dir->insert();
//                        } else{
//                            if ($this['is_city']) {
//                                // Добавть директорию город в нужные регион. для этого получить директорию с названием региона соответствующего города
//                                // Название региона в $parent['title']
//                                $dir_api->clearFilter();
//                                $dirs_region = $dir_api->setFilter('parent', $dir_type_object['id'])->getList();
//                                $parent = new Location($this['parent_id']);
//                                foreach ($dirs_region as $dir_region){
//                                    if($dir_region['name'] == $parent['title']){
//                                        $new_dir['parent'] = $dir_region['id'];
//                                        $new_dir->insert();
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        }
    }

    /**
     * Проверяет объект - регион это или нет
     * @return bool
     */
    public function isRegion()
    {
        $parent = new Location($this['parent_id']);
        return count($parent->getValues()) ? false : true;
    }
}
