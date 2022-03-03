<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Controller\Admin;

/**
* Содержит действия по обслуживанию
*/
class Tools extends \RS\Controller\Admin\Front
{
    function actionajaxReIndexArticles()
    {
        $config = $this->getModuleConfig();
        
        $api = new \Article\Model\Api();
        $count = 0;
        $page = 1;
        while($list = $api->getList($page, 200)) {
            foreach($list as $article) {
                $article->updateSearchIndex();
            }
            $count += count($list);
            $page++;
        }
        
        return $this->result->setSuccess(true)->addMessage(t('Обновлено %0 статей', [$count]));
    }
}