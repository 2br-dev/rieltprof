<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Filter,
    \RS\Html\Table;
use Shop\Model\TransactionApi;
use Users\Model\Orm\User;

/**
* Контроллр пользователей
*/
class BalanceCtrl extends \RS\Controller\Admin\Crud
{
    protected
        $writeoff,
        /**
        * @var \Shop\Model\TransactionApi
        */
        $transaction_api;
    
    function __construct()
    {
        $this->transaction_api = new TransactionApi();
        parent::__construct(new \Users\Model\Api());
    }
    
    function helperAddFunds()
    {
        $this->writeoff = $this->url->get('writeoff', TYPE_STRING); // Флаг списания, 1 - списание, 0 - пополнение
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this, $this->transaction_api);       
        
        $buttons = new \RS\Html\Toolbar\Element([]);
        $buttons->addItem(new \RS\Html\Toolbar\Button\SaveForm(null, $this->writeoff ? t('списать') : t('пополнить')));
        $buttons->addItem(new \RS\Html\Toolbar\Button\Cancel($this->url->getSavedUrl($this->controller_name.'index'), t('отмена')));
        
        return $helper
            ->viewAsForm()
            ->setBottomToolbar( $buttons )
            ->setTopTitle($this->writeoff ? t('Списание с баланса') : t('Пополнение баланса'))
            ->setFormSwitch($this->writeoff ? 'minus' : 'plus');
    }
    
    function actionAddFunds()
    {        
        $user_id    = $this->url->get('id', TYPE_INTEGER);

        if (!$user_id) {
            return $this->result
                            ->addSection('close_dialog', true)
                            ->addEMessage(t('Не указан пользователь. Невозможно изменить баланс'));
        }

        /**
        * @var \RS\Controller\Admin\Helper\CrudCollection
        */
        $helper = $this->getHelper();
        $elem = $this->transaction_api->getElement();
        $elem->user_id = $user_id;
        $elem['__cost']->setDescription($this->writeoff ? t('Сумма списания') : t('Сумма пополнения'));
        
        if ($this->url->isPost()) 
        {            
            $cost = abs(str_replace(' ', '', $this->url->post('cost', TYPE_STRING)));
            $data = [
                'user_id' => $user_id,
                'dateof' => date('Y-m-d H:i:s'),
                'status' => \Shop\Model\Orm\Transaction::STATUS_SUCCESS,
                'payment' => 0,
                'personal_account' => 1,
                'order_id' => 0,
                'cost' => $this->writeoff ? -$cost : $cost
                ] + $this->url->getSource(POST);
            
            $this->result->setSuccess( $this->transaction_api->changeBalance($data) );
            
            if(!$this->result->isSuccess()){
                $this->result->setErrors($elem->getDisplayErrors());
                return $this->result;
            }
            
            if($this->writeoff){
                $phrase = t('Со счета успешно списана сумма %0.', [$cost]);

                if ($elem['force_create_receipt']) {
                    $result = $this->transaction_api->createReceipt($elem);
                    if ($result === true) {
                        $phrase .= t(' Отправлен запрос на пробитие чека');
                    } else {
                        $phrase .= t(' Ошибка чека: %0', [$result]);
                    }
                }

                $this->result->addMessage($phrase);
            }
            else{       
                $this->result->addMessage(t('Счет успешно пополнен на сумму %0', [$cost]));
            }
            return $this->result;
        }

        return $this->result->setTemplate( $helper['template'] );
    }

    function actionFixBalance()
    {
        $user_id    = $this->url->get('id', TYPE_INTEGER);
        $user = new User($user_id);
        $transApi = new TransactionApi();

        $user->balance = $transApi->getBalance($user->id);
        $user->balance_sign = $transApi->getBalanceSign($user->balance, $user->id);
        $user->update();

        return $this->result->addMessage(t('Баланс и его подпись исправлены в соответсвии с историей транзакций'));
    }
    
}

