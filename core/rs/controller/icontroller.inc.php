<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller;

/**
* Интерфейс контроллера
*/
interface IController 
{
    public function presetAct($act);
    public function exec();
}

