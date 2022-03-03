<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Tags\Controller\Admin;

/**
* Контроллер блока тегов
*/
class BlockTags extends \RS\Controller\Admin\Block
{
    protected
        $action_var = 'tdo',
        $min_word_len = 3,
        $api;
        
        
    function init()
    {
        $this->api = new \Tags\Model\Api();
    }
    
    function actionIndex()
    {
        if (!isset($this->param['type']) || !isset($this->param['linkid'])) {
            throw new \RS\Controller\ParameterException(t('Не заданы параметры type или linkid'));
        }
        $this->view->assign('word_list_html', $this->actionGetWords($this->param['type'], $this->param['linkid']));
        return $this->fetch('form.tpl');
    }
    
    function actionAddWords()
    {
        if (!empty($_POST))
        {
            $keywords = $this->url->request('keywords', TYPE_STRING);
            $type = $this->url->request('type', TYPE_STRING);
            $link_id = $this->url->request('link_id', TYPE_INTEGER);
        
            $this->api->addWords($keywords, $type, $link_id);
            $this->result->setSuccess(true);
            return $this->result->getOutput();
        }
        
    }
    
    /**
    * AJAX слова привязанные к объекту
    */
    function actionGetWords($type = null, $link_id = null)
    {
        if (!isset($type)) $type = $this->url->request('type', TYPE_STRING);
        if (!isset($link_id)) $link_id = $this->url->request('link_id', TYPE_INTEGER);
        
        $word_list = $this->api->getWords($type, $link_id);
        $this->view->assign('word_list', $word_list);
        return $this->view->fetch('words.tpl');
    }
    
    /**
    * AJAX для autocomplete
    */
    function actionGetHelpList()
    {
        $term = $this->url->request('term', TYPE_STRING);
        if (mb_strlen($term)>=$this->min_word_len)
        {
            $tmplist = $this->api->getHelpList($term);
            $list = [];
            foreach($tmplist as $item) {
                $list[] = $item['word'];
            }
        } else $list = [];
        return json_encode($list);
    }
    
    function actionDel()
    {
        $lid = $this->url->request('lid', TYPE_INTEGER);
        $linkid = $this->url->request('linkid', TYPE_INTEGER);
        
        $this->result->setSuccess($this->api->delLink($lid, $linkid));
        return $this->result->getOutput();
    }
}

