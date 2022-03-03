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
* Класс содержит функции по сортировке порядка 
*/
class TemplateHookSortApi extends \RS\Module\AbstractModel\BaseModel
{
    protected
        $site_id,
        $context;
        
    function __construct($site_id = null, $context = null)
    {
        if ($site_id === null) {
            $site_id = \RS\Site\Manager::getSiteId();
        }
        if ($context === null) {
            $context = \RS\Theme\Manager::getCurrentTheme('blocks_context');
        }
        
        $this->setSiteId($site_id);
        $this->setContext($context);
    }
    
    /**
    * Устанавливает ID сайта, в рамках которого 
    * 
    * @param mixed $site_id
    */
    function setSiteId($site_id)
    {
        $this->site_id = $site_id;
    }
    
    /**
    * Устанавливает текущий контекст
    * 
    * @param mixed $context
    */
    function setContext($context)
    {
        $this->context = $context;
    }
    
    /**
    * Возвращает порядок модулей в рамках хуков в шаблонах
    * 
    * @return array
    */
    function getSortData()
    {
        return \RS\Orm\Request::make()
            ->from(new Orm\TemplateHookSort())
            ->where([
                'site_id' => $this->site_id,
                'context' => $this->context
            ])
            ->orderby('hook_name, sortn')
            ->objects(null, 'hook_name', true);
    }
    
    /**
    * Сохраняет порядок модулей, в котором они обрабатывают хуки в шаблонах
    * 
    * @return bool
    */
    function saveSortData($sort_data)
    {
        if (!$this->resetBySortData($sort_data)) {
            return false;
        }
        
        foreach($sort_data as $hook_name => $modules)
        {
            foreach($modules as $i => $module) {
                $hook_sort = new \Templates\Model\Orm\TemplateHookSort();
                $hook_sort['site_id'] = $this->site_id;
                $hook_sort['context'] = $this->context;
                $hook_sort['hook_name'] = $hook_name;
                $hook_sort['module'] = $module;
                $hook_sort['sortn'] = $i++;
                $hook_sort->insert();
            }
        }
        
        return true;
    }
    
    /**
    * Удаляет всю лишнюю информацию
    * 
    * @param array $sort_data
    * @param integer $site_id
    * @param string $content
    */
    function resetBySortData($sort_data, $clean_all_hooks = false)
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_DELETE)) {
            return $this->addError($access_error);
        }        
        
        $hook_names = array_keys($sort_data);
        if ($hook_names) {
            $q = \RS\Orm\Request::make()
                ->delete()
                ->from(new \Templates\Model\Orm\TemplateHookSort())
                ->where([
                    'site_id' => $this->site_id,
                    'context' => $this->context,
                ]);
            
            if (!$clean_all_hooks) {
                $q->whereIn('hook_name', array_keys($sort_data));
            }
            $q->exec();
        }
        
        return true;
    }
}