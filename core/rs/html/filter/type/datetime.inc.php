<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter\Type;

class Datetime extends AbstractType
{
    public 
        $tpl = 'system/admin/html_elements/filter/type/datetime.tpl';
        
    protected
        $search_type = 'eq';
}
