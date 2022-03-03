<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Controller\Admin;

class SelectTemplate extends \RS\Controller\Admin\Front
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
        $start_tpl = $this->url->get('start_tpl', TYPE_STRING);
        $path = $this->url->get('path', TYPE_STRING, $this->api->getPathFromSession());

        if ($path == '') {
            $theme = \RS\Theme\Manager::getCurrentTheme('theme');
            $path = "theme:{$theme}";
        }
        
        if ($struct = $this->api->parseStartTpl($start_tpl)) {
            $path = $struct['path'];
        }

        $only_themes = $this->url->get('only_themes', TYPE_INTEGER, 1);
        $this->api->savePathInSession($path);
        
        $list = $this->api->getFileList($path, ['tpl'], $only_themes);

        $this->view->assign([
            'root_sections'   => $this->api->getRootSections($only_themes), //Корневые рубрики
            'allow_edit_ext'  => $this->api->getAllowEditExtensions(),
            'list'            => $list,
            'only_themes'     => $only_themes,
            'start_struct'    => $struct
        ]);
        
        $this->url->saveUrl('index');        
        $helper = $this->helper;
        $helper->setTopTitle(t('Редактор шаблонов'));

        $this->view->assign('elements', $helper);
        return $this->result->setTemplate('select_template.tpl');
    }
}
