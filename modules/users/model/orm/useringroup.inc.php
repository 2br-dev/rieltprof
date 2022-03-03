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
 * Объект - связь пользователей с группами
 * --/--
 * @property integer $user ID пользователя
 * @property string $group ID группы пользователей
 * --\--
 */
class UserInGroup extends \RS\Orm\AbstractObject
{
    protected static
        $table = "users_in_group";

    protected function _init()
    {
        $properties = $this->getPropertyIterator()->append([
            'user' => new Type\Integer([
                'description' => t('ID пользователя')
            ]),
            'group' => new Type\Varchar([
                'description' => t('ID группы пользователей')
            ])
        ]);
        
        $this->addIndex(['user', 'group'], self::INDEX_PRIMARY);
    }    
    
    /**
    * Задает группы, в которых состоит пользователь
    * 
    * @param integer $userid - ID пользователя
    * @param array $groupsAlias - список групп
    */
    function linkUserToGroup($userid, array $groupsAlias)
    {
        $q = new \RS\Orm\Request();
        $q->delete()
            ->from(new \Users\Model\Orm\UserInGroup())
            ->where( ['user' => $userid])->exec();
        foreach($groupsAlias as $group)
        {
            $this['user'] = $userid;
            $this['group'] = $group;
            $this->insert();
        }
    }
}

