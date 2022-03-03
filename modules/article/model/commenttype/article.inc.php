<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Model\CommentType;

/**
* Тип комментария - коментарий к статье
*/
class Article extends \Comments\Model\Abstracttype
{
    protected
        $article;
        
    /**
    * Возвращает тип комментария
    */
    function getTitle()
    {
        return t('Комментарий к статье');
    }
    
    /**
    * Возвращает ссылку на объект в административной части
    * 
    * @return string
    */
    function getAdminUrl()
    {
        return \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->comment['aid']], 'article-ctrl');
    }    
    
    /**
    * Возвращает id товара, к которому необходимо привязать комментарий
    * 
    * @return integer
    */
    function getLinkId()
    {
        $route = \RS\Router\Manager::obj()->getCurrentRoute();
        if ($route->getId() == 'article-front-view') {
            if ($route->getExtra('article_id')) {
                return $route->getExtra('article_id');
            }
        }
        return false;
    }
    
    /**
    * Возвращает связанную с комментарием статью
    * 
    * @return \Article\Model\Orm\Article
    */
    function getLinkedObject()
    {
        if (!isset($this->article)) {
            $this->article = new \Article\Model\Orm\Article($this->comment['aid']);
        }
        return $this->article;
    }
    
    /**
    * Возвращает ссылку в клиентской части на связанную с комментарием статью
    * 
    * @param bool $absolute Если true, то возвращать абсолютную ссылку, иначе относительную
    * @return string
    */
    function getLinkedObjectUrl($absolute = false)
    {
        return $this->getLinkedObject()->getUrl($absolute);
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

