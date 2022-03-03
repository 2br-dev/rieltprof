<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Helper;


use RS\HashStore\Api;

class PersistentStateHashStore extends PersistentState
{

    public function __construct($prefix = "")
    {
        parent::__construct($prefix);
    }


    function get($name)
    {
        return Api::get($name);
    }

    function set($name, $value)
    {
        Api::set($name, $value);
    }

}