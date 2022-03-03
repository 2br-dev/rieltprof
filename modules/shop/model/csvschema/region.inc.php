<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\CsvSchema;
use \RS\Csv\Preset;

/**
* Схема экспорта/импорта характеристик в CSV
*/
class Region extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new \Shop\Model\Orm\Region(),
            'excludeFields' => ['id', 'site_id', 'parent_id'],
            'multisite' => true,
            'searchFields' => ['title', 'parent_id'],
            'multisite' => true,
            'savedRequest' => \Shop\Model\RegionApi::getSavedRequest('Shop\Controller\Admin\RegionCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
        ]), [
            new Preset\TreeParent([
                'ormObject' => new \Shop\Model\Orm\Region(),
                'titles' => [
                    'title' => t('Родитель')
                ],
                'idField' => 'id',
                'parentField' => 'parent_id',
                'treeField' => 'title',
                'rootValue' => 0,
                'multisite' => true,                
                'linkForeignField' => 'parent_id',
                'linkPresetId' => 0
            ])
        ]);
    }

    /**
     * Возвращает запрос для базовой выборки
     *
     * @return \RS\Orm\Request
     */
    function getBaseQuery()
    {

        $this->query = parent::getBaseQuery();
        // $pid = $this->params['pid']; - параметр передается по умолчанию из getBaseQuery

        $q = clone $this->query;
        $query_id = $q->select('id')->exec()->fetchSelected('id');// Выбираем id всех дочерних элементов
        $q->where = null;//обнуляем условие на parent_id
        foreach ($query_id as $key=>$value)
        {
            $ids[] = $key;
            $res = $q->where(['parent_id'=>$key])->exec()->fetchSelected('id');// Собираем id городов от регионов, если их нет то и запросы будут пустыми
            foreach ($res as $k=>$v)
                $ids[] = $k;
        }

        $this->query = $this->query->whereIn('parent_id',$ids,'OR');

        return $this->query;
    }
}