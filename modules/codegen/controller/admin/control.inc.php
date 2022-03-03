<?php
namespace CodeGen\Controller\Admin;

use CodeGen\Model\GenerateModuleForm;
use CodeGen\Model\ModuleGenerator;
use CodeGen\Model\ModuleParams;
use RS\Router\Manager;
use \RS\Html\Toolbar\Button as ToolbarButton;
use \RS\Html\Toolbar;

/**
* Контроллер Управление списком магазинов сети
*/
class Control extends \RS\Controller\Admin\Front
{
    function __construct()
    {
        parent::__construct();
    }


    function actionGenerateModule()
    {        
        $form_object = new GenerateModuleForm();

        if($this->url->isPost()) {
            $this->result->setSuccess( $form_object->checkData() );

            if($this->result->isSuccess())
            {
                $params = new ModuleParams();
                foreach($params as $key=>$ignored) {
                    $params->{$key} = $form_object[$key];
                }

                $module_generator = new ModuleGenerator();
                $module_generator->deployModule($params);

                if(!$module_generator->hasError())
                {
                    $this->result
                            ->setSuccessText(t('Модуль успешно создан'))
                            ->addMessage(t("Модуль успешно сохранен в папку %0", [
                        \Setup::$MODULE_FOLDER.'/'.strtolower($params->name)
                            ]));
                    return $this->result;
                }
            }
            else
            {
                $this->result->setErrors($form_object->getDisplayErrors());
            }
        }
        
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);        
        $helper
            ->setTopTitle( t('Генератор нового модуля') )
            ->viewAsForm()
            ->setBottomToolbar(new Toolbar\Element( [
                'Items' => [
                    new ToolbarButton\SaveForm(),
                    new ToolbarButton\Cancel($this->router->getadminUrl(false, [], 'modcontrol-control'))
                ]
            ]))
            ->setFormObject($form_object);

        $this->view->assign([
            'elements' => $helper
        ]);
        
        return $this->result->setTemplate($helper['template']);
    }

}
