<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Controller\Front;

use Menu\Model\Api as MenuApi;
use Menu\Model\Orm\Menu;
use RS\Debug\Action as DebugAction;
use RS\Debug\Tool as DebugTool;
use RS\Controller\Front;

/**
 * Фронт контроллер страницы-статьи, которая добавлена через меню
 */
class MenuPage extends Front
{
    function actionIndex()
    {
        /** @var \Menu\Model\Orm\Menu $menu_item */
        $menu_item = $this->url->parameters('menu_object');
        if ($this->app->title->get() == '') {
            $this->app->title->addSection($menu_item['title']);//добавить тайтл по названию пункта меню
        }
        //Наполняем Хлебные крошки
        $api = new MenuApi();
        /** @var Menu[] $path */
        $path = $api->getPathToFirst($menu_item['id']);
        foreach ($path as $item) {
            if ($item['public']) {
                $this->app->breadcrumbs->addBreadCrumb($item['title'], $item->getHref());
            }
        }

        //Устанавливаем инструменты для режима отладки
        if ($debug_group = $this->getDebugGroup()) {
            $create_href = $this->router->getAdminUrl('edit', ['id' => $menu_item['id']], 'menu-ctrl');
            $debug_group->addDebugAction(new DebugAction\Edit($create_href));
            $debug_group->addTool('edit', new DebugTool\Edit($create_href));
        }

        //Формируем вывод
        $this->view->assign(
            ['menu_item' => $menu_item] +
            $menu_item->getTypeObject()->getTemplateVar()
        );

        if ($template = $menu_item->getTypeObject()->getTemplate()) {
            return $this->result->setTemplate($template);
        }
        return null;
    }
}
