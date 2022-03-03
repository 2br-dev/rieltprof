<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Debug\Tool;

/**
* Базовый класс всех значков в режиме отладки
*/
abstract class AbstractTool implements ToolInterface
{
    protected 
        $options = [
            'attr' => []
    ],
        $text = '',
        $template = 'system/debug/icon_def.tpl',
        $uniq_group;
        
    function __construct(array $options = null)
    {
        if ($options) {
            $this->options = array_replace_recursive($this->options, $options);
        }
        @$this->options['attr']['class'] = 'debug-icon '.$this->options['attr']['class'];
    }
    
    /**
    * Возвращает HTML код значка для панели инструментов
    * @return string
    */
    function getView()
    {
        $html = new \RS\View\Engine();
        $html->assign('tool', $this);
        return $html->fetch($this->template);
    }
    
    /**
    * Возвращает содержимое ссылки
    * @return string
    */
    function getText()
    {
        return $this->text;
    }
    
    /**
    * Возвращает массив с заданными атрибутами
    * @return array
    */
    function getAttr()
    {
        return $this->options['attr'];
    }
    
    /**
    * Устанавливает идентификатор группы, к которой относится инструмент
    */
    function setUniq($uniq_group)
    {
        $this->uniq_group = $uniq_group;
    }
    
    /**
    * Возвращает уникальный номер группы
    * @return integer
    */
    function getUniq()
    {
        return $this->uniq_group;
    }
}

