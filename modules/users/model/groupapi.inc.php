<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model;

use RS\Cache\Manager as CacheManager;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\Module\AbstractModel\EntityList;
use RS\Module\Manager as ModuleManager;
use RS\Orm\Exception as OrmException;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Site\Model\Orm\Site;

class GroupApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\UserGroup, [
            'nameField' => 'name',
            'idField' => 'alias',
            'sortField' => 'sortn',
        ]);
    }

    public function save($id = null, array $user_post = [])
    {
        $ret = parent::save($id, $user_post);
        if ($ret) {
            /** @var Orm\UserGroup $element */
            $element = $this->getElement();
            if (isset($user_post['menu_access'])) {
                //Обновляем информацию о правах доступа
                $element->setMenuAccess($user_post['menu_access']);
            }

            if (isset($user_post['menu_admin_access'])) {
                //Обновляем информацию о правах доступа
                $element->setMenuAccess($user_post['menu_admin_access'], Orm\AccessMenu::ADMIN_MENU_TYPE);
            }

            if (isset($user_post['module_access'])) {
                //Обновляем информацию о правах к модулям
                $element->setModuleAccess($user_post['module_access']);
            }

            if (isset($user_post['site_access'])) {
                $element->setSiteAccess(SiteManager::getSiteId(), $user_post['site_access']);
            }
        }
        return $ret;
    }

    /**
     * Подготавливает информацию о модуле и его допустимых правах
     * @param $module_access - массив с парвами для модулей (возвращает Orm\UserGroup::getModuleAccess())
     * @return array
     */
    public function prepareModuleAccessData($module_access)
    {
        $list_fot_table = [];
        $modules = new ModuleManager();
        $modlist = $modules->getAllConfig();
        foreach ($modlist as $modclass => $modconfig) {
            $linedata = [
                'class' => $modclass,
                'name' => $modconfig['name'],
                'description' => $modconfig['description'],
                'access' => isset($module_access[$modclass]) ? $module_access[$modclass] : [],
                'right_object' => $modconfig->getModuleRightObject(),
            ];

            $list_fot_table[] = $linedata;
        }
        return $list_fot_table;
    }

    /**
     * Копирование прав доступа групп пользователей
     *
     * @param integer $new_site_id
     * @return void
     * @throws DbException
     * @throws EventException
     * @throws RSException
     * @throws OrmException
     */
    static function CloneRightFromDefaultSite($new_site_id)
    {
        $default_site = Site::loadByWhere([
            'default' => 1
        ]);

        if ($default_site['id'] && $new_site_id != $default_site['id']) {
            /** @var Orm\AccessSite[] $access_site */
            $access_site = OrmRequest::make()
                ->from(new Orm\AccessSite())
                ->where([
                        'site_id' => $default_site['id'],
                    ]
                )
                ->objects();

            foreach ($access_site as $item) {
                unset($item['id']);
                $item['site_id'] = $new_site_id;
                $item->insert();
            }

            /** @var Orm\AccessModule[] $access_module */
            $access_module = OrmRequest::make()
                ->from(new Orm\AccessModule())
                ->where([
                        'site_id' => $default_site['id'],
                    ]
                )
                ->objects();

            foreach ($access_module as $item) {
                unset($item['id']);
                $item['site_id'] = $new_site_id;
                $item->insert();
            }

            /** @var Orm\AccessMenu[] $access_menu */
            $access_menu = OrmRequest::make()
                ->from(new Orm\AccessMenu())
                ->where("`menu_type` != '#menu_type' AND `site_id` = '#site_id'", [
                        'menu_type' => 'admin',
                        'site_id' => $default_site['id'],
                    ]
                )
                ->objects();

            foreach ($access_menu as $item) {
                unset($item['id']);
                $item['site_id'] = $new_site_id;
                $item->insert();
            }
        }
    }

    /**
     * Возвращает совокупность установленных прав для переданных групп
     * Результат кэшируется
     *
     * @param string|string[] $groups - группы пользователей
     * @param bool $cache - использовать кэш
     * @return array
     * @throws DbException
     * @throws OrmException
     * @throws RSException
     */
    public static function getRights($groups, $cache = true)
    {
        $groups = (array) $groups;
        
        if ($cache) {
            return CacheManager::obj()
                ->expire(0)
                ->watchTables(new Orm\AccessModuleRight())
                ->request([__CLASS__, __FUNCTION__], $groups, false, SiteManager::getSiteId());
        } else {
            $rights = OrmRequest::make()
                ->from(new Orm\AccessModuleRight())
                ->where(['site_id' => SiteManager::getSiteId()])
                ->whereIn('group_alias', $groups)
                ->exec()->fetchAll();
            
            $result = [];
            foreach ($rights as $right) {
                $result[$right['module']][$right['right']][$right['access']] = $right['access'];
            }
            
            return $result;
        }
    }
}
