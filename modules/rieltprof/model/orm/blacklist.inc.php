<?php

namespace rieltprof\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * ORM объект
 */
class BlackList extends OrmObject
{
    protected static $table = 'rieltprof_blacklist';

    function _init()
    {
        parent::_init()->append([
            'author' => new Type\Varchar([
                'description' => t('Автор'),
            ]),
            'date' => new Type\Varchar([
                'description' => t('Дата')
            ]),
            'phone' => new Type\Varchar([
                'description' => t('Телефон')
            ]),
            'comment' => new Type\Text([
                'description' => t('Комментарий')
            ]),
            'public' => new Type\Integer([
                'description' => t('Публиковать'),
                'checkBoxView' => [1, 0],
                'default' => 1,
                'maxLength' => 1
            ])
        ]);
    }
}
