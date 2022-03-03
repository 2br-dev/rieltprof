<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\AccessControl;

/**
* Группа прав
*/
class RightGroup
{
    protected $alias;
    protected $title;
    protected $childs;

    /**
     * Конструктор
     *
     * @param string $alias - идентификатор группы прав
     * @param string $title - наименование группы прав
     * @param (Right|RightGroup)[] $childs - потомки группы
     */
    public function __construct($alias, $title, $childs = [])
    {
        $this->alias = $alias;
        $this->title = $title;
        $this->addChilds($childs);
    }
    
    /**
    * Возвращает идентификатор группы
    * 
    * @return string
    */
    public function getAlias()
    {
        return $this->alias;
    }
    
    /**
    * Возвращает наименование группы
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
        return true;
    }
    
    /**
    * Добавляет группе потомков
    * 
    * @param (Right|RightGroup)[] $childs
    * @return void
    */
    public function addChilds($childs)
    {
        if (!is_array($childs)) {
            $childs = [$childs];
        }
        foreach ($childs as $child) {
            if (!isset($this->childs[$child->getAlias()])) {
                $this->childs[$child->getAlias()] = $child;
            }
        }
    }

    /**
     * Возвращает потомков
     *
     * @return (Right|RightGroup)[]
     */
    public function getChilds()
    {
        return $this->childs;
    }
}
