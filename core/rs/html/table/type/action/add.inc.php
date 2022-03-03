<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type\Action;

class Add extends AbstractAction
{
    protected
        $class_ajax = 'crud-add',
        $class_action = 'add';
            
    function __construct($href_pattern, $title = null, $property = null)
    {
        if ($title === null) {
            $title = t('добавить');
        }
        parent::__construct($href_pattern, $title, $property);
    }
}