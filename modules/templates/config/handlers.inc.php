<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Config;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        if (\Setup::$INSTALLED) {
            $this->bind('getmenus');
            $this->bind('orm.afterwrite.site-site', $this, 'afterCreateSite');
            $this->bind('orm.delete.site-site', $this, 'deleteSite');
        }
    }

    /**
     * Обработчик события "создание нового сайта". Инсталирует выбранную тему
     *
     * @param mixed $params
     */
    public static function afterCreateSite($params)
    {
        if ($params['flag'] == \RS\Orm\AbstractObject::INSERT_FLAG) { //Если это создание нового сайта
            $site = $params['orm'];
            $theme_item = new \RS\Theme\Item($site['theme']);
            $theme_item->setThisTheme(null, $site['id']);
        }
    }

    /**
     * Обработчик события "удаление сайта". Удаляет блоки с сайта.
     *
     * @param mixed $params
     */
    public static function deleteSite($params)
    {
        $site = $params['orm'];
        $pages = \RS\Orm\Request::make()
            ->from(new \Templates\Model\Orm\SectionPage())
            ->where(['site_id' => $site['id']])
            ->objects();

        foreach ($pages as $page) {
            $page->delete();
        }
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Шаблоны'),
            'alias' => 'templates',
            'link' => '%ADMINPATH%/templates-filemanager/',
            'parent' => 'control',
            'sortn' => 5,
            'typelink' => 'link',
        ];
        $items[] = [
            'title' => t('Конструктор сайта'),
            'alias' => 'blocks',
            'link' => '%ADMINPATH%/templates-blockctrl/',
            'parent' => 'website',
            'sortn' => 40,
            'typelink' => 'link',
        ];
        return $items;
    }
}

