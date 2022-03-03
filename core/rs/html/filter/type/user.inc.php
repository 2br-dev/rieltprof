<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter\Type;

use RS\Router\Manager;

/**
* Фильтр по пользователю. Отображается в виде поля c autocomplete.
*/
class User extends AbstractType
{
    public
        $tpl = 'system/admin/html_elements/filter/type/user.tpl';    
    
    protected
        $request_url;
    
    function __construct($key, $title, $options = [])
    {
        $this->attr = [
            'class' => 'w150'
        ];
        parent::__construct($key, $title, $options);
        @$this->attr['class'] .= ' object-select';
    }
        
    /**
    * Возвращает текстовое значение фильтра
    * 
    * @return string
    */
    function getTextValue()
    {
        $user = new \Users\Model\Orm\User($this->getValue());
        return $user->getFio();
    }
    
    /**
    * Возвращает URL для поиска пользователя
    * 
    * @return string
    */
    function getRequestUrl()
    {
        return $this->request_url ?: Manager::obj()->getAdminUrl('ajaxEmail', null, 'users-ajaxlist');
    }
    
    /**
    * Устанавливает URL для поиска пользователя
    * 
    * @param string $url
    * @return User
    */
    function setRequestUrl($url)
    {
        $this->request_url = $url;
        return $this;
    }
}