<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Model\MenuType;
use \RS\Orm\FormObject,
    \RS\Orm\Type,
    \RS\Orm\PropertyIterator,
    \RS\Router\Manager as RouterManager,
    \RS\Router\Route;
    

class Article extends AbstractType
{
    /**
    * Возвращает уникальный идентификатор для данного типа
    * 
    * @return string
    */
    public function getId()
    {
        return 'article';
    }
    
    /**
    * Возвращает название данного типа
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Статья');
    }
    
    /**
    * Возвращает описание данного типа 
    * 
    * @return string
    */
    public function getDescription()
    {
        return t('Позволяет задать произвольный текст на странице пункта меню');
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
            $api = \Menu\Model\Api::getInstance();
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
            'content' => new Type\Richtext([
                'description' => t('Статья'),
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
        $api = \Menu\Model\Api::getInstance();
        $cur_item = $api->getCurrentMenuItem();
        if ($cur_item['id']) {
            $list = $api->getPathToFirst($cur_item['id']);
            foreach($list as $item) {
                if ($item['id'] == $this->menu['id']) return true;
            }
        }
        return false;
    }
    
    /**
    * Возвращает шаблон для данного пункта меню
    * 
    * @return string
    */
    public function getTemplate()
    {
        return 'front_article.tpl';
    }
    
    public function getTemplateVar()
    {
        //Для совместимости возвращаем объект статьи
        $article = new \Article\Model\Orm\Article();
        $article['content'] = $this->menu['content'];
        
        return [
            'article' => $article
        ];
    }
}