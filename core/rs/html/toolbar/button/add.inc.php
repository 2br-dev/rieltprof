<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

class Add extends Button
{
    protected
        $class_ajax = 'crud-add',
        $property = [
            'attr' => [
                'class' => 'btn-success'
            ]
    ];
        
    function __construct($href, $title = null, $property = null)
    {
        if ($title === null) {
            $title = t('добавить');
        }
        parent::__construct($href, $title, $property);
    }        
}

