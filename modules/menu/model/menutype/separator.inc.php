<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Model\MenuType;
use \RS\Orm\FormObject,
    \RS\Orm\Type,
    \RS\Orm\PropertyIterator;
    

class Separator extends AbstractType
{
    /**
    * Возвращает уникальный идентификатор для данного типа
    * 
    * @return string
    */
    public function getId()
    {
        return 'separator';
    }
    
    /**
    * Возвращает название данного типа
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Разделитель');
    }
    
    /**
    * Возвращает описание данного типа 
    * 
    * @return string
    */
    public function getDescription()
    {
        return '';
    }
    
    /**
    * Возвращает маршрут, если пункт меню должен добавлять его, 
    * в противном случае - false
    * 
    * @return \RS\Router\Route | false
    */
    public function getRoute()
    {
        return false;
    }
    
    /**
    * Возвращает поля, которые должны быть отображены при выборе данного типа
    * 
    * @return \RS\Orm\FormObject
    */
    public function getFormObject()
    {}
    
    /**
    * Возвращает ссылку, на которую должен вести данный пункт меню
    * 
    * @return string
    */
    public function getHref($absolute = false)
    {
        return '';
    }
    
    /**
    * Возвращает true, если пункт меню активен в настоящее время
    * 
    * @return bool
    */
    public function isActive()
    {
        return false;
    }

    /**
    * Возвращает True, если тип должен быть видимым в окне редактирования пунктов меню
    * 
    * @return bool
    */
    public function isVisible()
    {
        return false;
    }
}