<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

class Cancel extends Button
{    
    protected
        $class_ajax = 'crud-form-cancel';
            
    function __construct($href, $title = null, $property = null)
    {
        if ($title === null) {
            $title = t('отмена');
        }
        parent::__construct($href, $title, $property);
    }
}

