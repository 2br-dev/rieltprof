<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Admin;
use Main\Config\ModuleRights;
use Main\Model\Orm\DisableRoute;
use RS\AccessControl\Rights;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Table;

/**
* Контроллер списка маршрутов. В данном разделе можно увидеть все маршруты, которые присутствуют в системе
*/
class Routes extends \RS\Controller\Admin\Front
{
    protected $disabled_routes;

    function init()
    {
        $this->disabled_routes = DisableRoute::getDisabledRoutes();
    }

    function actionIndex()
    {
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this, null, $this->url);
        $helper->setTopTitle(t('Маршруты в системе'));
        $helper->setTopHelp($this->view->fetch('help/routes_index.tpl'));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Text('id', 'ID'),
                new TableType\Text('description', t('Описание')),
                new TableType\Usertpl('patterns', t('Маска пути'), '%main%/pageseo_column_route.tpl', ['TdAttr' => ['class' => 'cell-small']]),
                new TableType\StrYesno('hidden', t('Скрытый')),
                new TableType\YesNo('active', t('Активный'), [
                    'toggleUrl' => $this->router->getAdminPattern('AjaxToggleRoute', [':id' => '@id']),
                    'attrCallback' => function($cell, $index, $source) {
                        if ($cell->getRow()['id'] == 'main.admin') {
                            $source[$index]['class'] .= ' disabled';
                        }

                        return $source[$index] ?? [];
                    }
                ]),
            ]
        ]));
        
        $uri = $this->url->request('uri', TYPE_STRING, false);
        $host = $this->url->request('host', TYPE_STRING, $this->url->server('HTTP_HOST', TYPE_STRING));

        $routes = \RS\Router\Manager::obj()->getRoutes();
        $data = [];
        $i = 0;
        $selected = null;
        foreach($routes as $route) {
            $data[$i] = [
                'id' => $route->getId(),
                'patterns' => $route->getPatternsView(),
                'description' => $route->getDescription(),
                'hidden' => $route->isHidden(),
                'active' => !isset($this->disabled_routes[$route->getId()])
            ];
            if (!$selected && $route->match($host, $uri, false)) {
                $helper['table']->getTable()->setRowAttr($i, ['class' => 'hl-row']);
                $selected = $route->getId();
            }
            $i++;
        }
        
        $helper['table']->getTable()->setData($data);
        
        $this->view->assign([
            'elements' => $helper,
            'uri' => $uri,
            'host' => $host,
            'selected' => $selected
        ]);

        $helper->viewAsAny();
        $helper['form'] = $this->view->fetch('routes.tpl');

        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Переключает автивность маршрута
     */
    function actionAjaxToggleRoute()
    {
        if ($error = Rights::CheckRightError($this, ModuleRights::RIGHT_UPDATE)) {
            return $this->result->addEMessage($error);
        }

        $id = $this->url->request('id', TYPE_STRING);

        $disable_route = new DisableRoute();
        if ($disable_route->canDisableRoute($id)) {
            $disable_route['route_id'] = $id;
            if (isset($this->disabled_routes[$id])) {
                $disable_route->delete();
            } else {
                $disable_route->replace();
            }
            $this->result->setSuccess(true);
        } else {
            $this->result->setSuccess(false)->addEMessage(t('Невозможно отключить данный маршрут'));
        }

        return $this->result;
    }
}
