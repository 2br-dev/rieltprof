<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Storage;

/**
* Хранит объект в файле в формате массива. (один объект в одном файле)
* Данный тип хранилища предназначен для объектов - типа "конфигурация".
* В формате массива для того, чтобы параметры можно было менять быстро, вручную, 
* но и должен быть доступ к этому конфигу и из админки.
* 
* Данный тип хранилища не предусматривает, что у объекта будет какой-либо id для загрузки.(load)
* Каждый объект должен сохраняться в отдельный файл.
*/
class Arrayfile extends AbstractStorage
{    
    public 
        $head_comment,//Текстовый заголовок файла
        $store_file;  //Полный путь к файлу, в котором будут храниться данные объекта
    
    function _init()
    {
        $this->store_file = $this->getOption('store_file','');
        $this->head_comment = $this->getOption('head_comment', '//Данный файл сгенерирован автоматически.');
    }
        
    /**
    * Загружает объект по первичному ключу
    * 
    * @param mixed $primaryKey - значение первичного ключа. Не используется.
    * @return object
    */    
    public function load($primaryKey = null)
    {
        if ( file_exists($this->store_file) ) {            
            $data = include($this->store_file);
            $this->orm_object->getFromArray($data, null, false, true);
            return true;
        }
        return false;
    }
    
    /**
    * Добавляет объект в хранилище
    * 
    * @return bool
    */    
    public function insert()
    {
        $properties = $this->orm_object->getProperties();
        
        $tmp = [];
        foreach ($properties as $key=>$property) {
            if ($property->beforesave()) {
                $this->orm_object[$key] = $property->get();
            }            
            if (!$property->isUseToSave()) continue;
            if (!$property->isRuntime()) $tmp[$key] = $property->get();
        }
        $write_data = "return ".var_export($tmp, true).";";
        
        return $this->saveToFile($write_data);
    }
    
    /**
    * Перезаписывает объект в хранилище
    * 
    * @return bool
    */
    public function replace()
    {
        return $this->insert();
    }
    
    /**
    * Обновляет объект в хранилище
    * 
    * @param $primaryKey - значение первичного ключа
    * @return bool
    */
    public function update($primaryKey = null)
    {
        return $this->insert();
    }
    
    /**
    * Удаляет объект из хранилища
    * 
    * @return bool
    */
    public function delete()
    {
        if (file_exists($this->store_file)) {
            unlink($this->store_file);
        }
    }
    
    /**
    * Сохраняет данные в файл
    * 
    * @param string $write_data - PHP код для сохранения
    * @return integer 
    */
    protected function saveToFile($write_data)
    {
        $write_data = "<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/\n".$this->head_comment."\n".$write_data."\n";
        \RS\File\Tools::makePath($this->store_file, true);
        return file_put_contents($this->store_file, $write_data);
    }
}

