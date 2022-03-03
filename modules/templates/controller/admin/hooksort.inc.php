<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Controller\Admin;

/**
* Настройка сортировки модулей, обрабатывающих хуки в шаблонах
*/
class HookSort extends \RS\Controller\Admin\Front
{    
    public
        $api;
    
    function init()
    {
        $this->api = new \Templates\Model\TemplateHookSortApi();
    }
    
    function actionIndex()
    {
        $toolgroup = $this->url->get('toolgroup', TYPE_STRING, 0);
        
        $group = \RS\Debug\Group::getInstance($toolgroup);
        $template_hooks = $group->getData('hooksort', 'hooks', []);
        
        if ($this->url->isPost()) {            
            $sort_data = $this->url->request('sort_data', TYPE_ARRAY);            
            if ($this->api->saveSortData($sort_data)) {
                return $this->result->setSuccess(true);
            } else {
                return $this->result->setSuccess(false)->setErrors( $this->api->getDisplayErrors() );
            }
        }

        $hooks = new \RS\View\Hooks();        
        $this->view->assign([
            'template_hooks' => $template_hooks,
            'hook_handlers' => $hooks->getHookHandlers(false)
        ]);
        
        $collection = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $collection->setBottomToolbar(new \RS\Html\Toolbar\Element([
            'Items' => [
                new \RS\Html\Toolbar\Button\SaveForm(),
                new \RS\Html\Toolbar\Button\Cancel(''),
                new \RS\Html\Toolbar\Button\Button($this->router->getAdminUrl('ajaxresetsort', ['toolgroup' => $toolgroup]), t('Сброс'), [
                    'attr' => [
                        'class' => 'crud-get crud-close-dialog'
                    ]
                ])
            ]
        ]));
        
        $collection->viewAsForm();
        $collection->setTopTitle(t('Порядок модулей'));
        $collection['form'] = $this->view->fetch('hook_sort.tpl');
        
        return $this->result->setTemplate( $collection['template'] );
    }
    
    function actionAjaxResetSort()
    {
        $toolgroup = $this->url->get('toolgroup', TYPE_STRING, 0);
        $group = \RS\Debug\Group::getInstance($toolgroup);
        $template_hooks = $group->getData('hooksort', 'hooks', []);
        
        $this->result->setSuccess($this->api->resetBySortData($template_hooks));
        if (!$this->result->isSuccess()) {
            $this->result->setErrors( $this->api->getDisplayErrors() );
        }
        return $this->result;
    }
}
