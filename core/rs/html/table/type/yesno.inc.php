<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;

class Yesno extends AbstractType
{
    public 
        $property = [
            'attr' => [
                'class' => 'toggle-switch rs-switch'
            ]
    ];
        
    protected        
        $body_template = 'system/admin/html_elements/table/coltype/yesno.tpl';
    
    /**
    * Устанавливает url, который будет вызываться при нажатии на переключатель
    * 
    * @param string $url адрес к действию контроллера
    */
    function setToggleUrl($url)
    {
        @$this->property['attr']['class'] .= ' crud-switch';
        $this->property['attr']['@data-url'] = $url;
    }
}