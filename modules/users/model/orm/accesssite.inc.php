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
 * @property string $group_alias ID группы
 * @property integer $user_id ID пользователя
 * @property integer $site_id ID сайта, к которому разрешен доступ
 * --\--
 */
class AccessSite extends \RS\Orm\AbstractObject
{
    protected static
        $table = 'access_site';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'group_alias' => new Type\Varchar([
                'description' => t('ID группы'),
                'maxLength' => 50
            ]),
            'user_id' => new Type\Integer([
                'description' => t('ID пользователя')
            ]),
            'site_id' => new Type\Integer([
                'description' => t('ID сайта, к которому разрешен доступ')
            ])
        ]);
        
        $this->addIndex(['site_id', 'group_alias'], self::INDEX_UNIQUE);
        $this->addIndex(['site_id', 'user_id'], self::INDEX_UNIQUE);
    }
}

