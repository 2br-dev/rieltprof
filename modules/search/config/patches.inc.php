<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Search\Config;
use \RS\Db\Adapter as DbAdapter;

/**
* Патчи к модулю
*/
class Patches extends \RS\Module\AbstractPatches
{
    /**
    * Возвращает массив имен патчей.
    */
    function init()
    {
        return [
            '2008',
            '2002'
        ];
    }
    
    /**
    * Удаляет автоинкрементное поле ID, в связи с тем, 
    * что теперь первичный ключ составной: result_class, entity_id
    */
    function beforeUpdate2008()
    {
        $index = new \Search\Model\Orm\Index();
        try {
            DbAdapter::sqlExec('ALTER IGNORE TABLE '.$index->_getTable().' DROP COLUMN id');
            DbAdapter::sqlExec('ALTER IGNORE TABLE '.$index->_getTable().' DROP INDEX result_class-entity_id');
        } catch (\RS\Db\Exception $e) {}
    }
    
    /**
    * Патч для релиза 2.0.0.2
    * Удаляет дубликаты из таблицы с поисковыми индексами, чтобы установить уникальный индекс
    */
    function beforeUpdate2002()
    {
        $search_index_orm = new \Search\Model\Orm\Index();
        $total = \RS\Orm\Request::make()
            ->from($search_index_orm)
            ->count();
            
        $distinct = \RS\Orm\Request::make()
            ->select('COUNT(DISTINCT result_class,entity_id) as cnt')
            ->from($search_index_orm)
            ->exec()->getOneField('cnt', 0);
        
        if ($total != $distinct) {
            //Удаляем дубликаты
            $sqls = [
                "CREATE TEMPORARY TABLE search_index_tmp AS SELECT * FROM (SELECT * FROM ".$search_index_orm->_getTable()." ORDER BY dateof DESC) as sorted_table GROUP BY result_class, entity_id",
                "DELETE FROM ".$search_index_orm->_getTable(),
                "INSERT INTO ".$search_index_orm->_getTable()." SELECT * FROM search_index_tmp",
                "DROP TABLE search_index_tmp"
            ];
            foreach($sqls as $sql) {
                \RS\Db\Adapter::sqlExec($sql);
            }
        }
    }
}
