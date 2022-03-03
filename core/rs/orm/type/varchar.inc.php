<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Varchar extends AbstractType
{
    protected
        $php_type = 'string',
        $sql_notation = 'varchar',
        $auto_increment = false,
        $max_len = 255;
        
    function __construct(array $options = null)
    {        
        $k = 0.25;
        $maxLength = isset($options['maxLength']) ? $options['maxLength'] : $this->max_len;
        $size = (ceil($k*$maxLength)< 10) ? 10 : ceil($k*$maxLength);
        if ($size>85) $size = 85;
        
        $this->setAttr(['size' => $size, 'type' => 'text']);
        parent::__construct($options);        
    }
    
    /**
    * Устанавливает вид формы в виде textarea
    * @return String
    */
    function setViewAsTextarea()
    {
        $this->form_template = '%system%/coreobject/type/form/textarea.tpl';
        $this->view_attr += [
            'rows' => 3,
            'cols' => 80
        ];
        return $this;
    }
    
    /**
    * Устанавливает ассоциативный массив, элементы которого должны использоваться для отображения формы в виде элемента SELECT
    * 
    * @param array $list
    * @return static
    */
    public function setListFromArray(array $list)
    {
        //Убираем атрибут type, если выбран вид в виде select'а
        unset($this->view_attr['type']);
        return parent::setListFromArray($list);
    }

    /**
     * Возвращает true, так как поле кодируется в базе данных
     *
     * @return bool
     */
    public function isHtmlEncodedField()
    {
        return true;
    }
}  


