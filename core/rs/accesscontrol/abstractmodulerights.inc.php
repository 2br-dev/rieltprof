<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\AccessControl;

/**
* Абстрактный объект прав модуля
*/
abstract class AbstractModuleRights
{
    protected static
        $instance = [];
    
    protected
        $module,
        $rights = [],
        $right_groups = [],
        $root_items,
        $auto_checkers;
    
    final protected function __construct($module)
    {
        $this->module = $module;
        
        $this->addRights($this->getSelfModuleRights());
        
        $event_name = 'module.getrights.' . $this->module;
        $additional_rights = \RS\Event\Manager::fire($event_name, [])->getResult();
        $this->addRights((array) $additional_rights);
        
        $event_name = 'module.getcheckers.' . $this->module;
        $all_checkers = \RS\Event\Manager::fire($event_name, $this->getSelfAutoCheckers())->getResult();
        foreach ($all_checkers as $checker) {
            $this->auto_checkers[$checker->getCheckerType()][] = $checker;
        }

    }
    
    /**
    * Возвращает экземпляр объекта
    * 
    * @param \RS\Orm\ConfigObject $config - объект конфигурации модуля
    * @return static
    */
    final public static function getInstance(\RS\Orm\ConfigObject $config)
    {
        $module = \RS\Module\Item::nameByObject($config);
        if (!isset(self::$instance[$module])) {
            self::$instance[$module] = new static($module);
        }
        return self::$instance[$module];
    }
    
    /**
    * Возвращает собственные права
    * 
    * @return (Right|RightGroup)[]
    */
    abstract protected function getSelfModuleRights();
    
    /**
    * Возвращает собственные инструкции для автоматических проверок
    * 
    * @return \RS\AccessControl\AutoCheckers\AutoCheckerInterface[]
    */
    abstract protected function getSelfAutoCheckers();
    
    /**
    * Добавляет права 
    * 
    * @param (Right|RightGroup)[] $rights - добавляемые права
    * @param RightGroup[] $parents - список родительских группа прав
    */
    final protected function addRights($rights, $parents = [])
    {
        $parent = end($parents);

        foreach ($rights as $item) {
            if ($item instanceof Right && !isset( $this->rights[$item->getAlias()])) {
                $item->setParents($parents);
                $this->rights[$item->getAlias()] = $item;
                if ($parent === false) {
                    $this->root_items[$item->getAlias()] = $item;
                } else {
                    $this->right_groups[$parent->getAlias()]->addChilds($item);
                }
            }
            
            if ($item instanceof RightGroup) {
                if (!isset($this->right_groups[$item->getAlias()])) {
                    $new_group = new RightGroup($item->getAlias(), $item->getTitle());
                    $this->right_groups[$new_group->getAlias()] = $new_group;
                    if ($parent === false) {
                        $this->root_items[$new_group->getAlias()] = $new_group;
                    }
                }

                $this->addRights($item->getChilds(), array_merge($parents, [$item]));
            }
        }
    }
    
    /**
    * Возвращает существующие права
    * 
    * @return Right[]
    */
    final public function getRights()
    {
        return $this->rights;
    }

    /**
    * Возвращает дерево существующих прав
    *
    * @return (Right|RightGroup)[]
    */
    final public function getRightsTree()
    {
        return $this->root_items;
    }
    
    /**
    * Проверяет наличие права
    * 
    * @param string $alias - идентификатор права
    * @return bool
    */
    final public function hasRight($alias)
    {
        return isset($this->rights[$alias]);
    }
    
    /**
    * Возвращает наименование права, или false если права не существует
    * 
    * @param mixed $alias
    * @return string|false
    */
    final public function getRightTitleWithPath($alias)
    {
        if ($this->hasRight($alias)) {
            return $this->rights[$alias]->getTitleWithPath();
        }
        return false;
    }

    /**
     * Исполняет инструкции автоматической проверки прав
     * в случае успеха возвращает false, иначе - текст ошибки
     *
     * @param string $type - тип объектов автоматической проверки
     * @param array $params - параметры для проверки
     * @return string|false
     */
    final public function checkErrorAutoCheckers($type, $params)
    {
        foreach ($this->auto_checkers[$type] as $checker) {
            if ($error = $checker->checkError($params)) {
                return $error;
            }
        }
        return false;
    }
}
