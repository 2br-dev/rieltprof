<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Tags\Config;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this->bind('orm.init.article-article');
    }
    
    public static function ormInitArticleArticle($orm)
    {
        $orm->getPropertyIterator()->append([
            t('Тэги'),
                '_tags_' => new \RS\Orm\Type\UserTemplate('%tags%/tab_tags.tpl')
        ]);
    }
    
}

