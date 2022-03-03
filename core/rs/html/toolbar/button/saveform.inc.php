<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

use RS\Exception;

class SaveForm extends Button
{
    protected
        $class_ajax = 'crud-form-save',
        $split_button = false,
        $template = 'system/admin/html_elements/toolbar/button/split_button.tpl',

        $property = [
            'attr' => [
                'class' => 'btn-success'
            ],
    ];
        
    function __construct($href = null, $title = null, $property = null, $split_button = false)
    {
        $this->setSplitButton($split_button);

        if ($title === null) {
            $title = t('сохранить и закрыть');
        }
        parent::__construct($href, $title, $property);
    }

    /**
     * Устанавливает смежную кнопку.
     * Если true, то будет добавлена стандартная кнопка Apply
     *
     * @return bool(false) | AbstractButton
     */
    function setSplitButton($button)
    {
        if ($button === true) {
            $button = new ApplyForm();
        }

        if ($button && !($button instanceof AbstractButton)) {
            throw new Exception(t('Смежная кнопка должна быть потомком класса AbstractButton'));
        }

        $this->split_button = $button;
    }

    /**
     * Возвращает смежную кнопку или false
     *
     * @return bool(false) | AbstractButton
     */
    function getSplitButton()
    {
        return $this->split_button;
    }
}
