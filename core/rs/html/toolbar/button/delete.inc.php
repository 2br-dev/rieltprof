<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

class Delete extends Button
{
    protected
        $class_ajax = 'crud-remove',
        $property = [
            'attr' => [
                'class' => 'btn-alt btn-danger delete'
            ]
    ];

    function __construct($href, $title = null, $property = null)
    {
        if ($title === null) {
            $title = t('удалить');
        }
        parent::__construct($href, $title, $property);
    }        
}

