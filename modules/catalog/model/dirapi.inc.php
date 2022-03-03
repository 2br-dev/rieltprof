<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;

use Catalog\Model\Orm\Dir;
use RS\Cache\Manager as CacheManager;
use RS\Db\Adapter as DbAdapter;
use RS\Event\Manager as EventManager;
use RS\Helper\Tools;
use RS\Helper\Transliteration;
use RS\Module\AbstractModel\TreeCookieList;
use RS\Module\AbstractModel\TreeList\TreeListOrmIterator;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Tools as OrmTools;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;
use RS\Http\Request as HttpRequest;

class DirApi extends TreeCookieList
{
    const BC_SESSION_VAR = 'BREADCRUMB_DIRID';

    public function __construct()
    {
        parent::__construct(new Dir(), [
            'multisite' => true,
            'parentField' => 'parent',
            'sortField' => 'sortn',
            'idField' => 'id',
            'aliasField' => 'alias',
            'nameField' => 'name',
            'defaultOrder' => 'sortn'
        ]);
    }

    /**
     * Возвращает экземпляр текущего класса
     *
     * @param string $key ID экземпляра класса
     * @return static
     */
    static function getInstance($key = 'default')
    {
        /** @var static $_this */
        $_this = parent::getInstance($key)->clearFilter();
        return $_this;
    }

    /**
     * Функция быстрого группового редактирования
     *
     * @param array $data - ассоциативный массив со значениями обновляемых полей
     * @param array $ids - список id объектов, которые нужно обновить
     * @return bool|int
     */
    function multiUpdate(array $data, $ids = [])
    {
        //Добавим событие перед обновлением
        $event_result = EventManager::fire('orm.beforemultiupdate.catalog-dir', [
            'data' => $data, 
            'ids' => $ids, 
            'api' => $this,
        ]);
        if ($event_result->getEvent()->isStopped()) return false;
        list($data, $ids) = $event_result->extract();

        //Обновляем рекоммендуемые товары
        if (isset($data['recommended_arr']) && isset($data['recommended_arr']['product'])){
            $recomended = serialize($data['recommended_arr']);
            OrmRequest::make()
                ->from(new Dir())
                ->set([
                    'recommended' => $recomended
                ])
                ->whereIn('id', $ids)
                ->where([
                    'site_id' => SiteManager::getSiteId()
                ])
                ->update()
                ->exec();
        }
        unset($data['recommended_arr']);

        //Обновляем сопутсвующие товары
        if (isset($data['concomitant_arr']) && isset($data['concomitant_arr']['product'])){
            $concomitant = serialize($data['concomitant_arr']);
            OrmRequest::make()
                ->from(new Dir())
                ->set([
                    'concomitant' => $concomitant
                ])
                ->whereIn('id', $ids)
                ->where([
                    'site_id' => SiteManager::getSiteId()
                ])
                ->update()
                ->exec();
        }
        unset($data['concomitant_arr']);
        
        //Обновляем характеристики списка
        if (isset($data['in_list_properties_arr'])){
            $in_list_properties = serialize($data['in_list_properties_arr']);
            OrmRequest::make()
                ->from(new Dir())
                ->set([
                    'in_list_properties' => $in_list_properties
                ])
                ->whereIn('id', $ids)
                ->where([
                    'site_id' => SiteManager::getSiteId()
                ])
                ->update()
                ->exec();
        }
        unset($data['in_list_properties_arr']);
        
        $ret = parent::multiUpdate($data, $ids);
        //Добавим событие на обновлении
        EventManager::fire('orm.multiupdate.catalog-dir', [
            'ids' => $ids,
            'api' => $this,
        ]);
        return $ret;
    }
    
    public function listWithAll()
    {
        $this->load_parents = true;
        $treedata = $this->getTreeList(0);
        
        //Устанавливаем собственные инструменты спецкатегориям
        $router = RouterManager::obj();
        foreach($treedata as $item) {
            $dir = $item->getObject();
            if ($dir['is_spec_dir'] == 'Y') {
                $dir['treeTools'] = new \RS\Html\Table\Type\Actions('id', [
                    new \RS\Html\Table\Type\Action\Edit($router->getAdminPattern('edit_dir', ['id' => $item['fields']['id']]), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]
                    ]),
                    new \RS\Html\Table\Type\Action\DropDown([
                        [
                            'title' => t('показать на сайте'),
                            'attr' => [
                                '@href' => $router->getUrlPattern('catalog-front-listproducts', ['category' => $item['fields']['alias']]),
                                'target' => 'blank'
                            ]
                        ],
                        [
                            'title' => t('удалить'),
                            'attr' => [
                                '@href' => $router->getAdminPattern('del_dir', [':chk[]' => $item['fields']['id']]),
                                'class' => 'crud-remove-one'
                            ]
                        ]
                    ])
                ]);
            }
        }
        return $treedata;
    }

    /**
     * Возвращает список объектов, в случае, когда установлен фильтр подгружает родительские элементы также.
     *
     * @param integer $page - номер страницы запроса
     * @param integer $page_size - Размер страницы запроса
     * @param string|null $order - тип и направление сортировки
     * @return array
     */
    function getList($page = null, $page_size = null, $order = null)
    {   
        $this->setPage($page, $page_size);
        $this->setOrder($order);
        $list = $this->q->objects($this->obj, 'id');
                
        if ($this->filter_active && $this->load_parents) {
            $list += $this->loadParents($list);
        }
        return $list;
    }

    /**
     * Загружает недостающих родителей элементов
     *
     * @param mixed $list
     * @return Dir[]
     */
    function loadParents($list)
    {
        $parents = [];
        $parent_ids = [];
        foreach($list as $item) {
            if ($item['parent']>0) {
                $parent_ids[$item['parent']] = $item['parent'];
            }
        }
        if (count($parent_ids)) {
            $parents = OrmRequest::make()
                ->from($this->obj_instance)
                ->whereIn('id', $parent_ids)
                ->objects(null, 'id');
            $parents += $this->loadParents($parents);
        }        
        return $parents;
    }
        
    /**
     * @deprecated (19.03)
     * Возвращает полный список категорий и спецкатегорий в плоском списке
     *
     * @param bool $include_root - Если true, то включается корневой элемент, иначе возвращаются только существующие категории
     * @return array
     */
    static function selectList($include_root = true)
    {
        $first = ($include_root) ? [0 => t('Верхний уровень')] : [];
        return self::staticSelectList(0, $first);
    }

    /**
     * @deprecated (19.03)
     */
    function getNospecSelectList()
    {
        $this->setFilter('is_spec_dir', 'N');
        return $this->getSelectList(0);
    }

    /**
     * @deprecated (19.03)
     */
    public static function staticNospecSelectList()
    {
        $_this = new self();
        return $_this->getNospecSelectList();
    }

    /**
     * @deprecated (19.03)
     */
    public static function specSelectList($include_no_select = false)
    {
        $_this = new self();
        $_this->setFilter('is_spec_dir', 'Y');
        return ($include_no_select ? [0 => t('Не выбрано')] : []) + $_this->getSelectList(0);
    }

    /**
     * Возвращает дерево спецкатегорий
     *
     * @param string[] $first_elements - узлы, которые нужно добавить в начало списка
     * @return TreeListOrmIterator
     * @throws \RS\Exception
     */
    public static function staticSpecTreeList($first_elements = [])
    {
        $_this = new self();
        $_this->setFilter('is_spec_dir', 'Y');
        return $_this->getTreeList(0, $_this->getFakeNodesFromStringArray($first_elements));
    }

    /**
     * Возвращает дерево категорий, исключая спецкатегории
     *
     * @param string[] $first_elements - узлы, которые нужно добавить в начало списка
     * @return TreeListOrmIterator
     * @throws \RS\Exception
     */
    public static function staticNoSpecTreeList($first_elements = [])
    {
        $_this = new self();
        $_this->setFilter('is_spec_dir', 'N');
        return $_this->getTreeList(0, $_this->getFakeNodesFromStringArray($first_elements));
    }
    
    function getParentsId($id, $addroot = false)
    {
        if (!isset($this->dirs)) {
            $res = $this->getListAsResource();
            $this->dirs = $res->fetchSelected("{$this->id_field}","{$this->parent_field}"); //Здесь массив [[id] => [parent],....]
        }

        $result = [];
        while(isset($this->dirs[$id])) {
            $result[$id] = $id;
            $id = $this->dirs[$id];
        }
        if ($addroot) {
            $result[0] = 0; //Добавляем корневую категорию, если addroot - true
        }
        return $result;
    }
    
    function save($id = null, array $user_post = [])
    {       
        if ($id !== 0) {
            $ret = parent::save($id, $user_post);
            $ins_id = $this->obj_instance[$this->id_field];
        } else {
            $this->obj_instance->checkData($user_post);
            $this->obj_instance->clearErrors();
            $ins_id = 0; //Для корневой записи
            $ret = true;
        }
        if ($ret) {
            //Сохраняем свойства
            $prop = new Propertyapi();
            $prop->saveProperties($ins_id, 'group', $this->obj_instance['prop']);
        }
        
        return $ret;
    }
    
    /**
    * Обновляет счетчики у каталога товаров. 
    */
    public static function updateCounts()
    {
        $product_table = OrmTools::getTable( new Orm\Product() );
        $dir_table = OrmTools::getTable( new Orm\Dir() );
        $xdir = OrmTools::getTable( new Orm\Xdir() );
                
        $max_level = DbAdapter::sqlExec("SELECT MAX(level) as maxlevel FROM $dir_table")->getOneField('maxlevel', false);
        if ($max_level === false) return false;

        $sql = "UPDATE $dir_table SET itemcount = 0";
        DbAdapter::sqlExec($sql);
        
        $config = \RS\Config\Loader::byModule('catalog');
        $num_filter = ($config['hide_unobtainable_goods'] == 'Y') ? 'AND P.num>0' : '';
        
        $sql = "UPDATE $dir_table D, (SELECT dir_id, COUNT(*) as itemcount FROM $xdir X 
                    INNER JOIN $product_table P ON P.id = X.product_id
                    WHERE P.public=1 $num_filter GROUP BY dir_id) as C
                SET D.itemcount = C.itemcount WHERE D.id = C.dir_id";
                
        DbAdapter::sqlExec($sql); 
        
        //С литьев до корня переносим цифры
        for($i = $max_level; $i > 0; $i--) {
            $sql = "INSERT INTO $dir_table(id, itemcount) 
                    (SELECT B.parent, SUM(B.itemcount) FROM $dir_table B 
                    WHERE B.level='$i' AND B.parent>0 GROUP BY B.parent)
                    ON DUPLICATE KEY UPDATE itemcount = itemcount + VALUES(itemcount)";
            DbAdapter::sqlExec($sql);
        }
    }
    
    /**
    * Обновляет флаг уровня вложенности всем элементам дерева категорий
    */
    public static function updateLevels()
    {
        $true = true;
        
        $dir_table = OrmTools::getTable(new Orm\Dir());        
        
        OrmRequest::make()->update($dir_table)->set("level = NULL")->exec();
        OrmRequest::make()->update($dir_table)->set("level = 0")->where('parent = 0')->exec();
        
        $level = 0;
        while($true) {
            $sql = "INSERT INTO $dir_table(id, level) (SELECT id, '".($level+1)."' FROM $dir_table WHERE parent IN (SELECT id FROM $dir_table WHERE level='{$level}'))
            ON DUPLICATE KEY UPDATE level = VALUES(level)";
            
            DbAdapter::sqlExec($sql);
            $true = DbAdapter::affectedRows()>0;
            $level++;            
        }
    }    
    
    /**
    * Сохраняет последний открытый каталог в сессии
    * 
    * @param integer $dir_id
    */
    function PutInBreadcrumb($dir_id)
    {
        $_SESSION[self::BC_SESSION_VAR] = $dir_id;
    }
    
    /**
    * Возвращает категорию для отображения в навигационной цепочке
    * 
    * @param \Catalog\Model\Orm\Product $product - Объект товара. 
    * Если передан, то будет произведена проверка на допустимость категории в сессии для данного товара
    * 
    * @return integer | bool(false)
    */
    function getBreadcrumbDir(Orm\Product $product = null)
    {
        $dir_id = isset($_SESSION[self::BC_SESSION_VAR]) ? $_SESSION[self::BC_SESSION_VAR] : false;
        
        if ($dir_id && $product) {
            $product->fillCategories();
            if (!in_array($dir_id, $product['xdir'])){
                $dir_id = $product['maindir'];
            }
        }
        
        return $dir_id;
    }
    
    /**
     * Добавляет символьные идентификаторы товарам, у которых они не установлены
     *
     * @param integer $count_def - счетчик по-умолчанию
     * @return array|integer
    */
    function addTranslitAliases($count_def = 0)
    {
        $start_time = time();
        $max_exec_time = \RS\Config\Loader::byModule('main')->csv_timeout;
        $url = HttpRequest::commonInstance();

        $count = $url->request('count',TYPE_INTEGER, $count_def);

        $this->queryObj()
            ->where("(alias IS NULL OR alias='')");

        $res = $this->getListAsResource();
        while($row = $res->fetchRow()) {
            $count++;
            $dir = new Orm\Dir();
            $dir->getFromArray($row);
            $i = 0;
            $ok = false;
            while(!$ok && $i<15) {
                $dir[$this->alias_field] = Transliteration::str2url(Tools::unEntityString($dir['name'])).(($i>0) ? "-$i" : '');
                $ok = $dir->update();
                $i++;
            }
        }
        return $count;
    }

    /**
     * Конвертирует массив с псевдонимами категорий в ID
     *
     * @param array $aliases - список псевдонимов
     * @param bool $cache - Если true, то будет использовано кэширование
     * @return array
     */
    function convertAliasesToId($aliases, $cache = true) {
        if ($cache) {
            return CacheManager::obj()->request([$this, __FUNCTION__], $aliases, false);
        } else {
            foreach($aliases as $k => $id) {
                if ($dir = $this->getByAlias($id)) {
                    $aliases[$k] = $dir['id'];
                }
            }
            
            return $aliases;
        }
    }
}
