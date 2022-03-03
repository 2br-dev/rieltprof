<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter\Type;

class Text extends AbstractType
{
    public 
        $tpl = 'system/admin/html_elements/filter/type/string.tpl';
        
    protected
        $search_type = 'eq';

}

