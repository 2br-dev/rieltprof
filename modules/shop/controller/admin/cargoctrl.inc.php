<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use Shop\Model\CargoPresetApi;
use Shop\Model\OrderCargoApi;
use Shop\Model\Orm\Cargo\CargoPreset;
use Shop\Model\Orm\Cargo\OrderCargo;
use Shop\Model\Orm\Order;

/**
 * Отвечает за диалог добавления грузовых мест (коробок) к заказу
 */
class CargoCtrl extends Crud
{
    /**
     * @var OrderCargoApi
     */
    protected $api;

    function __construct()
    {
        parent::__construct(new OrderCargoApi);
    }

    function helperIndex()
    {
        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Грузовые места'));
        $helper->setBottomToolbar($this->buttons(['save', 'cancel']));
        $helper->viewAsForm();

        return $helper;
    }

    /**
     * Отображает диалог распределения грузов
     *
     * @return mixed|\RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \SmartyException
     */
    function actionIndex()
    {
        $helper = $this->getHelper();
        $order_id = $this->url->get('order_id', TYPE_INTEGER);
        $cargo_id = $this->url->get('cargo_id', TYPE_INTEGER);
        $order = new Order($order_id);
        if (!$order['id']) {
            $this->e404(t('Заказ не найден'));
        }

        $this->api->setFilter('order_id', $order_id);
        $cargos = $this->api->getList();
        $first_cargo = reset($cargos);
        $current_cargo_id = $cargo_id ?: ($first_cargo['id'] ?? 0);

        $preset_api = new CargoPresetApi();

        if ($this->url->isPost()) {
            $cargos = $this->url->post('cargo', TYPE_ARRAY);
            if ($this->api->saveCargos($order_id, $cargos)) {
                return $this->result
                    ->setSuccess(true)
                    ->setAjaxWindowRedirect($this->router->getAdminUrl('edit', ['id' => $order_id], 'shop-orderctrl'))
                    ->setSuccessText(t('Изменения успешно сохранены'));
            } else {
                return $this->result->setErrors( $this->api->getDisplayErrors() );
            }
        }

        $this->view->assign([
            'presets' => $preset_api->getList(),
            'order' => $order,
            'shipped_uits' => $this->api->getShippedUits($order),
            'cargos' => $cargos,
            'current_cargo_id' => $current_cargo_id ?? 0
        ]);
        $helper->setForm($this->view->fetch('admin/cargo/order_cargo_dialog.tpl'));

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Возвращает шаблоны одной коробки
     */
    function actionGetCargoForm()
    {
        $preset_id = $this->url->get('preset_id', TYPE_INTEGER);
        $preset = new CargoPreset($preset_id ?: null);

        $cargo = new OrderCargo();
        $cargo->getFromArray(array_diff_key($preset->getValues(), array_flip(['site_id', 'sortn', 'id'])));
        $cargo['id'] = -time();

        if (!$preset['id']) {
            $cargo['title'] = t('Коробка');
        }

        $this->view->assign([
            'cargo' => $cargo
        ]);

        return $this->result->addSection([
            'cargo_form' => $this->view->fetch('admin/cargo/cargo_form.tpl'),
            'cargo_item' => $this->view->fetch('admin/cargo/cargo_item.tpl'),
            'cargo_id' => $cargo['id']
        ]);
    }

}