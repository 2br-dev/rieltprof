<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Model;

use Menu\Model\MenuType\AbstractType as AbstractMenuType;
use Menu\Model\Orm\Menu;
use RS\Application\Auth;
use RS\Cache\Manager as CacheManager;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Helper\Tools as HelperTools;
use RS\Html\Table\Type as TableType;
use RS\Http\Request as HttpRequest;
use RS\Module\AbstractModel\TreeCookieList;
use RS\Module\AbstractModel\TreeList\TreeListFakeIterator;
use RS\Module\AbstractModel\TreeList\TreeListFakeNode;
use RS\Orm\AbstractObject;
use RS\Orm\Exception as OrmException;
use RS\Orm\Request as OrmRequest;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;

/**
 * Класс содержит функции для работы со списком меню
 */
class Api extends TreeCookieList
{
    const TYPELINK_LINK = 'link';

    protected $sort_field = 'sortn';
    protected $checkAccess = true; //Если true, то возвращаем только те пункты меню, на которые есть права у текущего пользователя. false - не прверять права
    protected $accessFilters = null;

    function __construct()
    {
        parent::__construct(new Menu(), [
            'multisite' => true,
            'parentField' => 'parent',
            'nameField' => 'title',
            'aliasField' => 'alias',
            'sortField' => 'sortn',
            'defaultOrder' => 'parent, sortn',
        ]);
    }

    /**
     * Возвращает массив с инструкциями для установки фильтра пунктов меню по ID,
     * согласно правам доступа текущего пользователя к этим пунктам
     *
     * @return array | bool(false) - Возвращает false в случае, если имеется полный доступ ко всем пунктам меню
     */
    function getAccessFilter()
    {
        //Кэшируем внутри объекта
        if (!isset($this->accessFilters)) {
            $current_user = Auth::getCurrentUser();
            $allow_menu = $current_user->getMenuAccess();

            $this->accessFilters = [];

            if (in_array(FULL_USER_ACCESS, $allow_menu)) {
                $this->accessFilters = false; //Полный доступ
            } else {
                //Полного доступа нет, добавляем список доступных пунктов меню
                $ids = array_diff($allow_menu, [FULL_USER_ACCESS]);
                if (!empty($ids)) {
                    $this->accessFilters['|id:in'] = implode(',', HelperTools::arrayQuote($ids));
                } else {
                    $this->accessFilters['id'] = 0;
                }
            }
        }
        return $this->accessFilters;
    }

    /**
     * Переключает флаг $this->checkAccess
     *
     * @param bool $checkAccess - если true, то будут возвращены только те пункты меню, к которым есть доступ
     * @return void
     */
    function setCheckAccess($checkAccess)
    {
        $this->checkAccess = $checkAccess;
    }

    static function selectList()
    {
        $_this = new self();
        $_this->setFilter('menutype', 'user');
        return [0 => t('Верхний уровень')] + $_this->getSelectList(0);
    }

    /**
     * Возвращает текущий пункт меню. В случае успешного обнаружения объект будет загружен (id>0)
     *
     * @return Orm\Menu
     */
    function getCurrentMenuItem()
    {
        $item_id = HttpRequest::commonInstance()->parameters('menu_item_id');
        return new Menu($item_id);
    }

    /**
     * Перемещает элемент from на место элемента to. Если flag = 'up', то до элемента to, иначе после
     *
     * @param int $from - id элемента, который переносится
     * @param int $to - id ближайшего элемента, возле которого должен располагаться элемент
     * @param string $flag - up или down - флаг выше или ниже элемента $to должен располагаться элемент $from
     * @param OrmRequest $extra_expr - объект с установленными уточняющими условиями, для выборки объектов сортировки
     * @param int $new_parent_id - новый ID родительского элемента
     * @return bool
     * @throws \RS\Db\Exception
     */
    function moveElement($from, $to, $flag, OrmRequest $extra_expr = null, $new_parent_id = null)
    {
        $from_obj = $this->getOneItem($from);

        if (!$extra_expr) {
            $extra_expr = OrmRequest::make()->where([
                'parent' => $new_parent_id ?: $from_obj['parent'],
                'menutype' => $from_obj['menutype'],
            ]);
        }

        if ($from_obj['menutype'] != 'admin') {
            $extra_expr->where(['site_id' => $from_obj['site_id']]);
        }

        return parent::moveElement($from, $to, $flag, $extra_expr, $new_parent_id);
    }

    /**
     * Получает меню для админки
     *
     * @param bool $cache - использовать кэш
     * @return array
     * @throws \RS\Event\Exception
     */
    function getAdminMenu($cache = true)
    {
        $user = Auth::getCurrentUser();

        if ($cache) {
            $site_id = SiteManager::getSiteId();
            return CacheManager::obj()->request([$this, 'getAdminMenu'], false, $site_id, $user['id']);
        } else {
            $event_result = EventManager::fire('getmenus', []);
            $menu_list = $event_result->getResult();
            $allow_menu = [];
            if ($check_access = $this->checkAccess) {
                $allow_menu = $user->getAdminMenuAccess();
                if (in_array(FULL_ADMIN_ACCESS, $allow_menu)) {
                    $check_access = false;
                }
            }

            $menus = [];
            if (!empty($menu_list)) {
                foreach ($menu_list as $item) {
                    if (!$check_access || (in_array($item['alias'], $allow_menu))) {
                        $item['link'] = $this->getHref($item);
                        $menus[$item['alias']] = $item;
                    }
                }
                uasort($menus, [$this, 'resortAdminMenu']);
                $menus = $this->getAdminMenuLikeTree($menus);
            }
            return $menus;
        }
    }

    /**
     * Отсортировывает меню по номеру сортировки
     *
     * @param array $a - массив со сведения о меню
     * @param array $b - массив со сведения о меню
     * @return int
     */
    private function resortAdminMenu($a, $b)
    {
        $al = isset($a['sortn']) ? $a['sortn'] : 0;
        $bl = isset($b['sortn']) ? $b['sortn'] : 0;
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    /**
     * Возвращает URL в зависимости от типа пункта меню
     *
     * @param array $item - массив со сведениями о пункте меню
     * @return string
     */
    public static function getHref($item)
    {
        if ($item['typelink'] != self::TYPELINK_LINK) {
            return false;
        } else {
            return
                str_replace('%ADMINPATH%', \Setup::$FOLDER . '/' . \Setup::$ADMIN_SECTION, $item['link']);
        }
    }

    /**
     * Сортирует ассоциативный массив плоского меню в древовидные
     *
     * @param array $items - массив пунктоа меню
     * @return TreeListFakeIterator
     */
    private function getAdminMenuLikeTree($items)
    {
        /** @var TreeListFakeNode[] $node_items */
        $node_items = [];
        foreach ($items as $alias => $item) {
            $fake_node = new TreeListFakeNode($item);
            $fake_node->setChilds(new TreeListFakeIterator());
            $node_items[$alias] = $fake_node;
        }

        $tree = new TreeListFakeIterator();
        foreach ($node_items as $alias => $node) {
            $object = $node->getObject();
            if (isset($node_items[$object['parent']])) {
                /** @var TreeListFakeIterator $node_iterator */
                $node_iterator = $node_items[$object['parent']]->getChilds();
                $node_iterator->addItem($node);
            } else {
                $tree->addItem($node);
            }
        }

        return $tree;
    }

    /**
     * Удаляет пункт меню
     *
     * @param mixed $alias
     * @param mixed $parent_alias
     * @param string $menutype - тип меню
     * @return bool
     * @throws DbException
     * @throws OrmException
     */
    function deleteItem($alias, $parent_alias, $menutype)
    {
        $parent = Menu::loadByWhere([
            'alias' => $parent_alias,
            'menutype' => $menutype
        ]);

        if ($parent['id']) {
            return OrmRequest::make()
                ->delete()
                ->from(new Menu)
                ->where([
                    'alias' => $alias,
                    'parent' => $parent['id'],
                    'menutype' => $menutype
                ])->exec()->affectedRows() > 0;
        }
        return false;
    }

    /**
     * Возвращает пункты меню для заданного root
     *
     * @param integer | string $root ID или ALIAS корневого элемента
     * @param bool $cache - если true, то
     * @return array ['root' => корневой элемент, 'items' => [пункт меню, пункт меню, ...]]
     * @throws OrmException
     * @throws RSException
     */
    function getMenuItems($root, $cache = true)
    {
        $site_id = SiteManager::getSiteId();
        if ($cache) {
            $cache_id = json_encode($this->getAccessFilter()) . $this->queryObj()->where;
            return CacheManager::obj()->request([$this, __FUNCTION__], $root, false, $site_id, $cache_id);
        } else {
            $root_orm = Menu::loadByWhere('(#id = "#root" or #alias = "#root") and site_id = #site_id', [
                'id' => $this->id_field,
                'alias' => $this->alias_field,
                'root' => $root,
                'site_id' => $site_id,
            ]);

            $this->setFilter('public', 1);
            $this->setFilter('menutype', 'user');

            $items = $this->getTreeList((int)$root_orm['id']);

            return [
                'root' => $root_orm,
                'items' => $items
            ];
        }
    }

    /**
     * Возвращает список зарегистрированных в системе типов меню
     *
     * @param bool $cache - использовать кэш
     * @return array
     * @throws EventException
     */
    public static function getMenuTypes($cache = true)
    {
        static $result;

        if (!isset($result) || !$cache) {
            $event_result = EventManager::fire('menu.gettypes', []);
            $result = [];
            foreach ($event_result->getResult() as $type) {
                /** @var AbstractMenuType $type */
                $result[$type->getId()] = $type;
            }
        }

        return $result;
    }

    /**
     * Возвращает массив с идентификатором типа в ключе и названием в значении
     *
     * @param bool $only_visible - только видимые
     * @return array
     */
    public static function getMenuTypesNames($only_visible = true)
    {
        $types = self::getMenuTypes();
        $result = [];
        foreach ($types as $key => $type) {
            if (!$only_visible || $type->isVisible()) {
                $result[$key] = $type->getTitle();
            }
        }
        return $result;
    }

    /**
     * Возвращает описание всех типов меню
     *
     * @param bool $only_visible - только видимые
     * @return string
     */
    public static function getMenuTypeDescriptions($only_visible = true)
    {
        $types = self::getMenuTypes();
        $description = '';
        foreach ($types as $type) {
            if (!$only_visible || $type->isVisible()) {
                $description .= "<b>{$type->getTitle()}</b> - " . $type->getDescription() . '<br>';
            }
        }
        return $description;
    }

    public function checkParent($obj, $post, $ids)
    {
        /** @var AbstractObject $obj */
        if (isset($post['parent'])) {
            $parents_arrs = $this->getPathToFirst($post['parent']);
            foreach ($ids as $n => $id) {
                if (isset($parents_arrs[$id])) {
                    $obj->addError(t('Неверно указан родительский элемент'), 'parent');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * возвращает маршруты, добавляемые пунктами меню
     *
     * @param bool $cache Если true, то результат сперва будет запрашиваться у кэша
     * @return array
     */
    public function getMenuRoutes($cache = true)
    {
        if ($cache) {
            return CacheManager::obj()
                ->watchTables($this->getElement())
                ->request([$this, __FUNCTION__], false, $this->getSiteContext());

        } else {
            $routes = [];
            $this->setFilter('menutype', 'user');
            /** @var Menu[] $list */
            $list = $this->getList();

            foreach ($list as $item) {
                if ($route = $item->getTypeObject()->getRoute()) {
                    $routes[] = $route;
                }
            }

            return $routes;
        }
    }
}
