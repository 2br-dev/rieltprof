<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Model\CsvSchema;
use \RS\Csv\Preset,
    \Article\Model\Orm;

/**
* Схема экспорта/импорта характеристик в CSV
*/
class Article extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new Orm\Article(),
            'temporaryId' => true,
            'excludeFields' => ['id', 'site_id', 'parent', 'image'],
            'multisite' => true,
            'searchFields' => ['title', 'parent'],
            'selectRequest' => \RS\Orm\Request::make()
                ->select('A.*, C.title as dir_title, C.alias as dir_alias')
                ->from(new Orm\Article(), 'A')
                ->leftjoin(new Orm\Category(), 'C.id = A.parent', 'C')
                ->where([
                    'A.site_id' => \RS\Site\Manager::getSiteId()
                ])
                ->orderby('A.parent')
        ]),
        [
            new Preset\SinglePhoto([
                'linkPresetId' => 0,
                'linkForeignField' => 'image',
                'title' => t('Изображение')
            ]),
            new Preset\TreeParent([
                'ormObject' => new Orm\Category(),
                'fieldsMap' => [
                    'dir_alias' => 'alias',
                    'dir_title' => 'title'
                ],
                'titles' => [
                    'dir_title' => t('Категория'),
                    'dir_alias' => t('Псевдоним категории')
                ],
                'idField' => 'id',
                'fields' => ['dir_alias'],
                'treeField' => 'dir_title',
                'parentField' => 'parent',
                'rootValue' => 0,
                'multisite' => true,
                'linkForeignField' => 'parent',
                'linkPresetId' => 0
            ]),
            new Preset\PhotoBlock([
                'typeItem' => \Article\Model\Orm\Article::IMAGES_TYPE,
                'linkPresetId' => 0,
                'linkIdField' => 'id'
            ]),
            new Preset\Tags([
                'item'         => 'article',
                'linkPresetId' => 0,
                'linkIdField'  => 'id'
            ]),
        ],
        [
            'beforeLineImport' => [__CLASS__, 'beforeLineImport']
        ]);
    }
    
    public static function beforeLineImport($_this)
    {
        //Устанавливаем временный id
        $time = -time();
        $_this->getPreset(0)->row['id'] = $time;
        $_this->getPreset(0)->row['_tmpid'] = $time;
    }
    
}