<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\CsvSchema;

use RS\Csv\AbstractSchema;
use RS\Csv\Preset;
use RS\Orm\Request as OrmRequest;
use Shop\Model\RegionApi;

/**
 * Схема экспорта/импорта характеристик в CSV
 */
class Region extends AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new \Shop\Model\Orm\Region(),
            'excludeFields' => ['id', 'site_id', 'parent_id'],
            'multisite' => true,
            'searchFields' => ['title', 'parent_id'],
            'savedRequest' => RegionApi::getSavedRequest('Shop\Controller\Admin\RegionCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
        ]), [
            new Preset\TreeParent([
                'ormObject' => new \Shop\Model\Orm\Region(),
                'titles' => [
                    'title' => t('Родитель'),
                ],
                'idField' => 'id',
                'parentField' => 'parent_id',
                'treeField' => 'title',
                'rootValue' => 0,
                'multisite' => true,
                'linkForeignField' => 'parent_id',
                'linkPresetId' => 0,
            ])
        ]);
    }

    /**
     * Возвращает запрос для базовой выборки
     *
     * @return OrmRequest
     */
    function getBaseQuery()
    {
        $this->query = parent::getBaseQuery();
        $q = clone $this->query;

        $query_ids = $q->select('id')->exec()->fetchSelected('id', 'id');

        if ($query_ids) {
            $region_api = new RegionApi();
            $query = clone $region_api->queryObj();
            $child_ids = $query->whereIn('parent_id', $query_ids)
                ->exec()->fetchSelected('id', 'id');

            if ($child_ids) {
                $q->whereIn('id', $child_ids, 'OR')
                    ->whereIn('parent_id', $child_ids, 'OR');
            }
        }

        return $q;
    }
}
