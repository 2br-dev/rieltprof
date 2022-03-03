<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Controller\Admin\Block;

class Files extends \RS\Controller\Admin\Block
{
    protected
        $action_var = 'files_do';
    
    public
        $file_api,
        $link_type,
        $link_id;
    
    function init()
    {
        $this->file_api = new \Files\Model\FileApi();
        $type_class_name = $this->myRequest('link_type', TYPE_STRING, $this->getParam('link_type'));
        $this->link_type = $this->file_api->getTypeClassInstance($type_class_name);        
        $this->link_id = $this->url->request('link_id', TYPE_INTEGER, $this->getParam('link_id'));        
        
        $this->view->assign([
            'link_type' => $type_class_name,
            'link_id' => $this->link_id
        ]);
    }
    
    /**
    * Возвращает список загруженных файлов
    */
    function actionIndex()
    {
        $this->file_api->setFilter([
            'link_type_class' => $this->link_type->getShortName(),
            'link_id' => $this->link_id
        ]);
        
        $this->view->assign([
            'max_upload_size' => \RS\File\Tools::getMaxPostFileSize(),
            'files' => $this->file_api->getList()
        ]);
        return $this->result->setTemplate('filesblock.tpl');
    }
    
    /**
    * Загружает файл на сервер
    */
    function actionUpload()
    {
        $files = $this->url->files('files', TYPE_ARRAY, []);
        $items = $this->file_api->uploadFromPost($files, $this->link_type, $this->link_id);
        
        foreach($items as &$item) {
            if ($item['success']) {
                $item['html'] = $this->view->assign('linked_file', $item['file'])->fetch('one_file.tpl');
                unset($item['file']);
            }
        }
        return $this->result->addSection('items', $items);
    }
    
    /**
    * Редактрование файла
    */
    function actionEdit()
    {
        $id = $this->url->request('file', TYPE_INTEGER);
        $file = $this->file_api->getElement();
        if ($id && !$file->load($id)) {
            return $this->e404(t('Файл не найден'));
        }
        
        if ($this->url->isPost()) {
            $this->result->setSuccess( $this->file_api->save($id) );
            if ($this->result->isSuccess()) {
                $html = $this->view->assign('linked_file', $file)->fetch('one_file.tpl');
                $this->result->addSection('html', $html);
            } else {
                //Ошибка сохранения формы
                $this->result->setErrors($this->file_api->getElement()->getDisplayErrors());
            }
            
            return $this->result;
        }
        
        $form = $file->getForm(null, null, false, null, '%files%/form_maker.tpl');
        return $this->result->setHtml($form);
    }
    
    /**
    * Смена уровня доступа одного файла
    */
    function actionChangeAccess()
    {
        $id = $this->url->post('file', TYPE_INTEGER);
        $access = $this->url->post('access', TYPE_STRING);
        
        $this->result->setSuccess($this->file_api->changeAccess($id, $access));
        if (!$this->result->isSuccess()) {
            $this->result->addEMessage($this->file_api->getErrorsStr());
        }
        return $this->result;
    }
    
    /**
    * Удаляет файлы
    */
    function actionDelete()
    {
        $ids = $this->url->post('files', TYPE_ARRAY);
        
        $this->result->setSuccess($this->file_api->deleteFiles($ids));
        if (!$this->result->isSuccess()) {
            return $this->result->addEMessage($this->file_api->getErrorsStr());
        }
        return $this->result;
    }
    
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $flag = $this->url->request('flag', TYPE_STRING);
        
        $this->result->setSuccess(
            $this->file_api->moveFileElement($from, $to, $flag, $this->link_type, $this->link_id)
        );
        
        return $this->result;
    }
}
