<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Debug\Action;

/**
* Действие "редактировать" в контекстном меню
*/
class Edit extends AbstractAction
{
    protected
        $attr = [
            'class' => 'debug-action-edit crud-edit'
    ];
    
    /**
    * Конструктор объектов действия контекстного меню в режиме отладки
    * 
    * @param string $href - ссылка действия
    * @param string $title - название действия, по-умолчанию "редактировать"
    * @param array $attr - ассоциативный массив атрибутов HTML элемента контекстного меню
    * @return AbstractAction
    */    
    function __construct($href, $title = null, $attr = [])
    {
        if ($title === null) {
            $title = t('редактировать');
        }
        parent::__construct($href, $title, $attr = []);
    }
    
}

