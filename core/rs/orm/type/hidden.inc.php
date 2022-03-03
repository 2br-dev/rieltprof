<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Type;

/**
* Тип - любое значение. только run-time тип.
*/
class Hidden extends MixedType
{
    protected
        $vis_form = true,
        $form_template = '%system%/coreobject/type/form/hidden.tpl';
    
    function isHidden()
    {
        return true;
    }
        
    
}
