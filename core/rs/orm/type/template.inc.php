<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

/**
* Тип данных - шаблон из списка
*/
class Template extends Varchar
{
    protected
        $only_themes = true,
        $form_template = '%system%/coreobject/type/form/template.tpl';
    

    /**
    * Устанавливает, отображать ли только папки тем оформления или отображать также папки модулей
    * 
    * @param bool $bool
    */
    function setOnlyThemes($bool)
    {
        $this->only_themes = $bool;
        return $this;
    }
    
    /**
    * Возвращает true, если необходимо дать выбот только папок с темами оформления, 
    * false - если нужно отобразить и папки модуле также
    * 
    * @return bool
    */
    function getOnlyThemes()
    {
        return $this->only_themes;
    }
}