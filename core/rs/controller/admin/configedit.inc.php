<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller\Admin;
use \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar;

/**
* Стандартный контроллер для редактирования объектов конфигураций.
*/
abstract class ConfigEdit extends \RS\Controller\Admin\Front
{
    protected
        $orm;
    
    function __construct(\RS\Orm\AbstractObject $object)
    {
        parent::__construct();
        $this->orm = $object;
    }
    
    function actionIndex()
    {
        //Если пост идет для текущего модуля
        if ($this->url->isPost()) 
        {            
            $this->result->setSuccess( $this->orm->save() );

            if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                if (!$this->result->isSuccess()) {
                    $this->result->setErrors($this->orm->getDisplayErrors());
                } else {
                    $this->result->setSuccessText(t('Изменения успешно сохранены'));
                }
                return $this->result;
            }
            
            if ($this->result->isSuccess()) {
                $this->successSave();
            } else {
                $error = $this->orm->getLastError();
            }
        } 
        $helper = $this->helperIndex();
        $helper['form'] = $this->orm->getForm();
        
        $this->view->assign([
            'elements' => $helper->active(),
            'errors' => isset($error) ? $error : []
        ]);
        return $this->result->setHtml($this->view->fetch( $helper['template'] ))->getOutput();        
    }
    
    function helperIndex()
    {
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this, null, $this->url);
        $helper->setBottomToolbar(new Toolbar\Element( [
            'Items' => [
                'save' => new ToolbarButton\ApplyForm(),
                'cancel' => new ToolbarButton\Cancel($this->router->getAdminUrl(false, null, false))
            ]]));
        $helper->viewAsForm();
        return $helper;
    }
}

