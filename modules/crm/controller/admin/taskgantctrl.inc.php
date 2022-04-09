<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Model\GantApi;
use Crm\Model\Orm\TaskFilter;
use Crm\Model\TaskFilterApi;
use RS\Controller\Admin\Front;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Toolbar;
use \RS\Html\Toolbar\Button as ToolbarButton;

/**
 * Контроллер управления диаграммой Ганта для задач
 */
class TaskGantCtrl extends Front
{
    protected $preset;
    protected $gant_api;
    protected $date_from;
    protected $date_to;
    protected $view_type;

    function init()
    {
        $this->gant_api = new GantApi();

        $this->preset = $this->getStoredParam('preset', TYPE_INTEGER, 0);
        $this->date_from = $this->getStoredParam('date_from', TYPE_STRING, GantApi::DATE_PRESET_WEEK);
        $this->date_to = $this->getStoredParam('date_to', TYPE_STRING);
        $this->view_type = $this->getStoredParam('view_type', TYPE_STRING, GantApi::VIEW_TYPE_TASK,
            array_keys($this->gant_api->getViewTypes()));

        $this->gant_api->setDateFilter($this->date_from, $this->date_to);
        $this->gant_api->setViewType($this->view_type);
        $this->gant_api->setTaskPreset($this->preset);

        $task_filters_api = new TaskFilterApi();
        $presets = $task_filters_api->getSelectList([0 => t('Все задачи')]);

        $this->view->assign([
            'date_presets' => $this->gant_api->getDateFilterPresets(),
            'presets' => $presets,
            'view_types' => $this->gant_api->getViewTypes(),

            'current_preset' => $this->preset,
            'current_date_range' => $this->gant_api->getCurrentDateRange(),
            'current_view_type' => $this->view_type,
        ]);
    }

    function actionIndex()
    {
        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Диаграмма Ганта'));
        $helper->setTopHelp(t('Диаграмма Ганта наглядно показывает график задач и их статус в диапазоне выбранных дат'));

        $params = [];
        if (!empty($this->preset)) {
            $taskFilter = new TaskFilter($this->preset);
            $params = ['dir' => $taskFilter['id'], 'f' => $taskFilter['filters_arr']];
        }

        $top_toolbar = new Toolbar\Element();
        $top_toolbar->addItem(new ToolbarButton\Add($this->router->getAdminUrl('add', [], 'crm-taskctrl'), t('Добавить задачу')));
        $top_toolbar->addItem(new ToolbarButton\Button($this->router->getAdminUrl(false, $params, 'crm-taskctrl'), t('Табличный вид')));

        $top_toolbar->addItem(new ToolbarButton\ModuleConfig(
            $this->router->getAdminUrl('edit', [
                'mod' => $this->mod_name
            ], 'modcontrol-control')), 'moduleconfig');

        $helper->setTopToolbar($top_toolbar);
        $helper->viewAsAny();

        $chart_data = $this->gant_api->getChartData();
        $this->view->assign([
           'chart_data' => $chart_data
        ]);

        if ($this->view_type == GantApi::VIEW_TYPE_TASK) {
            $template = '%crm%/admin/gant/tasks.tpl';
        } else {
            $template = '%crm%/admin/gant/user.tpl';
        }
        $helper->setForm($this->view->fetch($template));

        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Возвращает значение параметра и сохраняет последний выбор в cookie
     *
     * @param string $key название параметра
     * @param mixed $type тип
     * @param mixed $default значение по умолчанию
     * @param array $list список возможных значений
     *
     * @return mixed
     */
    protected function getStoredParam($key, $type, $default = null, $list = null)
    {
        $site_id = \RS\Site\Manager::getSiteId();
        $cookie_key = 'gant_'.$key.'_'.$site_id;

        $stored = $this->url->cookie($cookie_key, $type, $default);
        $value = $this->url->get($key, $type, $stored);

        if ($list) {
            $value = $this->url->convert($value, $list);
        }

        $cookie_expire = time()+60*60*24*730;
        $cookie_path = $this->router->getUrl('main.admin');
        $this->app->headers->addCookie($cookie_key, $value, $cookie_expire, $cookie_path);

        return $value;
    }
}