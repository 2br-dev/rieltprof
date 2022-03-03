<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photogalleries\Model\CsvSchema;

use RS\Csv\Preset;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;

/**
 * Схема импорта/экспорта в CSV файл альбомов
 */
class Album extends \RS\Csv\AbstractSchema
{
    protected $catalog_config;

    function __construct()
    {
        $exclude = [
            'id', 'site_id'
        ];

        parent::__construct(
            new Preset\Base([
                'ormObject' => new \Photogalleries\Model\Orm\Album(),
                'excludeFields' => $exclude,
                'savedRequest' => \Catalog\Model\Api::getSavedRequest('Photogalleries\Controller\Admin\Ctrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
                'multisite' => true,
                'searchFields' => ['title']
            ]),
            [
                new Preset\PhotoBlock([
                    'typeItem' => \Photogalleries\Model\Orm\Album::IMAGES_TYPE,
                    'linkPresetId' => 0,
                    'linkIdField' => 'id'
                ]),
            ],
            [
                'beforeLineImport' => [__CLASS__, 'beforeLineImport']
            ]
        );                                             
    }

    /**
     * Функция срабатывает перед записью одной строчки при импорте
     *
     * @param self $_this - объект текущей схемы
     * @throws DbException
     * @throws EventException
     */
    public static function beforeLineImport($_this)
    {
        /** @var \Photogalleries\Model\Orm\Album $row */
        $row = &$_this->getPreset(0)->row;

        //Устанавливаем временный id
        $time = -time();
        $row['id'] = $time;
        $row['_tmpid'] = $time;
    }
}
