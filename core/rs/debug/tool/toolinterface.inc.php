<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Debug\Tool;

/**
* Интерфейс описывает один объект инструмента в режиме отладки
*/
interface ToolInterface
{
    /**
    * Должен возвращать HTML кнопки в группе отладки
    */
    function getView();
}

