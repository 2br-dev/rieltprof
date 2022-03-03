<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;

use Main\Config\ModuleRights;
use RS\Performance\Timing;

class Debug extends \RS\Controller\Admin\Front
{
    protected $sort;
    protected $nsort;
    protected $default_n_sort;

    function init()
    {
        $this->wrapOutput(false);
        if (!$this->user->checkModuleRight('main', ModuleRights::RIGHT_DEBUG_MODE)) {
            return t('Недостаточно прав');
        }

        $this->sort = $this->url->get('sort', TYPE_STRING, 'time');
        $this->nsort = $this->url->get('nsort', TYPE_STRING, 'desc');

        $this->default_n_sort = [ //Направление сортировки по умолчанию
            'default' => 'asc',
            'time' => 'desc',
            'sql_time' => 'desc',
        ];

        if ($this->default_n_sort[$this->sort] == $this->nsort) {
            $this->default_n_sort[$this->sort] = ($this->default_n_sort[$this->sort] == 'asc') ? 'desc' : 'asc';
        }

        $this->view->assign([
            'sort' => $this->sort,
            'nsort' => $this->nsort,
            'default_n_sort' => $this->default_n_sort,
        ]);
    }

    /**
     * Отображает переменные, которые были переданы в контроллер
     *
     * @return string
     * @throws \RS\Event\Exception
     * @throws \SmartyException
     */
    function actionShowVars()
    {
        $toolgroup = $this->url->get('toolgroup', TYPE_STRING, 0);
        $page_id = $this->url->get('page_id', TYPE_INTEGER);
        $block_id = $this->url->get('block_id', TYPE_INTEGER);

        $timing = Timing::getInstance($page_id);
        $group = \RS\Debug\Group::getInstance($toolgroup);

        $vars = $group->getData('info', 'vars', []);
        $controller_data = $group->getData('info', 'controller', []);

        $report = $timing->getReport('time', false, $block_id);

        $this->view->assign([
            'timing_is_enable' => \Setup::$ENABLE_DEBUG_PROFILING,
            'report' => $report,
            'var_list' => $vars,
            'controller_data' => $controller_data
        ]);

        $this->app->removeJs()->removeCss();
        $this->app->title->addSection(t('Список переменных в шаблоне'));
        
        return $this->wrapHtml( $this->view->fetch('%system%/debug/showvars.tpl') );
    }

    /**
     * Отображает отчет о производительности
     *
     * @return string
     * @throws \RS\Event\Exception
     * @throws \SmartyException
     */
    function actionPageReport()
    {
        if (!\Setup::$ENABLE_DEBUG_PROFILING) {
            $this->e404();
        }
        
        $page_id = $this->url->get('page_id', TYPE_INTEGER);

        $timing = Timing::getInstance($page_id);
        $report = $timing->getReport($this->sort, $this->nsort == 'asc');

        $this->view->assign([
            'report' => $report
        ]);

        $this->app->removeJs()->removeCss();
        $this->app->title->addSection(t('Отчет о производительности страницы'));
        $this->app->addJsVar('performance_plot_data', $timing->getPlotData());
        $this->app->setBodyClass('admin-style');

        return $this->wrapHtml($this->view->fetch('%system%/debug/performance_report.tpl'));
    }
}

