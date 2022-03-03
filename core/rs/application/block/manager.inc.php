<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Application\Block;

/**
* Менеджер блоков на странице. Данный класс используется для рендеринга текущей страницы.
* Во время рендеринга учитываются сведения о странице из раздела Веб-сайт->Конструктор сайта.
* Если страница собрана по сетке, то происходит генерация сетки и рендеринг блоков (блок-контроллеров) модулей, 
* иначе рендерится шаблон, заданный для текущей страницы в Конструкторе сайта.
*/
class Manager
{
    /**
     * @var \RS\View\Engine $view
     */
    protected $view;
    protected $route_id; //id текущего маршрута
    protected $main_content; //Содержимое страницы
        
    /**
    * Устанавливает объект шаблонизатора
    * 
    * @param \RS\View\Engine $view - объект шаблонизатора
    * @return Manager
    */
    function setView(\RS\View\Engine $view)
    {
        $this->view = $view;
        return $this;
    }    
    
    /**
    * Устанавливает текущую страницу, для которой в дальнейшем будет получен контент
    * 
    * @param mixed $route_id - ID маршрута
    * @return Manager
    */
    function setRouteId($route_id)
    {
        $this->route_id = $route_id;
        return $this;
    }
    
    /**
    * Устанавливает основное содержимое страницы
    * 
    * @param mixed $content - html, возвращенный фронт-контроллером
    * @return Manager
    */
    function setMainContent($content)
    {
        $this->main_content = $content;
        return $this;
    }
    
    /**
    * Возвращает основное содержимое страницы
    * @return mixed
    */
    function getMainContent()
    {
        return $this->main_content;
    }

    /**
     * Рендерит текущую страницу с учетом настроек блоков в административной панели,
     * возвращает готовый HTML для вставки в шаблон.
     *
     * @return string
     * @throws \RS\Event\Exception
     * @throws \SmartyException
     */
    function renderLayout()
    {
        //Кэшируем данные для построения сетки
        $theme_data = \RS\Theme\Manager::getCurrentTheme();
        $layouts = \RS\Cache\Manager::obj()
                        ->tags(CACHE_TAG_BLOCK_PARAM)
                        ->request([$this, 'getLayoutData'], $this->route_id, $theme_data, \RS\Site\Manager::getSiteId());
        
        if (is_string($layouts)) {
            //Если у страницы задан конкретный шаблон, то рендерится только он.
            $html = $this->view->fetch($layouts);
        } else {
            //Если у страницы заданы блоки, то происходит процесс сборки блоков
            $this->view->assign('layouts', $layouts);
            $html = $this->view->fetch('%system%/gs_maker.tpl');
        }
        return \RS\Event\Manager::fire('render.beforeoutput', $html)->getResult();
    }

    /**
     * Данные, для построения сетки
     *
     * @param string $route_id - ID маршрута
     * @param array $theme_data - Данные текущей темы оформления
     *
     * @return array
     * @throws \RS\Orm\Exception
     */
    function getLayoutData($route_id, $theme_data)
    {
        $page         = \Templates\Model\Orm\SectionPage::loadByRoute($route_id, $theme_data['blocks_context']);
        $default_page = \Templates\Model\Orm\SectionPage::loadByRoute('default', $theme_data['blocks_context']);
        
        if (!$page['route_id']) {
            $page = $default_page;
        }
        
        if (!empty($page['template'])) {
            return $page['template'];
        }

        $result_structure = [];
        $result_structure['page_id']         = $page['id'];
        $result_structure['default_page_id'] = $page['id'];
        $result_structure['theme_data']      = $theme_data;

        foreach($page->getContainers() as $container_type => $data)
        {
            /**
             * @var \Templates\Model\Orm\SectionContainer $container
             */
            $container = $data['object'];
            if (!$container && $data['defaultObject']) {
                $container = $data['defaultObject'];
            }
            
            if ($container) {
                $result_structure['containers'][] = $container;                
                $result_structure['sections'][$container['id']] = $container->getSections();
            }
        }
        $result_structure['blocks'] = \Templates\Model\Orm\SectionModule::getPageBlocks($page['id'], true);
        $result_structure['blocks'] += \Templates\Model\Orm\SectionModule::getPageBlocks($default_page['id'], true);

        $result_structure['max_container_type'] = $page->max_container_type; //Количество контейнеров

        //Получаем тип сеточного фреймворка для текущей темы оформления
        $theme = new \RS\Theme\Item($theme_data['full_name']);
        $result_structure['grid_system'] = $theme->getGridSystem();
        
        return $result_structure;        
    }

}

