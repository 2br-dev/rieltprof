<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Model;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

/**
* API функции для контейнеров, находяхихся на страницах
*/
class ContainerApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new \Templates\Model\Orm\SectionContainer, 
        [
            'loadOnDelete' => true
        ]);
    }

    /**
     * Копирует контейнер со всем содержимым из одной страницы на другую
     *
     * @param integer $from_container - id контейнера источника
     * @param mixed $to_page_id - id страницы приемника контейнера
     * @param mixed $to_container_type - тип контейнера приемника
     * @return bool
     * @throws \RS\Orm\Exception
     */
    function copyContainer($from_container, $to_page_id, $to_container_type)
    {
        if ($acl_err = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->addError($acl_err);
        }
        
        $source   = new Orm\SectionContainer($from_container);
        $sections = $source->getSections();
                
        $source['id']      = null;
        $source['page_id'] = $to_page_id;
        $source['type']    = $to_container_type;
        $result = $source->insert();
        
        $this->copySectionsRecursive($sections, $to_page_id, -$source['type']);
        return $result ? $source['id'] : false;
    }
    
    /**
    * Копирует секции и блоки внутри контейнера
    * 
    * @param array $sections
    * @param integer $to_page_id
    * @param integer $parent
    */
    protected function copySectionsRecursive($sections, $to_page_id, $parent)
    {
        foreach($sections as $section) {
            $current_section = $section['section'];
            $blocks = $current_section->getBlocks();
            
            $current_section['id'] = null;
            $current_section['page_id'] = $to_page_id;
            $current_section['parent_id'] = $parent;
            $current_section->insert();

            if (!empty($section['childs'])) {
                $this->copySectionsRecursive($section['childs'], $to_page_id, $current_section['id']);
            } else {
                //Копируем модули со всеми настройками в секции
                foreach($blocks as $block) {
                    $block['id'] = null;
                    $block['page_id'] = $to_page_id;
                    $block['section_id'] = $current_section['id'];
                    $block->insert();
                }
            }
        }
    }
    
    /**
    * Исправляет сортировочные индексы всех блоков в конструкторе
    */
    function fixBloksSortn()
    {
        $this->fixSectionsSortn();
        $this->fixModulesSortn();
    }
    
    /**
    * Исправляет сортировочные индексы секций в конструкторе
    */
    protected function fixSectionsSortn()
    {
        $sections = \RS\Orm\Request::make()
            ->select('id, parent_id', 'page_id')
            ->from(new Orm\Section())
            ->orderby('sortn')
            ->exec()->fetchAll();
        // Группируем секции в массив [страница][контейнер][новый индекс]
        $grouped_sections = [];
        foreach ($sections as $section) {
            $grouped_sections[$section['page_id']][$section['parent_id']][] = $section['id'];
        }
        // Собираем сгруппированный массив для запроса к БД
        $values = [];
        foreach ($grouped_sections as $page) {
            foreach ($page as $container) {
                foreach ($container as $key=>$section_id) {
                    $values[] = "($section_id,$key)";
                }
            }
        }
        // Массово обновляем sortn секций
        $section_orm = new Orm\Section();
        $sql_table = $section_orm->_getTable();
        $sql_values = implode(',', $values);
        $sql = "insert into $sql_table (id,sortn) values $sql_values on duplicate key update sortn=values(sortn);";
        \RS\Db\Adapter::sqlExec($sql);
    }
    
    /**
    * Исправляет сортировочные индексы модулей в конструкторе
    */
    protected function fixModulesSortn()
    {
        $modules = \RS\Orm\Request::make()
            ->select('id, section_id')
            ->from(new \Templates\Model\Orm\SectionModule())
            ->orderby('sortn')
            ->exec()->fetchSelected('id', 'section_id');
        // Группируем модули в массив [секция][новый индекс]
        $grouped_modules = [];
        foreach ($modules as $id=>$section_id) {
            $grouped_modules[$section_id][] = $id;
        }
        // Собираем сгруппированный массив для запроса к БД
        $values = [];
        foreach ($grouped_modules as $section) {
            foreach ($section as $key=>$module_id) {
                $values[] = "($module_id,$key)";
            }
        }
        // Массово обновляем sortn модулей
        $module_orm = new \Templates\Model\Orm\SectionModule();
        $sql_table = $module_orm->_getTable();
        $sql_values = implode(',', $values);
        $sql = "insert into $sql_table (id,sortn) values $sql_values on duplicate key update sortn=values(sortn);";
        \RS\Db\Adapter::sqlExec($sql);
    }
}