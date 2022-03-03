<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Comments\Model;

/**
* Интерфейс классов описывающих тип комментариев
*/
interface IType
{
    /**
    * Возвращает тип комментария в читаемом виде, например: "комментарий к товарам"
    * 
    * @return string
    */    
    function getTitle();
    
    /**
    * Возвращает id объекта на текущей странице, к которому необходимо привязать комментарии
    * 
    * @return mixed
    */
    function getLinkId();
    
    /**
    * Возвращает объект, к которому привязан комментарий
    * 
    * @return object
    */
    function getLinkedObject();
    
    /**
    * Возвращает ссылку на объект в клиентской части, к которому привязан комментарий
    * 
    * @return object
    */
    function getLinkedObjectUrl($absolute = false);
    
    /**
    * Возвращает название товара
    * 
    * @return string
    */
    function getLinkedObjectTitle();
    
    
    /**
    * Возвращает путь к редактированию элемента в административной панели
    * 
    * @return string | bool(false)
    */
    function getAdminUrl();
}

