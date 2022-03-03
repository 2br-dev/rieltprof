<?php

namespace rieltprof\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * ORM объект
 */
class District extends OrmObject
{
    protected static $table = 'district';

    function _init()
    {
        parent::_init()->append([
            'title' => new Type\Varchar([
                'description' => t('Название'),
            ])
        ]);
    }
}
