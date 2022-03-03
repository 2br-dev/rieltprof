<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace SiteUpdate\Config;
use \RS\Orm\Type;

/**
* Конфигурационный файл модуля
*/
class File extends \RS\Orm\ConfigObject
{

    function _init()
    {
        parent::_init()->append([
            'file_download_part_size_mb' => new Type\Integer([
                'description' => t('Размер одной загружаемой части с сервера обновления')
            ]),
        ]);
    }    
    
    /**
    * Возвращает значения свойств по-умолчанию
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        $result = [];
        $this_module = \RS\Module\Item::nameByObject( get_called_class() );
        $file = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$this_module.\Setup::$CONFIG_FOLDER.'/module.xml';
        
        $config = @new \SimpleXMLElement($file, null, true);
        foreach($config->defaultValues[0] as $key => $value) {
            $result_value = (string)$value;
            if ($value['multilanguage']) {
                $result_value = t($result_value);
            }
            $result[$key] = $result_value;
        }
        return $result;
    }    
}

