<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Date extends AbstractType
{
    protected
        $php_type = 'string',
        $sql_notation = "date",
        $has_len = false,
        $max_len = 30,
        
        $form_template = '%system%/coreobject/type/form/date.tpl';

    /**
     * Вызывается у каждого свойства перед сохранением ORM объекта.
     *
     * @return void
     */
    public function selfSave()
    {
        if ($this->isAllowEmpty() && $this->value === '') {
            $this->value = null;
        }
    }
}  
