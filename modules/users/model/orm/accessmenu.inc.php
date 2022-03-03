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
 * @property string $menu_id ID пункта меню
 * @property string $menu_type Тип меню
 * @property integer $user_id ID пользователя
 * @property string $group_alias ID группы
 * --\--
 */
class AccessMenu extends \RS\Orm\AbstractObject
{
    const
        FULL_USER_ACCESS = -1, //Полный доступ к меню пользователя
        FULL_ADMIN_ACCESS = -2, //Полный доступ к меню администратора
        USER_MENU_TYPE = 'user', //Меню клиентской части
        ADMIN_MENU_TYPE = 'admin'; //Меню админ панели
        
    public static
        $table = 'access_menu';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'menu_id' => new Type\Varchar([
                'description' => t('ID пункта меню'),
                'maxLength' => 50
            ]),
            'menu_type' => new Type\Enum(['user', 'admin'], [
                'description' => t('Тип меню'),
                'allowEmpty' => false,
                'default' => 'user'
            ]),
            'user_id' => new Type\Integer([
                'description' => t('ID пользователя')
            ]),
            'group_alias' => new Type\Varchar([
                'description' => t('ID группы'),
                'maxLength' => 50
            ])
        ]);
        $this->addIndex(['site_id', 'menu_type'], self::INDEX_KEY);
    }
}

