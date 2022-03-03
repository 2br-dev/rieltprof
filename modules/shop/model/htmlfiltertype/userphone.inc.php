<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\HtmlFilterType;

/**
* Класс поиска по телефону пользователя
*/
class UserPhone extends \RS\Html\Filter\Type\AbstractType
{
    public 
        $tpl = 'system/admin/html_elements/filter/type/string.tpl';
        
    protected
        $search_type = 'eq';
        
        
    /**
    * Модифицирует запрос
    * 
    * @param \RS\Orm\Request $q
    * @return \RS\Orm\Request
    */
    function modificateQuery(\RS\Orm\Request $q)
    {
        //Если указано значение и таблица ещё не присоединена
        if (!empty($this->value) && !$q->issetTable(new \Users\Model\Orm\User())){
            $q->select = 'A.*';
            $q->leftjoin(new \Users\Model\Orm\User(), 'A.user_id=USER.id', 'USER');
        }
        return $q;
    }
    
    /**
    * Сравнивает строку используя инструкцию LIKE 'NEEDLE%'
    * Подменяется для добавления поиска по полю телефона
    * 
    */
    protected function where_like($likepattern)
    {
        $value = str_replace('like', $this->escape($this->getValue()), $likepattern);
        return "({$this->getSqlKey()} like '{$value}' 
        OR (A.user_phone like '{$value}' AND ({$this->getSqlKey()} IS NULL OR {$this->getSqlKey()} = '')))";
    }

}

