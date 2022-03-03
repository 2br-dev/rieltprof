<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\AccessControl;

/**
* Объект права
*/
class Right
{
    protected $alias;
    protected $title;
    protected $parents;

    /**
     * Конструктор
     *
     * @param string $alias - идентификатор права
     * @param string $title - наименование права
     */
    public function __construct($alias, $title)
    {
        $this->alias = $alias;
        $this->title = $title;
    }
    
    /**
    * Возвращает идентификатор права
    * 
    * @return string
    */
    public function getAlias()
    {
        return $this->alias;
    }
    
    /**
    * Возвращает наименование права
    * 
    * @return string
    */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
    * Сообщает является ли объект группой
    * 
    * @return bool
    */
    public function isGroup()
    {
        return false;
    }

    /**
     * Устанавливает список родительских групп для права
     *
     * @param RightGroup[] $parents - список родительских групп
     * @return void
     */
    public function setParents($parents)
    {
        $this->parents = $parents;
    }

    /**
     * Возвращает наименование права вместе с родительскими группами
     *
     * @return string
     */
    public function getTitleWithPath()
    {
        $parts = [];
        foreach ($this->parents as $group) {
            $parts[] = $group->getTitle();
        }
        $parts[] = $this->getTitle();

        return implode(' - ', $parts);
    }
}
