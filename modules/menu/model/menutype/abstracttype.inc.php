<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Model\MenuType;

/**
* Абстрактный класс типа меню
*/
abstract class AbstractType
{
    protected  $menu;

    /**
     * @param \Menu\Model\Orm\Menu $menu
     * @return AbstractType
     */
    function init(\Menu\Model\Orm\Menu $menu)
    {
        $this->menu = $menu;
        return $this;
    }
    
    /**
    * Возвращает уникальный идентификатор для данного типа
    * 
    * @return string
    */
    abstract public function getId();
    
    /**
    * Возвращает название данного типа
    * 
    * @return string
    */
    abstract public function getTitle();
    
    /**
    * Возвращает описание данного типа 
    * 
    * @return string
    */
    abstract public function getDescription();
    
    /**
    * Возвращает ссылку, на которую должен вести данный пункт меню
    * 
    * @return string
    */
    abstract public function getHref($absolute = false);
    
    /**
    * Возвращает true, если пункт меню активен в настоящее время
    * 
    * @return bool
    */
    abstract public function isActive();    
    
    /**
    * Возвращает маршрут, если пункт меню должен добавлять его, 
    * в противном случае - false
    * 
    * @return \RS\Router\Route | false
    */
    public function getRoute()
    {
        return null;
    }
    
    /**
    * Возвращает поля, которые должны быть отображены при выборе данного типа.
    * Возвращенные поля будут добавлены к объекту Пункта меню, соответственно 
    * будут учитываться в БД
    * 
    * @return \RS\Orm\FormObject | null
    */
    public function getFormObject()
    {
        return null;
    }

    /**
     * Возвращает готовый HTML код дополнительных полей
     *
     * @return string
     * @throws \SmartyException
     */
    public function getFormHtml()
    {
        if ($params = $this->getFormObject()) {
            $params->getPropertyIterator();
            if ($this->menu) {
                $params->getFromArray($this->menu->getValues());
            }
            $params->setFormTemplate(strtolower(str_replace('\\', '_', get_class($this))));
            $tpl_folder = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/menu'.\Setup::$MODULE_TPL_FOLDER;
            
            return $params->getForm(null, null, false, null, '%system%/coreobject/tr_form.tpl', $tpl_folder);
        }        
    }
    
    /**
    * Возвращает шаблон для данного пункта меню
    * 
    * @return string
    */
    public function getTemplate()
    {
        return null;
    }
    
    /**
    * Возвращает переменные, которые должны пойти в шаблон
    * 
    * @return array
    */
    public function getTemplateVar()
    {
        return [];
    }
    
    /**
    * Возвращает True, если тип должен быть видимым в окне редактирования пунктов меню
    * 
    * @return bool
    */
    public function isVisible()
    {
        return true;
    }

}
