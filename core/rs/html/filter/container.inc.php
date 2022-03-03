<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter;

use RS\Html\Filter\Type\AbstractType;
use RS\View\Engine;

/**
* Контейнер, содержащий линии с элементами фильтра
*/
class Container extends AbstractContainer
{
    public     
        $work = false,
        $clearlink = '';
    
    protected    
        $cache_items = [],
        $tpl = 'system/admin/html_elements/filter/container.tpl',
        $sec_containers = [];
        
    /**
    * Добавляет дополнительные контейнеры
    * 
    * @param array of SecContainer $containers - массив с дополнительными контейнерами
    * @return Container
    */
    function addSecContainers(array $containers)
    {
        foreach($containers as $container)
            $this->addSecContainer($container);
            
        return $this;
    }
    
    /**
    * Добавляет один дополнительный контейнер
    * 
    * @param Seccontainer $cont
    * @return Container
    */
    function addSecContainer(Seccontainer $cont)
    {
        if (isset($this->lines[0])) {
            $this->lines[0]->setOption('has_second_containers', true);
        }
        $this->sec_containers[] = $cont;
        
        return $this;
    }
        
    /**
    * Возвращает массив дополнительных контейнеров
    * 
    * @return array
    */
    function getSecContainers()
    {
        return $this->sec_containers;
    }
    
    /**
    * Добавляет линию, содержащую элементы форм
    * 
    * @param Line $line
    * @return AbstractContainer
    */
    function addLine(Line $line)
    {
        if (!count($this->lines)) { //Если это первая строка главного контейнера, то сообщаем ей об этом
            $line->setOption('is_first', true);
        }
        return parent::addLine($line);
    }    
    
    /**
    * Возвращает массив со всеми объектами \Html\Filter\Type\......, находящимися в контейнере
    * 
    * @return AbstractType[]
    */
    function getAllItems($with_prefilters = true)
    {
        if (!isset($this->cache_items[(int)$with_prefilters]))
        {
            $mk = microtime(true);
            //Собираем объекты форм в основном контейнере
            $all_items = [];
            foreach ($this->lines as $line)
                foreach ($line->getItems() as $item)
                {
                    $all_items[$item->getKey()] = $item;
                    if ($with_prefilters) {
                        $all_items = array_merge($all_items, $item->getPrefilters()); //у каждого элемента формы могут быть "префильтры", т.е. тоже формы
                    }
                }
            //Собираем объекты форм в дополнительных контейнерах
            foreach ($this->getSecContainers() as $cont)
                foreach($cont->getLines() as $lines)
                    foreach($lines->getItems() as $item)
                    {
                        $all_items[$item->getKey()] = $item;
                        if ($with_prefilters) {
                            $all_items = array_merge($all_items, $item->getPrefilters());
                        }
                    }
            $this->cache_items[(int)$with_prefilters] = $all_items;
        }
        return $this->cache_items[(int)$with_prefilters];
    }
    
    /**
    * Очищает кэш всех элементов фильтра. Необходимо вызывать, при добавлении новых элементов фильтра
    * 
    * @return Container
    */
    function cleanItemsCache()
    {
        $this->cache_items = [];
        return $this;
    }
    
    /**
    * Возвращает HTML код контейнера
    * @return string
    */
    function getView()
    {
        $tpl = new Engine();
        $tpl->assign('fcontainer', $this);
        return $tpl->fetch($this->tpl);
    }
}

