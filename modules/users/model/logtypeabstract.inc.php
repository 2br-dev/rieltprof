<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Users\Model;

/**
* Абстрактный класс типов события, которые совершает пользователь.
* Когда происходит запись события в лог, указывается класс, который сможет обработать данные по этому событию. 
* Он должен быть наследником данного класса
*/
abstract class LogtypeAbstract
{
    protected
        $id,
        $oid,
        $dateof,
        $user_id,
        $serialized,
        $data;
    
    /**
    * Загружает событие из массива
    * 
    * @param array $data
    */
    function load(array $data)
    {
        $this->id = $data['id'];
        $this->oid = $data['oid'];
        $this->dateof = $data['dateof'];
        $this->user_id = $data['user_id'];
        $this->serialized = $data['_serialized'];
        $this->data = $data;
    }
    
    /**
    * Возвращает дополнительные данные, которые переданы во время возникновения события
    */
    function getData()
    {
        return @unserialize($this->serialized);
    }
    
    /**
    * Возвращает id объекта, к которому привязано событие
    */
    function getObjectId()
    {
        return $this->oid;
    }

    /**
    * Возвращает ID события
    */    
    function getEventId()
    {
        return $this->id;
    }

    /**
    * Возвращает дату события
    */    
    function getEventDate()
    {
        return $this->dateof;
    }
    
    /**
    * Возвращает id пользователя, инициатора события
    */
    function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Возвращает объект пользователя, инициатора события
     *
     * @return Orm\User
     */
    function getUser()
    {
        return new \Users\Model\Orm\User($this->getUserId());
    }

}

