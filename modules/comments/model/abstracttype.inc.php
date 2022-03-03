<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Comments\Model;

/**
* Абстрактный класс типов комментариев
*/
abstract class Abstracttype implements IType  
{
    protected
        /**
        * Объект комментария
        * 
        * @var \Comments\Model\Orm\Comment
        */
        $comment;
    
    function __construct($comment = null)
    {
        $this->comment = $comment;
    }

    /**
     * Возвращает идентификатор типа комментариев
     *
     * @return string
     */
    function getTypeId()
    {
        return '\\'.get_class($this);
    }
    
    /**
    * Действие при добавлении комментария
    * 
    * @return bool
    */
    function onDelete()
    {}
    
    
    /**
    * Действие при удалении комментария
    * 
    * @return bool
    */    
    function onAdd()
    {}
}

