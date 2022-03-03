<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Admin;
use \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar;


/**
 * Импорт товаров из YML
 *
 */
class ImportYml extends \RS\Controller\Admin\Front
{
    public 
        $api,
        $post_data,
        $helper;
    
    function init()
    {

        $this->api = new \Catalog\Model\ImportYmlApi();
        $this->post_data = [
            'upload_images' => $this->url->request('upload_images', TYPE_INTEGER)
        ];

        $this->helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $this->helper
                ->setTopTitle(t('Импорт товаров из YML'))
                ->viewAsForm();
    }
    
    /**
     * 
     * @return type
     */
    function actionIndex()
    {
        if($this->url->isPost()){
            if(!$this->api->hasError() && ($this->api->uploadFile($this->url->files('ymlfile')))){

                return $this->result
                        ->addSection('callCrudAdd', $this->router->getAdminUrl('ajaxProcess', [
                            'step_data' => [
                                'upload_images' => $this->post_data['upload_images'],
                                'step'          => 0,
                                'offset'        => 0
                            ]]))
                        ->setSuccess(true);
            }
            return $this->result
                    ->setSuccess(false)
                    ->setErrors($this->api->getDisplayErrors());
        }

        $this->helper
            ->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                'save' => new ToolbarButton\SaveForm(null, t('Начать импорт')),
                'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name.'index')),
            ]
            ]));
        
        $this->helper['form'] = $this->view->fetch('%catalog%/importyml/import_form.tpl');

        return $this->result->setTemplate($this->helper['template']);
    }
    
    /**
     * 
     * @return type
     */
    function actionAjaxProcess()
    {

        $step_data = $this->url->request('step_data', TYPE_ARRAY);
        $result = $this->api->process($step_data);
        $this->view->assign([
            'steps' => $this->api->getSteps($step_data),
            'current_step' => $step_data,
            'next_step'    => $result,
            'statistic'    => $this->api->getStatistic(),
            'error' => $this->api->getErrorsStr()
        ]);
        
        $this->helper
            ->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name.'index'), t('Закрыть')),
            ]
            ]));
        
        
        $this->helper['form'] = $this->view->fetch('%catalog%/importyml/import_form_step2.tpl');
        
        if ($step_data['step'] > 0) 
            return $this->result->setHtml($this->helper['form']);
        
        return $this->result
            ->setSuccess(true)
            ->setTemplate($this->helper['template']);
    }
    
    function actionCleanTmp()
    {
        $this->api->cleanTemporaryDir();
        return $this->actionIndex();
    }
}
