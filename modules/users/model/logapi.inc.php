<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model;

/**
* Api по работе с логом пользователя
*/
class LogApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new \Users\Model\Orm\Log);
    }

    /**
     * Записывает в лог текущего пользователя запись
     *
     * @param string | object $class - Класс события наследник LogTypeAbstract
     * @param int $object_id - ID объекта события
     * @param mixed $data - данные
     * @param int $group - id группы. Если событие с таким id группы уже есть, оно будет удалено, а новое событие создано.
     *
     * @return bool
     */
    public static function appendUserLog($class, $object_id, $data = null, $group = null)
    {
        if (!is_object($class)) $class = new $class();
        if (!($class instanceof LogtypeAbstract)) {
            throw new Exception\UsersLog(t("$class должен быть наследником Users\\Model\\LogtypeAbstract"));
        }
        
        $log_item = new Orm\Log();
        $log_item['class'] = get_class($class);
        $log_item['oid'] = $object_id;
        $log_item['user_id'] = (int)\RS\Application\Auth::getCurrentUser()->offsetGet('id');
        $log_item['dateof'] = date('Y-m-d H:i:s');
        $log_item['data'] = $data;
        
        if ($group !== null) $log_item['group'] = $group;
        
        return $log_item->replace();
    }
    
    /**
    * Возвращает список последних событий заданного класса для текущего пользователя
    * 
    * @param mixed $class - класс логов
    * @param int $limit - количество элементов в списке
    * @param int $offset - начальный элемент в списке
    * @param mixed $user_id - фильтр по id пользователя. Если Null, то текущий пользователь
    * @param mixed $site_id - фильтр по id сайта
    * @return LogtypeAbstract[]
    */
    function getLogItems($class, $limit = null, $offset = 0, $user_id = false, $site_id = false)
    {
        $this->clearFilter();
        $this->setFilter('class', $class);
        if ($user_id !== false) {
            if ($user_id === null) $user_id = \RS\Application\Auth::getCurrentUser()->offsetGet('id');
            $this->setFilter('user_id', $user_id);
        }
        if ($site_id !== false) {
            $this->setFilter('site_id', $site_id);
        }        
        $this->setOrder('dateof DESC');
        
        if ($limit) {
            $this->queryObj()->offset($offset)->limit($limit);
        }
        return $this->wrapResult($this->getListAsResource());
    }

    /**
     * Возвращает количество элементов в логе по параметрам
     *
     * @param mixed $class - класс логов
     * @param mixed $user_id - фильтр по id пользователя. Если Null, то текущий пользователь
     * @param mixed $site_id - фильтр по id сайта
     * @return int
     */
    function getLogItemsCount($class, $user_id = false, $site_id = false)
    {
        $this->clearFilter();
        $this->setFilter('class', $class);
        if ($user_id !== false) {
            if ($user_id === null) $user_id = \RS\Application\Auth::getCurrentUser()->id;
            $this->setFilter('user_id', $user_id);
        }
        
        if ($site_id !== false) {
            $this->setFilter('site_id', $site_id);
        }
        return $this->getListCount();        
    }
    
    /**
    * Возвращает массив с экземплярами классов событий
    * 
    * @param mixed $resource
    * @return LogtypeAbstract[]
    */
    protected function wrapResult($resource)
    {
        $result = [];
        while($row = $resource->fetchRow()) {
            $obj = new $row['class']();
            $obj->load($row);
            $result[] = $obj;
        }
        return $result;
    }
    
}

