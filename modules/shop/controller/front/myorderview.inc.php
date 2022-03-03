<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Front;

/**
* Контроллер мои заказы
*/
class MyOrderView extends \RS\Controller\AuthorizedFront
{
    /**
     * @var \Shop\Model\OrderApi $api
     */
    public $api;
    /**
     * @var \Shop\Model\Orm\Order $order
     */
    public $order;

    /**
     * Инициализация класса
     */
    function init()
    {
        $order_id = urldecode($this->url->get('order_id', TYPE_STRING));
        $this->api = new \Shop\Model\OrderApi();
        $this->order = $this->api
            ->setFilter([
                'order_num' => $order_id,
                'user_id' => $this->user['id']
            ])->getFirst();
        if (!$this->order){
            $this->e404(t('Заказ не найден'));
        }
        $this->view->assign([
            'order' => $this->order
        ]);
    }

    /**
     * Показ детально заказа
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {
        $this->router->getCurrentRoute()->order_id = $this->order['id'];
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Мои заказы'), $this->router->getUrl('shop-front-myorders'))
            ->addBreadCrumb(t('Заказ №%0', [$this->order['order_num']]));
            
        return $this->result->setTemplate('myorder_view.tpl');
    }


    /**
     * Метод для смены оплаты
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionChangePayment()
    {
        $pay_api  = new \Shop\Model\PaymentApi();
        $payments = $pay_api->getCheckoutPaymentList($this->user, $this->order);

        //Если данные получены и мы можем менять статус
        if ($this->order->canChangePayment()){
            if ($this->isMyPost()){
                if (($this->order['user_id'] == $this->user['id']) && ($selected = $this->url->request('payment', TYPE_INTEGER))){
                    $this->order['payment'] = $selected;
                    $cart = $this->order->getCart();
                    $cart->makeOrderCart();
                    $cart->saveOrderData();
                    $this->order->update();
                    $this->view->assign([
                        'success' => true
                    ]);
                    $this->result->addSection('closeDialog', true)->addSection('reloadPage', true);
                }else{
                    $this->api->addError(t('Нет прав на изменение заказа'));
                }
            }
        }else{
            $this->api->addError(t('Ваш заказ оплачен или его пока невозможно оплатить'));
        }


        $this->view->assign([
           'payments' => $payments,
           'errors' => $this->api->getErrors(),
        ]);

        return $this->result->setTemplate('myorder_change_payment.tpl');
    }
}
