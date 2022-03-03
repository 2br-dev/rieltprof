<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Application;

use RS\Site\Manager as SiteManager;

/**
* Класс отвечает за хранение хлемных крошек для текущей страницы
*/
class BreadCrumbs
{
    protected
        $add_main = true,
        $breadcrumbs = [],
        $main_breadcrumb;

    function __construct()
    {        
        $this->setMainBreadCrumb(t('Главная'));
    }
    
    /**
    * Устанавливает, добавлять ли ссылку на главную страницу сайта
    * 
    * @param bool $bool - если true, то ссылка будет добавлена в начале массива
    * @return BreadCrumbs
    */
    function mainLink($bool)
    {
        $this->add_main = $bool;
        return $this;
    }
    
    /**
    * Устанавливает крошку главной страницы
    * 
    * @param string $title
    * @param string $href
    */
    function setMainBreadCrumb($title, $href = null)
    {
        if ($site = SiteManager::getSite()) {
            $root_url = $site->getRootUrl();
        } else {
            $root_url = \Setup::$FOLDER.'/';
        }

        $this->main_breadcrumb = [
            'title' => $title,
            'href' => $href ? $href : $root_url
        ];
    }

    /**
     * Возвращает массив с текущим главным элементов хлебных крошек
     *
     * @return array
     */
    function getMainBreadCrumb()
    {
        return $this->main_breadcrumb;
    }
    
    /**
    * Добавляет секцию в хлебные крошки
    * 
    * @param string $title Название секции
    * @param string $href Ссылка
    * @param integer|null $position Порядковый номер крошки в списке. Если Null, то добавляется в конец
    * @return BreadCrumbs
    */
    function addBreadCrumb($title, $href = null, $position = null)
    {
        $item = [
            'title' => $title,
            'href' => $href
        ];
        
        if ($position !== null) {
            $this->breadcrumbs = array_merge(array_slice($this->breadcrumbs, 0, $position), [$item], array_slice($this->breadcrumbs, $position));
        } else {
            $this->breadcrumbs[] = $item;
        }
        return $this;
    }

    /**
     * Удаляет секцию из определенной позиции. Если не передан $position,
     * то удалена будет последняя позиция
     *
     * @param integer $position
     * @return BreadCrumbs
     */
    function removeBreadCrumb($position = null)
    {
        if ($this->breadcrumbs) {
            if ($position === null) {
                $position = count($this->breadcrumbs) - 1;
            }

            unset($this->breadcrumbs[$position]);
        }

        return $this;
    }
    
    /**
    * Устанавливает все секции хлебныйх крошек 
    * 
    * @param array $breadcrumbs массив с хлебными 
    * @return BreadCrumbs
    */
    function setBreadCrumbs(array $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
        return $this;
    }
    
    /**
    * Возвращает массив с секциями хлебных крошек для текущей страницы
    * 
    * @param mixed $add_main
    * @return BreadCrumbs
    */
    function getBreadCrumbs()
    {
        if (empty($this->breadcrumbs)) return [];
        return $this->add_main ? array_merge([$this->getMainBreadCrumb()], $this->breadcrumbs) : $this->breadcrumbs;
    }
}
