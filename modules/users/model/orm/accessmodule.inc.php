<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Orm;
use \RS\Orm\Type;

/**
 * --/--
 * @property integer $site_id ID сайта
 * @property string $module Идентификатор модуля
 * @property integer $user_id ID пользователя
 * @property string $group_alias ID группы
 * @property integer $access Уровень доступа
 * --\--
 */
class AccessModule extends \RS\Orm\AbstractObject
{
    const
        /**
        * Величина, обозначающая максимальные права к модулю
        */
        MAX_ACCESS_RIGHTS = 255,
        FULL_MODULE_ACCESS = 'all';
    
    protected static
        $table = 'access_module';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'module' => new Type\Varchar([
                'description' => t('Идентификатор модуля'),
                'maxLength' => 150
            ]),
            'user_id' => new Type\Integer([
                'description' => t('ID пользователя')
            ]),
            'group_alias' => new Type\Varchar([
                'description' => t('ID группы'),
                'maxLength' => 50
            ]),
            'access' => new Type\Integer([
                'description' => t('Уровень доступа')
            ])
        ]);
        
        $this->addIndex(['site_id', 'module', 'user_id', 'group_alias'], self::INDEX_UNIQUE);
    }
}

