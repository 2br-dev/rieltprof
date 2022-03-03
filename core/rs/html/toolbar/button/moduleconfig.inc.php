<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

class ModuleConfig extends Button
{
    protected
        $template = 'system/admin/html_elements/toolbar/button/moduleconfig.tpl';
        
    function __construct($href, $title = null, $property = null)
    {
        $title = t('Настройка модуля');
        $this->property = [
            'attr' => [
                'title' => $title,
                'class' => 'btn-default mod-config'
            ]
        ];
        parent::__construct($href, $title, $property);
    }
}