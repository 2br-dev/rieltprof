<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class UserTemplate extends AbstractType
{
    protected
        $php_type = '',
        $me_visible = false,
        $runtime = true;

    /**
     * UserTemplate constructor.
     * @param string $template - путь к шаблону
     * @param string $meTemplate - путь к шаблону при мультиредактировании
     * @param null|array $options - массив дополнительных параметров
     */
    function __construct($template, $meTemplate = null, $options = null)
    {
        $this->setTemplate($template);
        $this->setMeTemplate($meTemplate);
        parent::__construct($options);
    }

    /**
     * Возвращает форму в отредеренном шаблоне
     *
     * @param null|array $view_options - массив дополнительных аттрибутов в форме
     * @param null|object $orm_object - объект, которому принадлежит поле
     * @return string
     */
    function formView($view_options = null, $orm_object = null)
    {
        $sm = new \RS\View\Engine();
        $sm -> assign([
            'field' => $this,
            'view_options' => $view_options !== null ? array_combine($view_options, $view_options) : null
        ]);
        
        return $sm -> fetch($this->getRenderTemplate());
    }
}