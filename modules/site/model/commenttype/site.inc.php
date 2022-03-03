<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Model\CommentType;

/**
* Тип комментария - коментарий к сайту
*/
class Site extends \Comments\Model\Abstracttype
{
    protected
        $site;
        
    /**
    * Возвращает тип комментария
    */
    function getTitle()
    {
        return t('Комментарий к caйту');
    }
    
    /**
    * Возвращает ссылку на объект в административной части
    * 
    * @return string
    */
    function getAdminUrl()
    {
        return \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->comment['aid']], 'site-control');
    }    
    
    /**
    * Возвращает id сайта, к которому необходимо привязать комментарий
    * 
    * @return integer
    */
    function getLinkId()
    {
        return \RS\Site\Manager::getSiteId();
    }
    
    /**
    * Возвращает связанную с комментарием статью
    * 
    * @return \Site\Model\Orm\Site
    */
    function getLinkedObject()
    {
        if (!isset($this->site)) {
            $this->site = new \Site\Model\Orm\Site($this->comment['aid']);
        }
        return $this->site;
    }
    
    /**
    * Возвращает ссылку в клиентской части на связанную с комментарием статью
    * 
    * @param bool $absolute Если true, то возвращать абсолютную ссылку, иначе относительную
    * @return string
    */
    function getLinkedObjectUrl($absolute = false)
    {
        return $this->getLinkedObject()->getRootUrl($absolute);
    }
    
    /**
    * Возвращает название связанной статьи 
    * 
    * @return string
    */
    function getLinkedObjectTitle()
    {
        return $this->getLinkedObject()->title;
    }    
    
    /**
    * Обновляет поле "рейтинг" у статьи
    * Вызывается при добавлении комментария
    */
    function onAdd()
    {
        $api = new \Comments\Model\Api(); 
        $api->recountItemRatingByComment($this->getLinkedObject(), $this->comment);
        return true;
    }
    
    /**
    * Действие при удалении комментария
    */
    function onDelete()
    {
        $api = new \Comments\Model\Api(); 
        $api->recountItemRatingByComment($this->getLinkedObject(), $this->comment);
    }
}

