<?php
namespace CodeGen\Model;


use RS\Module\AbstractModel\BaseModel;

class ModuleGenerator extends BaseModel{

    private $module_example;

    public function __construct()
    {
        $this->module_example = new \ZipArchive();
        $this->module_example->open(__DIR__.'/module_example.zip');
    }

    public function deployModule(ModuleParams $params)
    {
        for( $i = 0; $i < $this->module_example->numFiles; $i++ ){

            $stat = $this->module_example->statIndex( $i );
            $is_dir = !$stat['size'];

            $dest_path = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.strtolower($params->name).'/'.$stat['name'];

            if($is_dir && !is_dir($dest_path))
            {
                mkdir($dest_path, \Setup::$CREATE_DIR_RIGHTS, true);
            }

            if(!$is_dir)
            {
                $content = $this->module_example->getFromIndex( $i );
                $replaced = $this->replaceVariables($content, $params);
                file_put_contents($dest_path, $replaced);
            }

        }

        return true;
    }

    private function replaceVariables($text, ModuleParams $params)
    {
        $replace_pairs = [];

        foreach($params as $key=>$value)
        {
            $replace_pairs['@'.strtoupper($key).'@'] = $value;
            $replace_pairs['@'.$key.'@'] = strtolower($value);
        }
        return strtr($text, $replace_pairs);
    }

}

