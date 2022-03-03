<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\Types;

/**
* Объект с обязательными параметрами для уведомления
*/
class NoticeDataDesktopApp
{
    public    
        /**
        * Заголовок уведомления
        * 
        * @var string
        */
        $title,
        
        /**
        * Краткий текст уведомления без тегов
        * 
        * @var string
        */
        $short_message,
        
        /**
        * Абсолютная ссылка для перехода на сайт
        * 
        * @var string
        */
        $link,
        
        /**
        * Подпись к ссылке
        * 
        * @var string
        */
        $link_title,
        
        /**
        * Переменные, которые будут переданы в шаблон уведомления
        * 
        * @var array
        */
        $vars,
        
        /**
        * ID пользователя, для которого предназначено сообщение.
        * Если не заполнено или 0, то предназначается всем пользователям.
        * 
        * @var integer
        */
        $destination_user_id;
}
