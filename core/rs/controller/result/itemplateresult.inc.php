<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller\Result;

/**
* Интерфейс результата действия контроллера. 
* Может возвращать какие переменные переданы в шаблон и какой шаблон использован для рендеринга страницы
*/
interface ITemplateResult extends IResult
{
    /**
    * Должен устанавливать шаблон, который буден использован для рендеринга страницы
    */
    public function setTemplate($template);
    
    /**
    * Должен возвращать какой шаблон будет использован для рендеринга страницы
    * 
    * @return string
    */
    public function getTemplate();
    
    /**
    * Должен возвращать переменные, переданные в шаблон
    */
    public function getTemplateVars();
    
}


