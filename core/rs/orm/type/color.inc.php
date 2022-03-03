<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Type;

/**
 * Тип - цвет Напимер AA66FF. отображается с иконкой выбора цвета.
 */
class Color extends Varchar
{
    public $max_len = 7;

    protected $form_template = '%system%/coreobject/type/form/color.tpl';

    function __construct(array $options = null)
    {
        parent::__construct($options);
        if (empty($this->view_attr['class'])) {
            $this->view_attr['class'] = 'colorpicker';
        } else {
            $this->view_attr['class'] .= ' colorpicker';
        }
    }
}
