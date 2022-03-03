<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Tags\Config;

/**
* Патчи к модулю
*/
class Patches extends \RS\Module\AbstractPatches
{
    function init()
    {
        return [
            '20004'
        ];
    }

    /**
    * Патч прописывает alias ко всем тегам словам у которых нет этого
    * 
    */    
    function afterUpdate20004()
    {
        //Получим теги ранне созданные без alias
        $tags = \RS\Orm\Request::make()
                ->from(new \Tags\Model\Orm\Word())
                ->where('alias IS NULL')
                ->objects();
        if (!empty($tags)){
            foreach($tags as $tag){
               $tag->update(); //При обновлении появится alias 
            }
        }        
    }
}
