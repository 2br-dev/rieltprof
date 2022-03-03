<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;

use Main\Model\Widgets as WidgetApi;
use RS\Controller\Admin\Front;
use RS\Controller\Admin\Helper\HeaderPanel as HelperHeaderPanel;

/**
 * Контроллер виджетов на главной странице административной панели
 */
class Widgets extends Front
{
    /** @var WidgetApi */
    protected $api;

    public function init()
    {
        $this->api = new WidgetApi();
        $this->api->setUserId($this->user['id']);
    }

    public function actionIndex()
    {
        $header_panel = HelperHeaderPanel::getInstance();
        $header_panel->addItem(t('Добавить виджет'), null, ['class' => 'addwidget', 'icon' => 'plus']);

        $total = 0;
        $this->view->assign([
            'widgets' => $this->api->getMainList($total),
            'total' => $total
        ]);
        return $this->result->setTemplate('widgets.tpl');
    }

    /**
     * AJAX
     */
    public function actionGetWidgetList()
    {
        $this->view->assign('list', $this->api->getFullList(true, true));
        return $this->result->addSection('title', t('Добавить виджет'))->setTemplate('widget_list.tpl');
    }

    /**
     * AJAX
     */
    public function actionAjaxAddWidget()
    {
        $wclass = $this->url->request('wclass', TYPE_STRING);
        $col = $this->url->request('column', TYPE_INTEGER);
        $pos = $this->url->request('position', TYPE_INTEGER);
        $mode = $this->url->request('mode', TYPE_INTEGER);

        $widget = $this->api->insertWidget($wclass, $col, $pos, $mode); //Записываем в базу информацию о добавлении виджета
        return $this->api->getWidgetOut($wclass, ['widget' => $widget, 'force_wrap' => true]);
    }

    /**
     * AJAX
     */
    public function actionAjaxRemoveWidget()
    {
        $wclass = $this->url->request('wclass', TYPE_STRING);
        $this->api->removeWidget($wclass); //Записываем в базу информацию о добавлении виджета
        return $this->result->setSuccess(true);
    }

    /**
     * AJAX
     */
    public function actionAjaxMoveWidget()
    {
        $mode = $this->url->request('mode', TYPE_INTEGER);
        $wid = $this->url->request('wid', TYPE_INTEGER);
        $col = $this->url->request('col', TYPE_STRING);
        $pos = $this->url->request('pos', TYPE_INTEGER);
        $this->api->moveWidget($wid, $col, $pos, $mode);
        return $this->result->setSuccess(true);
    }

    /**
     * AJAX
     */
    public function actionAjaxRecalculatePositions()
    {
        $this->api->reCalculatePositions();
        return $this->result->setSuccess(true);
    }
}
