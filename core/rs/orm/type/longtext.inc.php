<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class LongText extends AbstractType
{
    protected
        $php_type = 'string',
        $sql_notation = 'longtext',
        $has_len = false,
        $form_template = '%system%/coreobject/type/form/textarea.tpl',
        $view_attr = ['cols' => '50', 'rows' => '10'];

}  


