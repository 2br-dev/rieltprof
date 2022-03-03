<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Search\Model;

/**
* Api - для работы с индексной таблицей. Позволяет добавлять и исключать объекты из поиска
*/
class IndexApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Index);
    }
    
    public static $i = 0;
    
    /**
    * Добавить к поиску или обновить запись для поиска
    * 
    * @param mixed $result_class - класс
    * @param mixed $entity_id
    * @param mixed $title
    * @param mixed $text
    */
    public static function updateSearch($result_class, $entity_id, $title, $text, $dateof = null)
    {
        $search_item = new Orm\Index();
        $search_item['result_class'] = is_object($result_class) ? get_class($result_class) : $result_class;
        $search_item['entity_id'] = $entity_id;
        $search_item['title'] = $title;
        $search_item['indextext'] = strip_tags($text);
        $search_item['dateof'] = ($dateof === null) ? date('c') : $dateof;
         
        SearchApi::currentEngine()->onUpdateSearch($search_item);
        
        $r = $search_item->replace();
    }
    
    /**
    * Удалить из поиска 
    * 
    * @param mixed $result_class - класс
    * @param mixed $entity_id
    * @return CDb_Result
    */
    public static function removeFromSearch($result_class, $entity_id)
    {
        $search_item = new Orm\Index();
        $search_item['result_class'] = is_object($result_class) ? get_class($result_class) : $result_class;
        $search_item['entity_id'] = $entity_id;
        $search_item->delete();
    }
    
}


