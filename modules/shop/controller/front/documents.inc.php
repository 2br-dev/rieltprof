<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Front;

/**
* Контроллер возвращает документы на оплату заказов
*/
class Documents extends \RS\Controller\Front
{
    function actionIndex()
    {

        $order_hash     = $this->url->request('order', TYPE_STRING);
        $transaction_sign = $this->url->request('transaction', TYPE_STRING);
        $doc_key        = $this->url->request('doc_key', TYPE_STRING);
        
        if($order_hash){
            $order = \Shop\Model\Orm\Order::loadByWhere([
                'hash' => $order_hash
            ]);
            
            if (!$order['id']) {
                $this->e404(t('Заказ не найден'));
            }
            
            $this->wrapOutput(false);
            return $order->getPayment()->getTypeObject()->getDocHtml($doc_key);
        }
        
        if($transaction_sign){
            $transaction = \Shop\Model\Orm\Transaction::loadByWhere([
                'sign' => $transaction_sign
            ]);
            $this->wrapOutput(false);
            if ($transaction->id) {
                return $transaction->getPayment()->getTypeObject()->getDocHtml($doc_key);
            }
            $this->e404(t('Транзакция не найдена'));
        }
    }
}
