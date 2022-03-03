<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;

/**
* Тип колонки - действия со строкой данных.
*/
class Actions extends AbstractType
{
    const
        AUTO_WIDTH = 22;
        
    public 
        $property = [
            'ThAttr' => ['class' => 'settings'],
            'customizable' => false
    ];

    protected
        $actions = [],
        $head_template = 'system/admin/html_elements/table/coltype/actions_head.tpl',
        $body_template = 'system/admin/html_elements/table/coltype/actions.tpl';        
        
    function __construct($field, array $actions, $property = null)
    {
        parent::__construct($field, null, $property);
        $this->setActions($actions);
        if (!isset($property['noAutoWidth'])) $this->setAutoWidth();
    }
    
    function setAutoWidth()
    {
        if (!isset($this->property['ThAttr']['style'])) {
            $this->property['ThAttr']['style'] = '';
        }
        if (!isset($this->property['TdAttr']['style'])) {
            $this->property['TdAttr']['style'] = '';
        }
        $width = self::AUTO_WIDTH * (count($this->actions) ?: 1);
        
        $this->property['ThAttr']['style'] .= "width:".$width."px;";
        $this->property['TdAttr']['style'] .= "width:".$width."px;";
    }
    
    /**
    * Добавить список действий, которые можно произвести со строкой
    * 
    * @param array $actions
    */
    function setActions(array $actions)
    {
        foreach($actions as $action) {
            $this->addAction($action);
        }
    }

    /**
     * Добавить объект действия
     *
     * @param Action\AbstractAction $action - добавляемое действие
     * @param int $position - позиция в списке, если не указана - действие будет добавлено в конец
     * @return void
     */
    function addAction(Action\AbstractAction $action, $position = null)
    {
        $action->setContainer($this);
        
        if ($position !== null) {
            $this->actions = array_merge(array_slice($this->actions, 0, $position), [$action], array_slice($this->actions, $position));
        } else {
            $this->actions[] = $action;
        }
    }
    
    /**
    * Удаляет действие
    * @param integer $position - порядковый номер действия
    * @return void
    */
    function removeAction($position)
    {
        unset($this->actions[$position]);
    }
    
    /**
    * Возвращает список объектов действий
    * @return Action\AbstractAction[]
    */
    function getActions()
    {
        return $this->actions;
    }
    
    function getLineAttr(Action\AbstractAction $action, $index = 'attr')
    {
        $str = '';
        if (isset($action->options[$index]))
            foreach($action->options[$index] as $key=>$val) {
                if ($key[0] == '@') {
                    $val = $this->getHref($val);
                    $key = substr($key, 1);
                }
                $str .= " $key=\"$val\"";
            }
        return $str;
    }

    function __clone()
    {
        foreach($this->actions as $key => $action) {
            $this->actions[$key] = clone $action;
            $this->actions[$key]->setContainer($this);
        }
    }
}
