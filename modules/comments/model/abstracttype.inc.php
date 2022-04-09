<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Comments\Model;

use Comments\Model\Orm\Comment;
use RS\Orm\Request;

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

    /**
     * Возвращает количество различных оценок
     *
     * @return array
     */
    function getMarkMatrix()
    {
        $api = new \Comments\Model\Api();
        return $api->getMarkMatrix($this->getLinkId(), $this->getTypeId(), 5);
    }

    /**
     * Возвращает среднюю оценку комментариев
     *
     * @return float
     */
    function getRatingBall()
    {
        $rate = Request::make()
            ->select('AVG(rate)')
            ->from(new Comment())
            ->where([
                'type' => $this->getTypeId()
            ])->exec()->fetchSelected(null, 'AVG(rate)');
        return round($rate[0], 2);
    }
}

