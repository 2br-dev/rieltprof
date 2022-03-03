<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\HashStore;
use \RS\Orm\Type;

/**
* ORM - объект хранилища ключ -> значение
*/
class Api extends \RS\Orm\AbstractObject
{
    protected static $table = 'hash_store';
        
    function _init()
    {
        self::$db = \Setup::$DB_NAME;
        $this->getPropertyIterator()->append([
            'hash' => new Type\Bigint([
                'description' => t('Хэш ключа'),
                'maxLength' => 12,
                'unsigned' => true,
                'primaryKey' => true
            ]),
            'value' => new Type\Varchar([
                'description' => t('Значение для ключа'),
                'maxLength' => 4000
            ])
        ]);
    }
    
    function getPrimaryKeyProperty()
    {
        return 'hash';
    }
    
    /**
    * Сохраняет значение $value для ключа $key
    * 
    * @param mixed $key - ключ
    * @param string $value - значение
    * @return void;
    */
    public static function set($key, $value) 
    {
        $store = new self();
        $store['hash'] = self::makeHash($key);
        $store['value'] = serialize($value);
        $store->replace();
    }
    
    /**
    * озвращает значение по ключу
    * 
    * @param string $key - ключ
    * @param mixed $default - значение по-умолчанию, возвращается в случае, если ключ не найден
    * @return string
    */
    public static function get($key, $default = null)
    {
        $store = new self();
        if ($store->load(self::makeHash($key))) {
            return @unserialize($store['value']);
        }
        return $default;
    }
    
    /**
    * Возвращает хэш по ключу
    * 
    * @param string $key - ключ
    * @return string
    */
    public static function makeHash($key)
    {
        return sprintf('%u', crc32($key));
    }    
}
