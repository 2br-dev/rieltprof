<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Config;

use Menu\Model\Orm\Menu;
use RS\AccessControl\DefaultModuleRights;
use RS\Config\Cms as RSConfig;
use RS\Config\Loader as ConfigLoader;
use RS\Module\AbstractPatches;
use RS\Module\Manager as ModuleManager;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Request;
use RS\Orm\Type;
use Users\Model\Api;
use Users\Model\GroupApi;
use Users\Model\Orm\AccessMenu;
use Users\Model\Orm\AccessModule;
use Users\Model\Orm\AccessModuleRight;
use Users\Model\Orm\User;

/**
* Патчи к модулю
*/
class Patches extends AbstractPatches
{
    function init()
    {
        return [
            '20054',
            '20027',
            '20049',
            '306',
            '400',
            '4010',
            '4035'
        ];
    }

    /**
     * Обновляет структуру БД
     */
    function beforeUpdate4035()
    {
        $user = new User();
        $user->getPropertyIterator()->append([
            t('Основные'),
            'e_mail' => new Type\Varchar([
                'maxLength' => '150',
                'unique' => false //Удаляем индекс уникальности
            ]),
            'is_enable_two_factor' => new Type\Integer([
                'allowEmpty' => false
            ]),
        ]);
        $user->dbUpdate();
    }

    /**
     * Обновляет структуру БД
     */
    function beforeUpdate306()
    {
        $user = new User();
        $user->getPropertyIterator()->append([
            t('Основные'),
            'last_ip' => new  Type\Varchar([
                'description' => t('Последний IP, который использовался'),
                'maxLength' => 100
            ]),
            'registration_ip' => new Type\Varchar([
                'description' => t('IP пользователя при регистрации'),
                'maxLength' => 100
            ])
        ]);
        $user->dbUpdate();
    }

    /**
     * Обновляет структуру БД
     */
    function beforeUpdate20054()
    {
        $user = new User();
        $user->getPropertyIterator()->append([
            t('Основные'),
            'last_visit' => new Type\Datetime([
                'description' => t('Последний визит')
            ]),
        ]);
        $user->dbUpdate();
    }
    
    /**
    * Патч к версии 2.0.0.49
    * Принудительно обновляет структуру таблицы пользователя
    */
    function afterUpdate20049()
    {
        $user = new User();
        $user->getPropertyIterator()->append([
            t('Основные'),
            'ban_expire' => new Type\Datetime([
                'description' => t('Заблокировать до ...'),
                'template' => '%users%/form/user/ban_expire.tpl'
            ]),
            'ban_reason' => new Type\Varchar([
                'description' => t('Причина блокировки'),
                'visible' => false
            ]),
        ]);
        $user->dbUpdate();
    }
    
    
    /**
    * Патч к версии 2.0.0.27.
    * Обновляет данные в базе для сохранения прав доступа групп к меню 
    * после изменения концепции организации меню в админ. панели
    */
    function afterUpdate20027()
    {
        $access_menu = new AccessMenu();
        $access_menu->getPropertyIterator()->append([
            'menu_type' => new Type\Enum(['user', 'admin'], [
                'description' => t('Тип меню'),
                'allowEmpty' => false,
                'default' => 'user'
            ]),
        ]);
        $access_menu->dbUpdate();
        
        OrmRequest::make()
            ->update()
            ->from(new AccessMenu(), 'A')
            ->join(new Menu(), 'M.id = A.menu_id', 'M')
            ->set('A.menu_type = M.menutype')
            ->exec();

        OrmRequest::make()
            ->update()
            ->from(new AccessMenu(), 'A')
            ->join(new Menu(), 'M.id = A.menu_id', 'M')
            ->set('A.menu_id = M.alias')
            ->where(['A.menu_type' => 'admin'])
            ->exec();

        OrmRequest::make()
            ->update(new AccessMenu())
            ->set(['menu_type' => 'admin'])
            ->where(['menu_id' => '-2'])
            ->exec();
    }

    /**
     * Патч к версии 4.0.0.
     * Переход на новую систему прав.
     * Переписываем по новому права к модулям.
     */
    public function afterUpdate400()
    {
        $old_access = OrmRequest::make()
            ->from(new AccessModule())
            ->exec()->fetchAll();

        $old_rights = [];
        foreach ($old_access as $item) {
            $old_rights[$item['site_id']][$item['group_alias']][$item['module']] = $item['access'];
        }

        foreach ($old_rights as $site_id=>$groups) {
            foreach ($groups as $group_alias=>$modules) {
                if (isset($modules['all']) && $modules['all'] == 255) {
                    $module_manager = new ModuleManager();
                    $module_list = $module_manager->getList();
                    foreach ($module_list as $module_item) {
                        $module_config = ConfigLoader::byModule($module_item->getName());

                        if ($module_config) {
                            $module_rights = $module_config->getModuleRightObject();
                            
                            foreach ($module_rights->getRights() as $right) {
                                $new_right = new AccessModuleRight();
                                $new_right['site_id'] = $site_id;
                                $new_right['group_alias'] = $group_alias;
                                $new_right['module'] = $module_item->getName();
                                $new_right['right'] = $right->getAlias();
                                $new_right['access'] = RSConfig::ACCESS_ALLOW;
                                $new_right->insert();
                            }
                        }
                    }
                } else {
                    foreach ($modules as $module=>$access) {
                        $module_config = ConfigLoader::byModule($module);

                        if ($module_config) {
                            $module_rights = $module_config->getModuleRightObject();

                            foreach ($module_rights->getRights() as $right) {
                                if (($access % 2 == 1 && $right->getAlias() == DefaultModuleRights::RIGHT_READ) ||
                                    ($access % 4 >= 2 && $right->getAlias() != DefaultModuleRights::RIGHT_READ)) {

                                    $new_right = new AccessModuleRight();
                                    $new_right['site_id'] = $site_id;
                                    $new_right['group_alias'] = $group_alias;
                                    $new_right['module'] = $module;
                                    $new_right['right'] = $right->getAlias();
                                    $new_right['access'] = RSConfig::ACCESS_ALLOW;
                                    $new_right->insert();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Патч к версии 4.0.10
     * Проставляет исходный сортировочный индекс группам пользователей
     */
    public function afterUpdate4010()
    {
        $group_api = new GroupApi();
        foreach ($list = $group_api->getList() as $key => $group) {
            $group['sortn'] = $key;
            $group->update();
        }
    }
}
