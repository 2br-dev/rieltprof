<?php

namespace rieltprof\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * ORM объект
 */
class Partners extends OrmObject
{
    protected static $table = 'partners';
    public static $src_folder = '/storage/partners/original';

    function _init()
    {
        parent::_init()->append([
            'title' => new Type\Varchar([
                'description' => t('Название'),
            ]),
            'link' => new Type\Varchar([
                'description' => t('Ссылка на сайт'),
            ]),
            'image' => new Type\Image([
                'description' => t('Логотип'),
                'max_file_size'    => 10000000, //Максимальный размер - 10 Мб
                'allow_file_types' => array('image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'),//Допустимы форматы jpg, png, gif
                'storage' => array(\Setup::$ROOT, \Setup::$FOLDER . static::$src_folder),
            ]),
            'short_description' => new Type\Text([
                'description' => t('Краткое описание')
            ]),
            'public' => new Type\Integer([
                'description' => t('Показывать'),
                'checkBoxView' => [1,0],
                'maxLength' => 1,
                'default' => 1
            ])
        ]);
    }
}
