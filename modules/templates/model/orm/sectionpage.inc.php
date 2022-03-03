<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Templates\Model\Orm;
use \RS\Orm\Type;

/**
 * Страница, привязанная к определенному маршруту (URL).
 * Содержит настройки рендеринга страницы.
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $route_id Маршрут
 * @property string $context Дополнительный идентификатор темы
 * @property string $template Шаблон
 * @property integer $inherit Наследовать шаблон по-умолчанию?
 * --\--
 */
class SectionPage extends \RS\Orm\OrmObject
{
    public
        $max_container_type;
    
    protected static
        $table = 'section_page';
    
    function __construct($id = null)
    {
        parent::__construct();
        if ($id !== null) $this->load($id);        
    }
    
    function _init()
    {
        parent::_init();
        
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite([
                'no_export' => true
            ]),
            'route_id' => new Type\Varchar([
                'maxLength' => '255',
                'allowempty' => false,
                'description' => t('Маршрут'),
                'hint' => t('Маршрут означает определенный тип страниц, Например: "просмотр товара", или "список продукции"'),
                'List' => [[__CLASS__, 'getRouteSelectList']],
                'Attr' => [['size' => 1]],
            ]),
            'context' => new Type\Varchar([
                'maxLength' => 32,
                'description' => t('Дополнительный идентификатор темы'),
                'visible' => false
            ]),
            'template' => new Type\Template([
                'maxLength' => '255',
                'description' => t('Шаблон'),
                'hint' => t('Указанный шаблон будет использован вместо блоков. Возможность разметить страницу блоками в этом случае будет отключена. Указывайте произвольный шаблон в случае, если макет невозможно сверстать с помощью gs960.css или bootstrap'),
            ]),
            'inherit' => new Type\Integer([
                'maxLength' => 1,
                'default' => 1,
                'description' => t('Наследовать шаблон по-умолчанию?'),
                'checkboxView' => [1,0]
            ])
        ]);
        
        $this->addIndex(['site_id', 'route_id', 'context'], self::INDEX_UNIQUE);
    }
    
    public static function getRouteSelectList()
    {
        $routes = \RS\Router\Manager::obj()->getRoutes();
        $result = [];
        $result['default'] = \RS\Router\Manager::obj()->getRoute('default')->getDescription();
        foreach($routes as $id => $route) {
            if (!$route->isHidden()) {
                $result[$id] = $route->getDescription();
            }
        }

        asort($result);
        return $result;
    }
    
    /**
    * Возвращает объект страницы для нужного маршрута
    * 
    * @param mixed $route_id
    * @param string $context - дополнительный идентификатор темы
    * @param integer $site_id - ID сайта
    */
    public static function loadByRoute($route_id, $context = 'theme', $site_id = null)
    {
        if ($site_id === null) {
            $site_id = \RS\Site\Manager::getSiteId();
        }
        $result = self::loadByWhere([
            'site_id' => $site_id,
            'route_id' => $route_id,
            'context' => $context
        ]);
                
        if (!$result['id'] && $route_id == 'default') { //страница для default Должна существовать всегда
            $page = new self();
            $page['site_id'] = $site_id;
            $page['route_id'] = $route_id;
            $page['context'] = $context;
            $page->insert();
            return $page;
        }
        return $result;
    }
    
    /**
    * Возвращает объект \Main\Orm\PageSeo если такой существует для данной страницы
    * @return \Main\Orm\PageSeo | null
    */
    public function getSeo()
    {
        $seo_api = new \PageSeo\Model\PageSeoApi();
        $seo_api->setFilter('route_id', $this['route_id']);
        return $seo_api->getFirst();
    }
    
    /**
    * Возвращает объект маршрута, к которому привязана страница или NULL, если маршрут не найден
    * @return \RS\Router\RouteAbstract | null
    */
    public function getRoute()
    {
        $route = \RS\Router\Manager::getRoute($this['route_id']);
        return $route;
    }
    
    protected function loadContainers($page_id)
    {
        if (empty($page_id)) return [];
        return \RS\Orm\Request::make()
            ->from(new SectionContainer())
            ->where(['page_id' => $page_id])
            ->orderby('type')
            ->objects(null, 'type');
    }
    
    /**
    * Возвращает контейнеры, которые находятся на этой странице
    */
    public function getContainers($route_id = null)
    {
        $default_containers = $loaded_containers = $this->loadContainers($this['id']);
        
        $last = end($loaded_containers);
        $max = $last ? $last['type'] : 0;
        
        if ($this['route_id'] != 'default' && $this['inherit']) {
            $page = self::loadByRoute('default', $this['context']);
            $default_containers = $this->loadContainers($page['id']);
            $last = end($default_containers);
            $default_max = $last ? $last['type'] : 0;
            if ($default_max > $max) $max = $default_max;
        }
        
        $containers = [];
        $this->max_container_type = $max;
        for($type=1; $type <= $max; $type++)
        {
            $containers[$type] = [
                'type' => $type,
                'defaultObject' => isset($default_containers[$type]) ? $default_containers[$type] : false,
                'object' => isset($loaded_containers[$type]) ? $loaded_containers[$type] : false
            ];
        }
        
        return $containers;
    }
    
    function delete()
    {
        //Удаляем все контейнеры, которые находятся внутри данного
        $sub_containers = \RS\Orm\Request::make()
            ->from(new SectionContainer())
            ->where([
                'page_id' => $this['id']
            ])
            ->objects();
            
        foreach($sub_containers as $container) {
            $container->delete();
        }
        
        return parent::delete();
    }    
    
}


