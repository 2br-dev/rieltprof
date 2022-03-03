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

class ImportPhotos extends \RS\Controller\Admin\Front
{
    public 
        $api,
        $post_data,
        $helper;
        
    function init()
    {
        $this->api = new \Catalog\Model\ImportPhotosApi();
        $this->post_data = [
            'field' => $this->url->request('field', TYPE_STRING),
            'separator' => $this->url->request('separator', TYPE_STRING, null, null)
        ];
        
        $this->helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $this->helper
            ->setTopTitle(t('Импорт изображений товаров из ZIP архива'))
            ->viewAsForm();        
    }
    
    function actionIndex()
    {
        if ($this->url->isPost()) {           
            $nextstep = $this->url->post('nextstep', TYPE_INTEGER);
            if (empty($this->post_data['separator'])) {
                $this->api->addError(t('Укажите символ, разделяющий свойство товара и номер в имени файла'), t('Символ-разделитель'), 'separator');
            }
            
            if (!$this->api->hasError() && ($nextstep == 3 || $this->api->uploadFile($this->url->files('zipfile')))) {
   
                return $this->result
                        ->addSection('callCrudAdd', $this->router->getAdminUrl('ajaxProcess', $this->post_data + ['step' => $nextstep]))
                        ->setSuccess(true);
                
            } else {
                return $this->result
                                ->setSuccess(false)
                                ->setErrors($this->api->getDisplayErrors());
            }
        }

        $this->helper
            ->setBottomToolbar(new Toolbar\Element( [
            'Items' => [
                'save' => new ToolbarButton\SaveForm(null, t('Начать импорт')),
                'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name.'index')),
            ]
            ]));
        
        $this->view->assign([
            'max_file_size' => \RS\File\Tools::fileSizeToStr(\RS\File\Tools::getMaxPostFileSize()),
            'compare_fields' => $this->api->getCompareProductFields()
        ]);
        $this->helper['form'] = $this->view->fetch('%catalog%/importphotos/import_form.tpl');
        
        return $this->result->setTemplate($this->helper['template']);        
    }
    
    function actionAjaxProcess()
    {
        $pos = $this->url->request('pos', TYPE_INTEGER, 0);
        $step = $this->url->request('step', TYPE_INTEGER, 2);
        $error = null;
        $this->api->resetStatistic();
        
        if ($this->url->isPost()) {
            switch($step) {
                case 2: $result = $this->api->extractFile($pos); break;
                case 3: $result = $this->api->importPhoto($pos, $this->post_data['field'], $this->post_data['separator']); break;
            }

            if ($result === true) {
                $step++;
                $pos = 0;
            }
            elseif ($result === false) {
                $error = $this->api->getErrorsStr();
            } else {
                $pos = $result;
            }
        }

        if ($step < 4 && !$error) {
            $this->view->assign([
                'next_url' => $this->router->getAdminUrl('ajaxProcess', [], null),
                'params' => json_encode($this->post_data + ['pos' => $pos, 'step' => $step]),
            ]);
        }

        $this->view->assign([
            'log_url' => $this->api->getLogUrl(true),
            'info' => $this->api->getParam(),
            'step' => $step,
            'error' => $error
        ]);
        
        $this->helper
            ->setBottomToolbar(new Toolbar\Element( [
            'Items' => [
                'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name.'index'), t('Закрыть')),
            ]
            ]));
        
        $this->helper['form'] = $this->view->fetch('%catalog%/importphotos/import_form_step2.tpl');
                    
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