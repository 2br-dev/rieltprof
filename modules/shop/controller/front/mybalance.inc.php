<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use RS\Application\Application;
use RS\Application\Auth;
use RS\Controller\AuthorizedFront;
use RS\Router\Manager as RouterManager;
use Shop\Model\OnlinePayApi;
use Shop\Model\Orm\Payment;
use Shop\Model\PaymentApi;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use \Shop\Model\PaymentType\PersonalAccount;
use Shop\Model\TransactionApi;

/**
* Контроллер лицевого счета
*/
class MyBalance extends AuthorizedFront
{
    function init()
    {
        if (!$this->getModuleConfig()->use_personal_account) {
            $this->e404(t('Данный раздел отключен администратором'));
        }
    }


    function actionIndex()
    {
        $title = t('Лицевой счет');
        $this->app->title->addSection($title);
        $this->app->breadcrumbs->addBreadCrumb($title);        
                
        $page_size = 10;
        $page = $this->url->get('p', TYPE_INTEGER, 1);
        
        $transApi = new \Shop\Model\TransactionApi();
        $transApi->setPersonalAccountTransactionsFilter();
        $transApi->setFilter('status', 'success');
        
        $list = $transApi->getList($page, $page_size, 'id desc');

        $balance_string = $this->getFormattedBalance();

        $paginator = new \RS\Helper\Paginator($page, $transApi->getListCount(), $page_size);
        $this->view->assign([
            'paginator' => $paginator,
            'balance_string' => $balance_string,
            'list' => $list
        ]);

        return $this->result->setTemplate('mybalance/mybalance.tpl');
    }
    
    /**
    * Пополнение лицевого счета
    * 
    */
    function actionAddFunds()
    {
        $title = t('Пополнение лицевого счета');
        $this->app->title->addSection($title);
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Лицевой счет'), $this->router->getUrl('shop-front-mybalance'))
            ->addBreadCrumb($title);
        
        $transApi = new TransactionApi();

        $my_type = $this->user['is_company'] ? 'company' : 'user';
        $pay_api = new PaymentApi();
        $pay_api->setFilter('public', 1);
        $pay_api->setFilter('user_type', ['all', $my_type], 'in');     // Разный список оплат, в зависимости от того, компания ли это или обынчй пользователь
        $pay_api->setFilter('class', PersonalAccount::SHORT_NAME, '!=');    // Исключаем из способов пополнения счета оплату с самого себя
        $pay_api->setFilter('target', ['all', 'refill'], 'in');
        $pay_list = [];
        foreach ($pay_api->getList() as $payment) {
            /** @var Payment $payment */
            $type_object = $payment->getTypeObject();
            if ($type_object instanceof InterfaceRecurringPayments && $type_object->getRecurringPaymentsType() == InterfaceRecurringPayments::RECURRING_TYPE_ONLY_SAVE_METHOD) {
                continue;
            }
            $pay_list[] = $payment;
        }
        $this->view->assign('pay_list', $pay_list);
        $this->view->assign('api', $transApi);                 
        
        if ($this->url->isPost()) {
            $payment_id = $this->url->post('payment', TYPE_INTEGER);
            $payment = new Payment($payment_id);
            $cost = $this->url->post('cost', TYPE_FLOAT);
            if (!$payment['id']) {
                $transApi->addError(t('Укажите способ оплаты'), 'payment');
            }
            if (!$cost) {
                $transApi->addError(t('Укажите сумму'), 'cost');
            }
            if (!$transApi->hasError()) {
                if ($payment->getTypeObject()->canOnlinePay()) {
                    $params = [
                        'type' => OnlinePayApi::TYPE_BALANCE_ADD_FOUNDS,
                        'cost' => (string)$cost,
                        'payment' => $payment['id'],
                    ];
                    $url_params = [
                        'Act' => 'pay',
                        'params' => $params,
                        'sign' => OnlinePayApi::getPayParamsSign($params),
                    ];
                    Application::getInstance()->redirect(RouterManager::obj()->getUrl('shop-front-onlinepay', $url_params));
                }

                // Создаем транзакцию для пополнения счета
                $transaction = $transApi->createTransactionForAddFunds(Auth::getCurrentUser()['id'], $payment['id'], $cost);
                if ($transaction['id']) {
                    if ($transaction->getPayment()->getTypeObject()->isPostQuery()) { //Если нужен пост запрос
                        $url = $transaction->getPayUrl();
                        $this->view->assign([
                            'url' => $url,
                            'transaction' => $transaction
                        ]);
                        $this->wrapOutput(false);
                        return $this->result->setTemplate("%shop%/onlinepay/post.tpl");
                    } else {
                        Application::getInstance()->redirect($transaction->getPayUrl());
                    }
                } else {
                    $transApi->addError($transaction->getErrorsStr());
                }
            }
        }

        $balance_string = $this->getFormattedBalance();

        $this->view->assign([
            'balance_string' => $balance_string,
            'base_currency' => \Catalog\Model\CurrencyApi::getBaseCurrency(),
            'current_currency' => \Catalog\Model\CurrencyApi::getCurrentCurrency(),
        ]);
        return $this->result->setTemplate('mybalance/addfunds.tpl');
    }

    function getFormattedBalance()
    {
        $cost_in_base_currency = $this->user->getBalance(false, true);
        $cost_in_current_currency = $this->user->getBalance(true, true);
        if($cost_in_base_currency != $cost_in_current_currency){
            $balance_string = $cost_in_base_currency." ($cost_in_current_currency)";
        }else{
            $balance_string = $cost_in_base_currency;
        }
        return $balance_string;

    }
    
    /**
    * Страница подтверждения списания средств с лицевого счета
    * Страница показывется при оплате заказа с помощю способа оплаты "Лицевой счет"
    */
    function actionConfirmPay()
    {
        $transaction_id = $this->url->get('transaction_id', TYPE_INTEGER);
        $transaction = new \Shop\Model\Orm\Transaction($transaction_id);
        $transApi = new \Shop\Model\TransactionApi();
        
        // Проверка существования транзации и принедлежности её этому пользователю
        if(!$transaction->id || $transaction->user_id != $this->user->id){
            $this->e404(t('Транзация не найдена'));
        }      
        
        // Проверка наличия средств на лицевом счете
        if( (float) $this->user->getBalance() < (float) $transaction->cost ){
            $transApi->addError(t('На лицевом счете недостаточно средств для оплаты'));
        }
        
        // Нажатие кнопки "Оплатить"
        if($this->url->isPost()) {
            if(!$transApi->hasError()){
                // Все хорошо. Выдаем товар покупателю (помечаем ордер как оплаченный)
                // Для этого вызываем у транзакции метод onResult, по аналогии с тем, как это делается при 
                // оплате через онлайн способы оплаты
                $transaction->onResult($this->url);
                
                // Средства списываются с лицевого счета благодаря тому, что данная транзакция переходит в статус Success
                // Физически списание средств происходит в методе Transaction::afterWrite()
                
                // Переадресуем на страницу уведомления об успешной оплате
                $url = $this->router->getUrl('shop-front-onlinepay', ['Act' => 'success', 'PaymentType' => PersonalAccount::SHORT_NAME, 'transaction_id' => $transaction->id]);
                Application::getInstance()->redirect($url);
            }
        }
        
        $this->view->assign('transaction', $transaction);
        $this->view->assign('api', $transApi);
        return $this->result->setTemplate('mybalance/confirmpay.tpl');
    }
}

