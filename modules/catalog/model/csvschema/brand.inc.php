<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvSchema;
use \RS\Csv\Preset;

/**
* Схема экспорта/импорта брендов в CSV
*/
class Brand extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new \Catalog\Model\Orm\Brand(),
            'excludeFields' => [
                'id', 'site_id', 'image'
            ],
            'savedRequest' => \Catalog\Model\BrandApi::getSavedRequest('Catalog\Controller\Admin\BrandCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
            'multisite' => true,
            'searchFields' => ['title']
        ]), [
            new Preset\SinglePhoto([
                'linkPresetId' => 0,
                'linkForeignField' => 'image',
                'title' => t('Изображение')
            ])
        ]);
    }
}