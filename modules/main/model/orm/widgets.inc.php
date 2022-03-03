<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\Orm;
use \RS\Orm\Type;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $user_id 
 * @property integer $mode2_column Колонка виджета в двухколоночной сетке
 * @property integer $mode3_column Колонка виджета в трехколоночной сетке
 * @property integer $mode1_position Позиция виджета в одноколоночной сетке
 * @property integer $mode2_position Позиция виджета в двухколоночной сетке
 * @property integer $mode3_position Позиция виджета в трехколоночной сетке
 * @property string $class 
 * @property string $vars 
 * --\--
 */
class Widgets extends \RS\Orm\OrmObject
{
    const
        MAX_COLUMNS = 3;
        
    protected static
        $table = 'widgets';
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'user_id' => new Type\Integer([
                'maxLength' => '11',
            ]),
            
            'mode2_column' => new Type\Integer([
                'description' => t('Колонка виджета в двухколоночной сетке'),
                'maxLength' => '5',
            ]),
            'mode3_column' => new Type\Integer([
                'description' => t('Колонка виджета в трехколоночной сетке'),
                'maxLength' => '5',
            ]),
            
            'mode1_position' => new Type\Integer([
                'description' => t('Позиция виджета в одноколоночной сетке'),
                'maxLength' => '5',
            ]),
            'mode2_position' => new Type\Integer([
                'description' => t('Позиция виджета в двухколоночной сетке'),
                'maxLength' => '5',
            ]),
            'mode3_position' => new Type\Integer([
                'description' => t('Позиция виджета в трехколоночной сетке'),
                'maxLength' => '5',
            ]),
        
            'class' => new Type\Varchar([
                'maxLength' => '255',
            ]),
            'vars' => new Type\Text([
            ]),
        ]);
        
        $this->addIndex(['site_id', 'user_id', 'class'], self::INDEX_UNIQUE);
    }

    /**
    * Возвращает полное имя класса виджета для текущего объекта
    * 
    * @return string
    */
    function getFullClass()
    {
        return self::staticGetFullClass($this['class']);
    }
    
    /**
    * Возвращает полное имя класса виджета
    * 
    * @param string $short_classname - сокращенное имя класса контроллера виджета
    * @return string
    */    
    public static function staticGetFullClass($short_classname)
    {
        $classname = preg_replace('/^(.*?)-(.*)$/', '$1-controller-admin-$2',$short_classname);
        return str_replace('-','\\', $classname);        
    }
    
    /**
    * Возвращает позиции виджета в сетках, различной колоночности
    * 
    * @return string
    */
    function getPositionsJson()
    {
        $result = [];
        for($i=1; $i <= self::MAX_COLUMNS; $i++) {
            $result[$i]['column'] = $i == 1 ? 1 : $this["mode{$i}_column"];
            $result[$i]['position'] = $this["mode{$i}_position"];
        }
        
        return json_encode($result);
    }
}

