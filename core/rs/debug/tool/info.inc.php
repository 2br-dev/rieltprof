<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Debug\Tool;

use RS\Controller\Block;
use RS\Performance\Timing;

/**
* Класс кнопки "Информация" в панели инструментов режима отладки.
* Отображает информацию о блоке (блок-контроллере)
*/
class Info extends AbstractTool
{
    public 
        $render_template;
        
    protected
        $controller_name,
        $module,
        $config,
        $template = 'system/debug/icon_info.tpl';
    
    /**
    * Конструктор кнопки "информация"
    * 
    * @param string $mod_name - имя модуля (имя папки)
    * @param string $controller_name - имя класса контроллера
    * @param array|null $options - дополнительные аттрибуты html элемента
    */
    function __construct($mod_name, $controller_name, array $options = null)
    {
        parent::__construct($options);        
        $this->module = new \RS\Module\Item($mod_name);
        $this->config = $this->module->getConfig();
        $this->controller_name = $controller_name;
    }
    
    /**
    * Возвращает объект конфигурации модуля
    * 
    * @param string $field
    * @return \RS\Orm\ConfigObject
    */
    function getConfig($field = null)
    {
        return isset($field) ? $this->config[$field] : $this->config;
    }
    
    /**
    * Возвращает объект модуля
    * @return \RS\Module\Item
    */
    function getModule()
    {
        return $this->module;
    }
    
    /**
    * Возвращает имя контроллера
    * @return string
    */
    function getControllerName()
    {
        return $this->controller_name;
    }

    /**
     * Возвращает текстовое название контроллера
     *
     * @return string
     */
    function getControllerTitle()
    {
        $class_name = $this->getControllerName();
        if ($class_name) {
            $controller = new $class_name;
            if ($controller instanceof Block) {
                return $controller->getInfo('title');
            }
        }
        return '';
    }

    /**
     * Для блочных контроллеров возвращает ID блока
     *
     * @return integer
     */
    function getBlockId()
    {
        return $this->options['block_id'];
    }

    /**
     * Возвращает ID страницы
     *
     * @return string
     */
    function getPageId()
    {
        return Timing::getCurrentPageId();
    }
    
    /**
    * Добывает информацию о переменных из результата выполнения контроллера
    * @return array
    */
    function parseVars(\RS\Controller\Result\ITemplateResult $actionResult)
    {
        $vars = $actionResult->getTemplateVars();
        $var_list = [];
        foreach($vars as $key=>$var) 
        {
            $tmp = [];
            $tmp['key'] = $key;
            $tmp['type'] = gettype($var);
            
            switch($tmp['type']) {
                case 'object': {
                    $tmp['type'] .= ' `'.get_class($var).'`'; 
                    break;
                }
                case 'array': {
                    if (count($var)) {
                        $type = gettype(reset($var));
                        if ($type == 'object') {
                            $type = get_class(reset($var));
                        }
                        $tmp['type'] .= ' of `'.$type.'`'; 
                    }
                    $tmp['type'] .= ' ('.count($var).' elements)';
                    break;                    
                }
            }
            $var_list[$key] = $tmp;
        }
        ksort($var_list);
        return $var_list;
    }
}

