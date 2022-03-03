<?php

namespace rieltprof\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * ORM объект
 */
class Review extends OrmObject
{
    protected static $table = 'review';

    function _init()
    {
        parent::_init()->append([
            'user_from' => new Type\Varchar([
                'description' => t('Кто написал отзыв'),
            ]),
            'user_to' => new Type\Integer([
                'description' => t('Для кого (id)')
            ]),
            'text' => new Type\Text([
                'description' => t('Текст отзыва')
            ]),
            'rating' => new Type\Integer([
                'description' => t('Балл')
            ]),
            'date' => new Type\Varchar([
                'description' => t('Дата отзыва')
            ])
        ]);
    }
}
