<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photo\Controller\Admin;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

/**
* Добавление фотографий к произвольному объекту
*/
class BlockPhotos extends \RS\Controller\Admin\Block
{
    protected
        $api,
        $default_params = [
            'indexTemplate' => 'form.tpl', //Шаблон по умолчанию
            'photoTemplate' => 'form_onepic.tpl', //Шаблон одной картинки
    ],
        $action_var = 'pdo';
        
    protected static 
        $counter = 1;
        
    function __construct($param = [])
    {
        $param += ['uniq' => self::$counter++];
        parent::__construct($param);
        $this->api = new \Photo\Model\PhotoApi();
    }
        
    function actionIndex()
    {
        $this->view->assign([
            'addParam' => ['linkid' => $this->param['linkid'], 'type' => $this->param['type']],
            'photo_list_html' => $this->actionGetPhoto($this->param['linkid'], $this->param['type'])
        ]);
        return $this->fetch( $this->getParam('indexTemplate') );
    }
    
    /**
    * AJAX 
    * Добавление фото и привязка его к нужному объекту
    */
    function actionAddphoto()
    {
        @set_time_limit(0);
        $linkid   = $this->url->request('linkid', TYPE_INTEGER);
        $type     = $this->url->request('type', TYPE_STRING);
        $redirect = $this->url->request('redirect', TYPE_STRING);
        $title    = $this->url->request('title', TYPE_STRING);

        
        $items = [];
        $files = $this->url->files('files', TYPE_ARRAY, []);
        if (!empty($files))
        {
            $photo_api = new \Photo\Model\PhotoApi();
            $normalize_files = [];
            
            for($i=0; $i<count($files['name']); $i++) {
                $normalize_files[] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                ];
            }
            
            foreach ($normalize_files as $key => $file)
            {
                if ($file['error'] != UPLOAD_ERR_NO_FILE) {
                    $photo_api->cleanUploadError();                    
                    $photo = $photo_api->uploadImage($file, $type, $linkid, $title);
                    
                    $items[$key] = [
                        'success' => ($photo !== false),
                        'errors' => $photo_api->getUploadError(),
                    ];
                    
                    if ($photo !== false) {
                        $this->view->assign('photo_list', [$photo]);
                        $items[$key]['html'] = $this->view->fetch( ($type!='catalog') ? $this->getParam('photoTemplate') : '%catalog%/form/product/photo/form_onepic_product.tpl');
                    }
                }
            }
        } else {
            $this->result->addSection('no_files', true);
        }
        
        $this->result->addSection('items', $items);
        $this->result->setNoAjaxRedirect($redirect);
        if (!empty($err)) {
            $this->result->setErrors($err);
        }
        return $this->result->getOutput();
    }
    
    /**
    * AJAX
    */
    function actionEditPhoto()
    {
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_UPDATE)) return $this->result->getOutput();
        
        $photoid = $this->url->request('photoid', TYPE_INTEGER, 0);
        $photo = new \Photo\Model\Orm\Image($photoid);
        if ($photo['id']) {
            $this->result->setSuccess( $photo->save($photoid) );
        }
        
        return $this->result->getOutput();
    }
    
    /**
    * AJAX
    * Возвращет HTML со списком фотографий
    */
    function actionGetPhoto($linkid = null, $type = null)
    {
        if (!isset($linkid) && !isset($type)) {
            $linkid = $this->url->request('linkid', TYPE_INTEGER);
            $type = $this->url->request('type', TYPE_STRING);
        }
        
        $this->api->setFilter('linkid', $linkid);
        $this->api->setFilter('type', $type);
        
        $photoList = $this->api->getList();
        
        $this->view->assign('photo_list', $photoList);
        return $this->view->fetch( $this->getParam('photoTemplate') );
    }
    
    
    /*
    * AJAX
    */
    function actionDelPhoto()
    {
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_DELETE)) return $this->result->getOutput();
        
        $photos = $this->url->request('photos', TYPE_ARRAY);
        if (empty($photos)) return false;
        
        $photo = new \Photo\Model\Orm\Image();
        foreach ($photos as $photoid)
        {
            $photo->load($photoid);
            $photo->delete();
        }
        return $this->result->setSuccess(true)->getOutput();
    }
    
    /**
    * AJAX
    */
    function actionMovePhoto()
    {
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_UPDATE)) return $this->result->getOutput();
        
        $photoid = $this->url->request('photoid', TYPE_INTEGER, 0);
        $position = $this->url->request('pos', TYPE_INTEGER);

        $photo = new \Photo\Model\Orm\Image($photoid);
        $photo->moveToPosition($position);
        
        return $this->result->setSuccess(true)->getOutput();
    }
    
    /**
    * Перевернуть изображение
    */
    function actionRotate()
    {
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_UPDATE)) return $this->result->getOutput();
        
        $direction = $this->url->request('direction', TYPE_STRING, 'cw');
        $photoid = $this->url->request('photoid', TYPE_INTEGER);
        
        $photo = new \Photo\Model\Orm\Image();
        if ($photo->load($photoid)) {
            $angle = ($direction == 'ccw') ? 90 : -90;
            $photo->rotate($angle);
        }
        
        return $this->result->setSuccess(true)->getOutput();
    }
    
    function noWriteRights($right)
    {
        if ($acl_err = Rights::CheckRightError($this, $right)) {
            $this->result
                ->setSuccess(false)
                ->setErrors([$acl_err]);
            return true;
        }
        return false;
    }
    
}

