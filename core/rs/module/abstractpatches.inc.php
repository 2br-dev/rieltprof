<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Module;

/**
* Абстрактный класс, реализует необходимые методы для установки патчей
*/
abstract class AbstractPatches
{
    protected
        $error_patches;
    /**
    * Возвращает массив имен патчей.
    * В классе должны быть пределены методы:
    * beforeUpdate<ИМЯ_ПАТЧА> или
    * afterUPDATE<ИМЯ_ПАТЧА>
    * 
    * @return array
    */
    abstract function init();
    
    /**
    * Выполняет все патчи, которые должны выполняться перед обновлением модуля
    */
    function runBeforeUpdatePatches()
    {
        return $this->runPatches('beforeUpdate');
    }

    /**
    * Выполняет все патчи, которые должны выполняться после обновления модуля
    */    
    function runAfterUpdatePatches()
    {
        return $this->runPatches('afterUpdate');
    }
    
    /**
    * Выполняет все патчи с заданным префиксом.
    * Патчи выполняются только один раз
    * 
    * @param string $prefix
    */
    private function runPatches($prefix)
    {        
        $this->error_patches = [];
        
        $module = strtoupper(strtok(get_class($this), '\\'));
        foreach($this->init() as $name) {
            $method = $prefix.$name;
            if (method_exists($this, $method)) {
                $patch_id = "MODULE_{$module}_PATCH_{$method}";
                if (!\RS\HashStore\Api::get($patch_id)) {
                    try {
                        if (\Setup::$INSTALLED) {
                            $call_result = call_user_func([$this, $method]);
                            
                            if ($call_result === false) {
                                $this->error_patches[] = $method;
                            }
                        }
                        //При первой установке движка, пометим патч выполненным,
                        //так как все изменения уже включены в дистрибутив
                        \RS\HashStore\Api::set($patch_id, true);
                        
                    } catch (\RS\Exception $e) { 
                        $this->error_patches[] = $method;
                    }
                }
            }
        }
        return empty($this->error_patches);
    }
    
    /**
    * Возвращает названия методов, в которых произошли ошибки
    */
    function getErrorPatches()
    {
        return $this->error_patches;
    }
    
}
