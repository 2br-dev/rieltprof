<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

class Save extends Button
{
    protected
        $class_ajax = 'crud-list-save',
        $property = [
            'attr' => [
                'class' => 'btn-success'
            ]
    ];
        
    function __construct($href = null, $title = null, $property = null)
    {
        if ($title === null) {
            $title = t('сохранить');
        }
        parent::__construct($href, $title, $property);
    }           
}
