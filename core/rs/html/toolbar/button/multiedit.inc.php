<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

class Multiedit extends Button
{
    protected
        $class_ajax = 'crud-multiedit',
        $property = [
            'attr' => [
                'class' => 'btn-alt btn-primary'
            ]
    ];
        
    function __construct($href, $title = null, $property = null)
    {
        if ($title === null) {
            $title = t('редактировать');
        }
        parent::__construct($href, $title, $property);
    }        
}

