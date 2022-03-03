<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Helper;

/**
 * Класс объектов, сохраняющих свое состояние
 *
 * Class PersistentState
 * @package RS\Helper
 */
abstract class PersistentState
{
    protected $prefix = "";

    public function __construct($prefix = "")
    {
        $this->prefix = $prefix;
    }

    public function __get($name)
    {
        return $this->get($this->prefix.$name);
    }

    public function __set($name, $value)
    {
        $this->set($this->prefix.$name, $value);
    }

    abstract function get($name);
    abstract function set($name, $value);

}