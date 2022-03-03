<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\CsvSchema;
use \RS\Csv\Preset,
    \Shop\Model\Orm;

/**
* Схема экспорта/импорта характеристик в CSV
*/
class Payment extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new Orm\Payment(),
            'excludeFields' => ['id', 'site_id', 'first_status', 'success_status'],
            'multisite' => true,
            'savedRequest' => \Shop\Model\PaymentApi::getSavedRequest('Shop\Controller\Admin\PaymentCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
            'searchFields' => ['title']
        ]), [
            new Preset\LinkedTable([
                'ormObject' => new Orm\UserStatus(),
                'fields' => ['title'],
                'titles' => ['title' => t('Начальный статус заказа')],
                'idField' => 'id',
                'multisite' => true,
                'linkForeignField' => 'first_status',
                'linkPresetId' => 0,
                'linkDefaultValue' => 0,
                'save' => false
            ]),
            new Preset\LinkedTable([
                'ormObject' => new Orm\UserStatus(),
                'fields' => ['title'],
                'titles' => ['title' => t('Статус заказа в случае успешной оплаты')],
                'idField' => 'id',
                'multisite' => true,
                'linkForeignField' => 'success_status',
                'linkPresetId' => 0,
                'linkDefaultValue' => 0,
                'save' => false
            ])
        ]);
    }
}