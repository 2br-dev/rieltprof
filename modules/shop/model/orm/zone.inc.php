<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm;

use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * Регион доставки
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property array $xregion Регионы
 * --\--
 */
class Zone extends OrmObject
{
    protected static $table = 'order_zone';
    
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title' => new Type\Varchar([
                'checker' => ['chkEmpty', t('Введите имя зоны')],
                'description' => t('Название')
            ]),
            'xregion' => new Type\ArrayList([
                'checker' => ['chkEmpty', t('Укажите регионы')],
                'description' => t('Регионы'),
                'tree' => [['\Shop\Model\RegionApi', 'staticTreeList']],
                'attr' => [[
                    AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                ]],
            ]),
        ]);
    }

    /**
     * Действия после записи объекта
     *
     * @param string $flag - insert или update
     * @return void
     * @throws \RS\Event\Exception
     */
    function afterWrite($flag)
    {
        // Удаляем старые связи с регионами
        $this->deleteRegions();
            
        // Записываем новые регионы
        if(is_array($this['xregion']))
        {
            foreach($this['xregion'] as $region_id){
                $xregion = new Xregion();
                $xregion['zone_id']   = $this['id'];
                $xregion['region_id']= $region_id;
                $xregion->insert();
            }
        }
    }
    
    /**
    * Удалить все связи этой зоны с регионами
    */
    function deleteRegions()
    {
        OrmRequest::make()->delete()
            ->from(new Xregion)
            ->where(['zone_id' => $this['id']])
            ->exec();
    }
    
    
    /**
    * Заполнить поле xregions массивом идентификаторов регионов
    */
    function fillRegions()
    {
        $regions = OrmRequest::make()->select('region_id')
            ->from(new Xregion)
            ->where(['zone_id' => $this['id']])
            ->exec()->fetchSelected(null, 'region_id');
        $this['xregion'] = $regions;
    }
    
    /**
    * Удаление
    */
    function delete()
    {
        // Удаляем cвязи с регионами
        $this->deleteRegions();
        
        // Удаляем себя
        return parent::delete();
    }    
    
    function cloneSelf()
    {
        $this->fillRegions();
        return parent::cloneSelf();
    }
}
