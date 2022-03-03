<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Site\Manager as SiteManager;
use Users\Model\GroupApi;

/**
 * Объект - группа пользователей
 * --/--
 * @property string $alias Псевдоним(англ.яз)
 * @property string $name Название группы
 * @property string $description Описание
 * @property integer $is_admin Администратор
 * @property integer $sortn Сортировочный индекс
 * --\--
 */
class UserGroup extends OrmObject
{
    //Предустановленные группы
    const GROUP_SUPERVISOR = 'supervisor';
    const GROUP_ADMIN = 'admins';
    const GROUP_GUEST = 'guest';
    const GROUP_CLIENT = 'clients';

    protected static $table = "users_group";

    protected $non_delete_groups = ['supervisor', 'clients', 'admins', 'guest'];
    protected $access_menu_table = 'access_menu';
    protected $access_module_table = 'access_module';

    public function __construct($id = null)
    {
        $this->access_menu_table = "`" . \Setup::$DB_NAME . "`.`" . \Setup::$DB_TABLE_PREFIX . "{$this->access_menu_table}`";
        $this->access_module_table = "`" . \Setup::$DB_NAME . "`.`" . \Setup::$DB_TABLE_PREFIX . "{$this->access_module_table}`";
        parent::__construct($id);
    }

    protected function _init()
    {
        $this->getPropertyIterator()->append([
            t('Основные'),
                'alias' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Псевдоним(англ.яз)'),
                    'primaryKey' => true,
                    'Checker' => ['chkPattern', t('Псевдоним должен состоять из латинских букв и цифр.'), '/^[a-zA-Z0-9]+$/'],
                ]),
                'name' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Название группы'),
                    'Checker' => ['chkEmpty', t('Необходимо заполнить название группы')],
                ]),
                'description' => new Type\Text([
                    'maxLength' => '100',
                    'description' => t('Описание'),
                ]),
                'is_admin' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Администратор'),
                    'hint' => t('Администратор имеет доступ в панель управления'),
                    'CheckboxView' => [1, 0],
                ]),
                'sortn' => new Type\Integer([
                    'index' => true,
                    'description' => t('Сортировочный индекс'),
                    'visible' => false
                ]),
            t('Права'),
                '__access__' => new Type\UserTemplate('%users%/form/group/access.tpl'),
        ]);
    }

    /**
     * Возвращает имя свойства, которое помечено как первичный ключ.
     *
     * @return string
     */
    public function getPrimaryKeyProperty()
    {
        return 'alias';
    }

    /**
     * Функция срабатывает перед записью в базу
     *
     * @param mixed $flag
     * @return void
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = OrmRequest::make()
                ->select('MAX(sortn) as max')
                ->from($this)
                ->exec()->getOneField('max', 0) + 1;
        }
    }

    /**
     * Удаляет объект из хранилища, возвращает true, в случае успеха
     *
     * @return bool
     */
    public function delete()
    {
        if (in_array($this['alias'], $this->non_delete_groups)) return false; //Группу администраторы - удалить нельзя. Это базовыя группа.
        $this->setModuleAccess([]); //Удаляем записи о правах к модулям
        $this->setMenuAccess([]); //Удаляем записи о правах к пунктам меню
        return parent::delete();
    }

    /**
     * Возвращает массив с id доступных пунктов меню для этой группы
     * array(-1,...) - доступ ко всем пунктам меню пользователя
     * array(-2,...) - доступ ко всем пунктам меню администратора
     * array() - нет доступа ни к одному пункту меню
     *
     * @return array
     */
    public function getMenuAccess()
    {
        if (empty($this['alias'])) return [];

        return OrmRequest::make()
            ->select('menu_id')
            ->from(new AccessMenu())
            ->where([
                'site_id' => SiteManager::getSiteId(),
                'group_alias' => $this['alias']
            ])
            ->exec()
            ->fetchSelected(null, 'menu_id');
    }

    /**
     * Получить права к модулям
     */
    public function getModuleAccess()
    {
        if (empty($this['alias'])) {
            return [];
        }

        return GroupApi::getRights($this['alias']);
    }

    /**
     * Установить права к пунктам меню
     *
     * @param int[] $menu_ids - список id пунктов меню
     * @param string $menu_type - тип меню
     * @return void
     */
    public function setMenuAccess(array $menu_ids, $menu_type = AccessMenu::USER_MENU_TYPE)
    {
        if (empty($this['alias'])) return;
        OrmRequest::make()
            ->delete()
            ->from(new AccessMenu())
            ->where([
                'group_alias' => $this['alias'],
                'menu_type' => $menu_type,
                'site_id' => SiteManager::getSiteId()
            ])
            ->exec();

        if (empty($menu_ids)) return;
        foreach ($menu_ids as $menu_id) {
            $item = new AccessMenu();
            $item['site_id'] = SiteManager::getSiteId();
            $item['menu_id'] = $menu_id;
            $item['menu_type'] = $menu_type;
            $item['group_alias'] = $this['alias'];
            $item->insert();
        }
    }

    /**
     * Устанавливает права к модулям
     *
     * @param array $module_rights - массив прав
     *  $module_rights = [
     *      module_name => [
     *          right_alias => access
     *      ]
     *  ]
     * @return void
     */
    public function setModuleAccess($module_rights)
    {
        if (empty($this['alias'])) {
            return;
        }

        OrmRequest::make()
            ->delete()
            ->from(new AccessModuleRight())
            ->where([
                'group_alias' => $this['alias'],
                'site_id' => SiteManager::getSiteId()
            ])
            ->exec();

        if (empty($module_rights)) return;

        foreach ($module_rights as $module => $rights) {
            foreach ($rights as $right => $access) {
                if ($access) {
                    $item = new AccessModuleRight();
                    $item['site_id'] = SiteManager::getSiteId();
                    $item['group_alias'] = $this['alias'];
                    $item['module'] = $module;
                    $item['right'] = $right;
                    $item['access'] = $access;
                    $item->insert();
                }
            }
        }
    }

    /**
     * Устанавливает право доступа к администрированию сайта
     *
     * @param integer $site_id
     * @param boolean $bool
     */
    public function setSiteAccess($site_id, $bool = true)
    {
        if ($bool) {
            $access_site = new AccessSite();
            $access_site['site_id'] = $site_id;
            $access_site['group_alias'] = $this['alias'];
            $access_site->replace();
        } else {
            OrmRequest::make()
                ->delete()
                ->from(new AccessSite())
                ->where([
                    'site_id' => $site_id,
                    'group_alias' => $this['alias']
                ])
                ->exec();
        }
    }

    /**
     * Возвращает true, если у группы есть доступ к сайту site_id
     *
     * @param integer $site_id
     * @return bool
     */
    public function getSiteAccess($site_id)
    {
        $access = AccessSite::loadByWhere([
            'site_id' => $site_id,
            'group_alias' => $this['alias']
        ]);

        return $access['site_id'] > 0;
    }
}
