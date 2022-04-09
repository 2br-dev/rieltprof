<?php
namespace TinyPNG\Model\Orm;
use \RS\Orm\Type;

/**
* Изображение, которое нужно уменьшить
*/
class Image extends \RS\Orm\OrmObject
{
    protected static
        $table = 'tinypng_image';   
    
    function _init()
    {        
        parent::_init()->append(array(
            'file' => new Type\Varchar(array(
                'description' => t('Относительный путь к файлу'),
                'index' => true,
            )),
            'theme' => new Type\Varchar(array(
                'description' => t('Тема оформления, если из темы'),
            ))
        ));
    }
}
