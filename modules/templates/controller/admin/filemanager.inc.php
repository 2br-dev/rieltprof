<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Controller\Admin;
use \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar;

class FileManager extends \RS\Controller\Admin\Front
{
    protected
        $helper,
        $api;
    
    function init()
    {
        $this->api = new \Templates\Model\FileManagerApi();
        $this->helper = new \RS\Controller\Admin\Helper\CrudCollection($this, null, $this->url);
    }
    
    function actionIndex()
    {
        
        $path = $this->url->get('path', TYPE_STRING, $this->api->getPathFromSession());
        $this->api->savePathInSession($path);
        
        $list = $this->api->getFileList($path);
        $this->view->assign([
            'root_sections' => $this->api->getRootSections(), //Корневые рубрики
            'allow_edit_ext' => $this->api->getAllowEditExtensions(),
            'list' => $list,
        ]);
        
        $this->url->saveUrl('index');        
        $helper = $this->helper;
        $helper->setTopHelp(t('С помощью данного раздела есть возможность корректировать любые шаблоны, используемые в ReadyScript. Для редактирования шаблонов рекомендуется наличие знаний HTML, CSS, Smarty. Используйте инструменты данного раздела для загрузки CSS-стилей, изображений и других файлов, которые могут использоваться в теме оформления.'));
        $helper->setTopTitle(t('Редактор шаблонов'));
        $helper->setTopToolbar(new Toolbar\Element( [
            'Items' => [
                new ToolbarButton\Dropdown([
                    [
                        'title' => t('Создать файл'),
                        'attr' => [
                            'class' => 'btn-success',
                            'onclick' => "$(this).parent().rsDropdownButton('toggle');"
                        ]
                    ],
                    [
                        'title' => t('TPL - шаблон'),
                        'attr' => [
                            'class' => 'crud-add',
                            'href' => $this->router->getAdminUrl('add', ['path' => $list['epath']['public_dir'], 'ext' => 'tpl'])
                        ]
                    ],
                    [
                        'title' => t('JS - скрипт'),
                        'attr' => [
                            'class' => 'crud-add',
                            'href' => $this->router->getAdminUrl('add', ['path' => $list['epath']['public_dir'], 'ext' => 'js'])
                        ]
                    ],
                    [
                        'title' => t('CSS - стиль'),
                        'attr' => [
                            'class' => 'crud-add',
                            'href' => $this->router->getAdminUrl('add', ['path' => $list['epath']['public_dir'], 'ext' => 'css'])
                        ]
                    ]
                ]),
                new ToolbarButton\Space(),
                new ToolbarButton\Button('', t('Создать папку'), ['attr' => [
                    'class' => 'btn-default makedir',
                    'data-url' => $this->router->getAdminUrl('makeDir', ['path' => $list['epath']['public_dir']])
                ]]),
                new ToolbarButton\Button($this->router->getAdminUrl('uploadForm', ['path' => $list['epath']['public_dir']]), t('Загрузить файлы'), ['attr' => ['class' => 'btn-default crud-add']]),
                new ToolbarButton\Button($this->router->getAdminUrl('cloneTheme'), t('Клонировать тему'), ['attr' => ['class' => 'btn-default crud-add']])
            ]
        ]));
        
        $this->view->assign('elements', $helper);

        $this->helper->viewAsAny();
        $this->helper['form'] = $this->view->fetch('file_manager.tpl');

        return $this->result->setTemplate( $this->helper['template'] );
    }
    
    
    function actionAdd($filename = null)
    {
        $helper = $this->helper;        
                
        $path = $this->url->request('path', TYPE_STRING);
        $ext = $this->url->request('ext', TYPE_STRING, 'tpl');
        $overwrite = $this->url->request('overwrite', TYPE_INTEGER);
    
        $epath = $this->api->extractPath($path);
        
        if ($this->url->isPost()) {
            $filename = ltrim($this->url->request('filename', TYPE_STRING), '/');
            
            $fullpath = $this->url->request('basepath', TYPE_STRING).'/'.$filename;
            $content = $this->url->request('content', TYPE_MIXED, '', false);
            
            $this->result->setSuccess($this->api->saveFile($fullpath, $content, $overwrite));
        
            if ($this->result->isSuccess()) {
                $this->result->setSuccessText(t('Файл успешно сохранен'));
            }            
            
            if ($this->url->isAjax()) {
                return $this->result->setErrors($this->api->getDisplayErrors());
            }
            
            if (!$this->result->isSuccess()) {
                $helper['formErrors'] = $this->api->getDisplayErrors();
            }
        }        
        
        if ($filename === null) {
            $fname = 'noname.'.$ext;
            $helper->setTopTitle(t('Добавить файл'));
        } else {
            $fname = $filename;
            list($name, $ext) = \RS\File\Tools::parseFileName($filename, true);
            
            $data['content'] = $this->api->getFileContent($path.$filename);
            $data['overwrite'] = true;
            $helper->setTopTitle(t('Редактировать файл {filename}'), ['filename' => $filename]);
        }
        
        $data['filename'] = $epath['relative_path'].$fname;
        
        $helper
            ->viewAsForm()
            ->setBottomToolbar(new Toolbar\Element( [
                'Items' => [
                    'save' => new ToolbarButton\SaveForm(),
                    'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name.'index')),
                ]
            ]));
            
        $this->view->assign([
            'data' => $data,
            'elements' => $helper,
            'root_sections' => $this->api->getRootSections(), //Корневые рубрики
            'epath' => $epath,
            'ext' => $ext
        ]);
        $helper['form'] = $this->view->fetch('form/file_edit.tpl');        
        return $this->result->setTemplate( $helper['template'] );
    }    
    
    function actionEdit()
    {
        $filename = $this->url->request('file', TYPE_STRING);
        return $this->actionAdd($filename);
    }
    
    
    function actionAjaxDownload()
    {
        $path = $this->url->request('path', TYPE_STRING);
        $this->api->downloadFile($path);
    }
    
    function actionRename()
    {
        $path = $this->url->request('path', TYPE_STRING);
        $new_filename = $this->url->request('new_filename', TYPE_STRING);
        $success = $this->api->rename($path, $new_filename);
        if(!$success){
            foreach($this->api->getErrors() as $error) {
                $this->result->addEMessage($error);
            }
        }         
        return $this->result->setSuccess( $success );
    }
    
    function actionDelete()
    {
        $path = $this->url->request('path', TYPE_STRING);
        $success = $this->api->delete($path);
        if(!$success){
            foreach($this->api->getErrors() as $error) {
                $this->result->addEMessage($error);
            }
        } 
        return $this->result
            ->setSuccess( $success )
            ->setErrors( $this->api->getDisplayErrors() )
            ->setNoAjaxRedirect($this->url->getSavedUrl('index'));
    }
    
    function actionMakeDir()
    {
        $path = $this->url->request('path', TYPE_STRING);
        $name = $this->url->request('name', TYPE_STRING);
        $success = $this->api->makeDir($path, $name);
        if(!$success){
            foreach($this->api->getErrors() as $error) {
                $this->result->addEMessage($error);
            }
        } 
        return $this->result->setSuccess( $success );
    }
    
    function actionUploadForm()
    {
        $path = $this->url->get('path', TYPE_STRING, $this->api->getPathFromSession());
        
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper
            ->setTopTitle(t('Загрузка файлов'))
            ->viewAsForm()
            ->setBottomToolbar(new Toolbar\Element( [
                'Items' => [
                    'save' => new ToolbarButton\SaveForm(null, t('Загрузить'), ['noajax' => true, 'attr' => ['class' => 'btn-success start-upload']]),
                    'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name.'index')),
                ]
            ]));
        
        $this->view->assign([
            'path' => $path,
            'allow_ext' => $this->api->getAllowExt()
        ]);
        
        $helper['form'] = $this->view->fetch('form/uploadfiles.tpl');
        
        return $this->result->setTemplate($helper['template']);
    }
    
    function actionUploadFile()
    {
        $path = $this->url->get('path', TYPE_STRING);
        $normalized_files_post = \RS\File\Tools::normalizeFilePost( $this->url->files('files') );
        
        $items = [];
        foreach($normalized_files_post as $file) {
            $this->api->cleanErrors();
            if ($this->api->uploadFile($path, $file)) {
                $item = [
                    'success' => true
                ];
            } else {
                $item = [
                    'success' => false,
                    'error' => $this->api->getErrorsStr()
                ];
            }
            $items[] = $item;
        }

        return $this->result->addSection('items', $items);
    }
    
    function actionCloneTheme()
    {
        $helpder = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helpder
            ->setTopTitle(t('Клонирование темы оформления'))
            ->viewAsForm()
            ->setBottomToolbar(new Toolbar\Element( [
                'Items' => [
                    'save' => new ToolbarButton\SaveForm(),
                    'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name.'index')),
                ]
            ]));
            
        $clone_api = new \Templates\Model\CloneThemeApi();
        $root_sections = $this->api->getRootSections();
            
        if ($this->url->isPost()) {
            $elem = [
                'source_theme' => $this->url->post('source_theme', TYPE_STRING),
                'new_name' => strtolower($this->url->post('new_name', TYPE_STRING)),
                'new_title' => $this->url->post('new_title', TYPE_STRING),
                'new_author' => $this->url->post('new_author', TYPE_STRING),
                'new_descr' => $this->url->post('new_descr', TYPE_STRING),
                'set_theme' => $this->url->post('set_theme', TYPE_STRING),
            ];
            $this->view->assign('elem', $elem);
            $this->result->setSuccess($clone_api->cloneTheme($elem['source_theme'], 
                                                             $elem['new_name'], 
                                                             $elem['new_title'], 
                                                             $elem['new_author'], 
                                                             $elem['new_descr'], 
                                                             $elem['set_theme']));
            if ($this->result->isSuccess()) {
                return $this->result->setSuccessText(t('Тема успешно клонирована'));
            } else {
                return $this->result->setErrors($clone_api->getDisplayErrors());
            }
        }
        
        $this->view->assign([
            'root_sections' => $root_sections,
            'clone_api' => $clone_api,
        ]);
        
        $helpder['form'] = $this->view->fetch('form/clone_theme.tpl');
        
        return $this->result->setTemplate($helpder['template']);
    }
}

