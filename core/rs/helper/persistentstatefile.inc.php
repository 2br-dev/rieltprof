<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Helper;


class PersistentStateFile extends PersistentState
{

    private $file = null;
    private $data = [];

    public function __construct($file, $prefix = "")
    {
        parent::__construct($prefix);
        $this->file = $file;
        if(file_exists($file))
        {
            $this->data = @unserialize(file_get_contents($file));
        }
        else
        {
            \RS\File\Tools::makePath($file, true);
            $this->save();
        }
    }


    function get($name)
    {
        return $this->data[$name];
    }

    function set($name, $value)
    {
        $this->data[$name] = $value;
        $this->save();
    }

    function clean()
    {
        $this->data = [];
        $this->save();
    }

    private function save()
    {
        assert(file_exists($this->file));
        file_put_contents($this->file, @serialize($this->data));
    }
}