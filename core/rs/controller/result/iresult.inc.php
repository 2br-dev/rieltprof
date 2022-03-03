<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller\Result;

/**
* Интерфейс результата действия контроллера
*/
interface IResult
{
    /**
    * Должен возвращать данные для передачи в браузер
    * 
    * @return string
    */
    public function getOutput();
    
    /**
    * Должен возвращать HTML, подготовленый для вывода
    */
    public function getHtml();
    
    /**
    * Должен устанавливать данные, которые будет отправены в output
    * 
    * @param string $html - 
    */
    public function setHtml($html);
}


