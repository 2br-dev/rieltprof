<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\CsvSchema;

use Crm\Model\CsvPreset\LinksReverse;
use Crm\Model\Orm\Interaction;
use Crm\Model\Orm\Task;
use \RS\Csv\Preset;
use RS\Config\Loader;
use Crm\Model\Orm\Status;
use Crm\Model\CsvPreset\CustomFields;
use Crm\Model\CsvPreset\Links;

/**
 * Схема экспорта/импорта сделок в CSV
 */
class Deal extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        $user_fields_manager = Loader::byModule($this)->getDealUserFieldsManager();

        parent::__construct(new Preset\Base([
            'ormObject' => new \Crm\Model\Orm\Deal(),
            'excludeFields' => [
                'id', '_tmpid'
            ],
            'savedRequest' => \Crm\Model\DealApi::getSavedRequest('Crm\Controller\Admin\DealCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
            'searchFields' => ['deal_num']
        ]), [
            new Preset\LinkedTable([
                'ormObject' => new Status(),
                'fields' => ['title'],
                'titles' => ['title' => t('Статус')],
                'idField' => 'id',
                'linkForeignField' => 'status_id',
                'linkPresetId' => 0,
                'linkDefaultValue' => 0,
                'beforeRowImport' => function($self) {
                    $self->row['object_type_alias'] = 'crm-deal';
                }
            ]),
            new Links([
                'LinkIdField' => 'id',
                'LinkForeignField' => 'links',
                'LinkSourceType' => \Crm\Model\Orm\Task::getLinkSourceType(),
                'LinkPresetId' => 0,
            ]),
            new LinksReverse([
                'ColumnTitle' => t('Взаимодействия'),
                'LinkType' => \Crm\Model\Orm\Deal::getSelfLinkManagerType(),
                'sourceType' => Interaction::getLinkSourceType(),
                'linkIdField' => 'id',
                'exportObject' => new Interaction(),
                'exportFormatCallback' => function($export_object) {
                        return $export_object['date_of_create'].' - '.$export_object['title'].'('.$export_object->getCreatorUser()->getFio().')';
                },
            ]),
            new LinksReverse([
                'ColumnTitle' => t('Задачи'),
                'LinkType' => \Crm\Model\Orm\Deal::getSelfLinkManagerType(),
                'sourceType' => Task::getLinkSourceType(),
                'linkIdField' => 'id',
                'exportObject' => new Task(),
                'exportFormatCallback' => function($export_object) {
                        return $export_object['task_num'].' - '.$export_object['title'].'('.$export_object->getCreatorUser()->getFio().')';
                },
            ]),
            new CustomFields([
                'userFieldsManager' => $user_fields_manager,
                'linkIdField' => 'custom_fields',
                'linkPresetId' => 0
            ])
        ], [
            'FieldScope' => [
                'linksreverse-title' => 'export',
                'links-title' => 'export'
            ]
        ]);
    }
}