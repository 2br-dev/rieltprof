<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Menu\Model\MenuType;

use RS\Orm\FormObject;
use RS\Orm\Type;
use RS\Orm\PropertyIterator;
use RS\Router\Manager as RouterManager;
use RS\Router\Route;
    

class Page extends AbstractType
{
    /**
    * Возвращает уникальный идентификатор для данного типа
    * 
    * @return string
    */
    public function getId()
    {
        return 'empty';
    }
    
    /**
    * Возвращает название данного типа
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Страница');
    }
    
    /**
    * Возвращает описание данного типа 
    * 
    * @return string
    */
    public function getDescription()
    {
        return t('Означает, что Вы можете сконструировать страницу для данного пункта меню в разделе Конструктор сайта');
    }
    
    /**
    * Возвращает маршрут, если пункт меню должен добавлять его, 
    * в противном случае - false
    * 
    * @return \RS\Router\Route | null
    */
    public function getRoute()
    {
        static $api;
        
        if ($api === null) {
            $api = new \Menu\Model\Api();
            $api->setFilter('menutype', 'user');
        }
            
        $path_str = '';
        $sections = '';
        foreach($api->getPathToFirst($this->menu['id']) as $one) {
            $path_str .= ' > '.$one['title'];
            if (!$one['hide_from_url']) {
                $sections .= '/'.str_replace(' ','-', $one['alias']);
            }
        }
        
        if ($sections !== '') {
            $route = new Route(
                'menu.item_'.$this->menu['id'],
                $sections.'/',
                [
                    'controller' => 'menu-front-menupage',
                    'menu_item_id' => $this->menu['id'],
                    'menu_object' => $this->menu
                ],
                t('Меню').$path_str,
                false,
                '^{pattern}$'
            );
            return $route;
        }
    }
    
    /**
    * Возвращает поля, которые должны быть отображены при выборе данного типа
    * 
    * @return \RS\Orm\FormObject
    */
    public function getFormObject()
    {
        $properties = new PropertyIterator([
            'link_template' => new Type\Template([
                'description' => t('Шаблон'),
            ]),
        ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }
    
    /**
    * Возвращает ссылку, на которую должен вести данный пункт меню
    * 
    * @return string
    */
    public function getHref($absolute = false)
    {
        return RouterManager::obj()->getUrl('menu.item_'.$this->menu['id'], [], $absolute);
    }
    
    /**
    * Возвращает true, если пункт меню активен в настоящее время
    * 
    * @return bool
    */
    public function isActive()
    {
        static $act_path;
        
        if (!isset($act_path)) {
            $api = \Menu\Model\Api::getInstance();
            $cur_item = $api->getCurrentMenuItem();
            
            $act_path = [];
            if ($cur_item['id']) {
                $list = $api->getPathToFirst($cur_item['id']);
                foreach($list as $item) {
                    $act_path[] = $item['id'];
                }
            }
        }
        
        return (in_array($this->menu['id'], $act_path));
    }
    
    /**
    * Возвращает шаблон для данного пункта меню
    * 
    * @return string
    */
    public function getTemplate()
    {
        return $this->menu['link_template'];
    }
}