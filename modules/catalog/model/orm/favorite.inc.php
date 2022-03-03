<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm;

use RS\Orm\OrmObject;
use \RS\Orm\Type;

/**
 * Объект - избранные товары
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $guest_id id гостя
 * @property integer $user_id id пользователя
 * @property integer $product_id id товара
 * --\--
 */
class Favorite extends OrmObject
{
    protected static $table = 'product_favorite';

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'guest_id' => new Type\Varchar([
                'description' => t('id гостя'),
                'maxLength' => 50
            ]),
            'user_id' => new Type\Integer([
                'description' => t('id пользователя')
            ]),
            'product_id' => new Type\Integer([
                'description' => t('id товара')
            ])
        ]);

        $this->addIndex(['guest_id', 'user_id', 'product_id'], self::INDEX_UNIQUE);
        $this->addIndex(['user_id'], self::INDEX_KEY);
        $this->addIndex(['product_id'], self::INDEX_KEY);
    }
}
