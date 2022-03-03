<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type\Action;

class Edit extends AbstractAction
{
    protected
        $class_ajax = 'crud-edit',
        $class_action = 'edit',
        $class_icon = 'edit';
    
    function __construct($href_pattern, $title = null, $property = null)
    {
        if ($title === null) {
            $title = t('редактировать');
        }
        parent::__construct($href_pattern, $title, $property);
    }
}

