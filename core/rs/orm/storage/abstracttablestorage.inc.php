<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Storage;

/**
* Класс предназначен для типов хранилищь, которые связаны с базой данных
*/
class AbstractTableStorage extends AbstractStorage
{
    protected
        $table;

    /**
    * Инициализирует хранилище
    * 
    * @return void
    */    
    function _init()
    {
        $this->table = $this->orm_object->_getTable();
    }    
    
    /**
    * Возвращает условие для выборки по первичному ключу
    * 
    * @param array | string $primaryKeyValue - первичный ключ. Если первичный ключ простой, то ожидается скалярный тип, 
    * если составной, то массив [поле1 => 'значение', поле2 => 'значение']
    * @return array массив с парами ключ => значение
    */
    protected function getPrimaryKeyExpr($primaryKeyValue = null)
    {
        $where = [];
        $pk_field = $this->orm_object->getPrimaryKeyProperty();
                
        if ($primaryKeyValue !== null && is_scalar($pk_field) != is_scalar($primaryKeyValue)) {
            throw new \RS\Orm\Storage\Exception(t('Неверный тип первичного ключа, ожидается %0, передан %1', [gettype($pk_field),  gettype($primaryKeyValue)]));
        }
        
        foreach((array)$pk_field as $key) {
            if ($primaryKeyValue !== null) {
                $pk = is_array($primaryKeyValue) ? $primaryKeyValue[$key] : $primaryKeyValue;
            } else {
                $pk = $this->orm_object[$key];
            }
            $where[$key] = $pk;
        }
        return $where;
    }       
}