<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

class ApplyForm extends Save
{
    protected
        $class_ajax = 'crud-form-apply',
        $property = [
            'attr' => [
                'class' => 'l-border btn-success'
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
