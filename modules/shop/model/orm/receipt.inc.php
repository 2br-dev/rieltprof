<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\Orm\OrmObject;
use \RS\Orm\Type;
use Shop\Model\CashRegisterApi;
use Shop\Model\CashRegisterType\AbstractProxy;
use Shop\Model\CashRegisterType\AbstractType;
use Shop\Model\CashRegisterType\Stub;
use \Shop\Model\Orm\Order;
use \Shop\Model\Orm\Payment;
use Shop\Model\ReceiptInfo;
use \Users\Model\Orm\User;
use \Shop\Model\PaymentType\PersonalAccount;

/**
 * Чеки
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $sign Подпись чека
 * @property string $uniq_id Идентификатор транзакции от провайдера
 * @property string $type Тип чека
 * @property string $provider Провайдер
 * @property string $url Ссылка на чек покупателю
 * @property string $dateof Дата транзакции
 * @property integer $transaction_id ID связанной транзакции
 * @property float $total Сумма в чеке
 * @property string $status Статус чека
 * @property string $error Ошибка
 * @property string $extra Дополнительное поле для данных
 * @property array $extra_arr 
 * --\--
 */
class Receipt extends OrmObject
{
        
    const STATUS_SUCCESS = 'success';      
    const STATUS_FAIL    = 'fail';  
    const STATUS_WAIT    = 'wait'; 
    
    const TYPE_SELL       = 'sell';      
    const TYPE_REFUND     = 'sell_refund';  
    const TYPE_CORRECTION = 'sell_correction'; 
    
    private $transaction;

    protected static $table = 'receipt';
    
    protected $cache_payment;
        
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'sign' => new Type\Varchar([
                'description' => t('Подпись чека'),
                'index' => true
            ]),
            'uniq_id' => new Type\Varchar([
                'description' => t('Идентификатор транзакции от провайдера'),
                'index' => true
            ]),
            'type' => new Type\Enum(array_keys(self::handbookType()), [
                'allowEmpty' => false,
                'description' => t('Тип чека'),
                'listFromArray' => [self::handbookType()],
                'visible' => false
            ]),
            'provider' => new Type\Varchar([
                'description' => t('Провайдер'),
                'maxLength' => 50,
            ]),
            'url' => new Type\Varchar([
                'description' => t('Ссылка на чек покупателю'),
            ]),
            'dateof' => new Type\Datetime([
                'description' => t('Дата транзакции'),
                'visible' => false
            ]),
            'transaction_id' => new Type\Integer([
                'maxLength' => '11',
                'description' => t('ID связанной транзакции'),
            ]),
            'total' => new Type\Decimal([
                'description' => t('Сумма в чеке'),
                'maxLength' => 20,
                'decimal' => 2
            ]),
            'status' => new Type\Enum(array_keys(self::handbookStatuses()), [
                'allowEmpty' => false,
                'description' => t('Статус чека'),
                'listFromArray' => [self::handbookStatuses()],
                'visible' => false
            ]),
            'error' => new Type\Text([
                'description' => t('Ошибка'),
            ]),
            'extra' => new Type\Text([
                'description' => t('Дополнительное поле для данных'),
                'visible' => false,
            ]),
            'extra_arr' => new Type\ArrayList([
                'visible' => false
            ])
        ]);
    }

    /**
     * Вызывается после загрузки объекта
     * @return void
     */
    function afterObjectLoad()
    {
        $this['extra_arr'] = [];
        if (!empty($this['extra'])) {
            $this['extra_arr'] = unserialize($this['extra']);
        }
    }
    
    /**
    * Возвращает информацию из секции extra
    * 
    * @param string $key - ключ для конкретного массива экстраданных
    * @return array
    */
    function getExtraInfo($key = null)
    {
        if (!$key){
            return $this['extra_arr'];       
        }
        return isset($this['extra_arr'][$key]) ? $this['extra_arr'][$key] : [];
    }
    
    
    /**
    * Добавляет extra информацию в секцию extra по ключу
    * 
    * @param string $key - ключ для записи
    * @param mixed $data - данные для записи
    */
    function setExtraInfo($key, $data)
    {
        $extra_arr         = $this['extra_arr'];
        $extra_arr[$key]   = $data;
        $this['extra_arr'] = $extra_arr;
    }
    
    
    /**
    * Возращает объект транзакции 
    * @return Transaction
    */
    function getTransaction()
    {
        if($this->transaction == null){
            $this->transaction = new Transaction($this['transaction_id']);
        }
        return $this->transaction;
    }


    /**
    * Действия перед записью
    * 
    * @param string $save_flag - insert или update
    */
    function beforeWrite($save_flag)
    {
        $this->before_write_receipt = new Receipt($this['id']);
        if ($save_flag == self::INSERT_FLAG){
            $this['dateof'] = date("Y-m-d H:i:s");    
        }
        $this['extra']  = serialize($this['extra_arr']);
    }
    
    /**
    * Действия после записи
    * 
    * @param string $save_flag - insert или update
    */
    function afterWrite($save_flag)
    {
        if (in_array($this['type'], [self::TYPE_SELL, self::TYPE_REFUND])){
            $transaction = $this->getTransaction();
            $transaction->no_need_check_sign = true;
            switch($this['status']){
                case self::STATUS_SUCCESS:
                    if ($this['type'] == self::TYPE_SELL){
                        $transaction['receipt'] = $transaction::RECEIPT_SUCCESS;    
                    }else if ($this['type'] == self::TYPE_REFUND){
                        $transaction['receipt'] = $transaction::RECEIPT_REFUND_SUCCESS;  
                    }
                    //Отправим чек пользователю
                    $notice = new \Shop\Model\Notice\ReceiptToUser();
                    $notice->init($this);
                    \Alerts\Model\Manager::send($notice);   
                    break;
                case self::STATUS_FAIL:
                    $transaction['receipt'] = $transaction::RECEIPT_FAIL; 
                    break;
            }

            $transaction->update();

            //Если, эта транзакция для заказа, то сменим ему нужный статус
            if ($this['status'] == self::STATUS_SUCCESS && $this['type'] == self::TYPE_SELL && ($transaction->getOrder()->id)){
                $order = $transaction->getOrder();
                $success_status = $transaction->getPayment()->success_status;
                if ($success_status != 0 ){
                     $order['status'] = $success_status;
                     $order->update();
                }
            }
        }
        
        //Если в чеке произошла ошибка, то отправим уведомление об этом
        if (empty($this->before_write_receipt['error']) && !empty($this['error'])){
            $notice = new \Shop\Model\Notice\ReceiptErrorToAdmin();
            $notice->init($this);
            \Alerts\Model\Manager::send($notice);   
        }
    }

    /**
     * Возвращает URL для просмотра выписаного чека
     *
     * @return string
     */
    function getReceiptUrl()
    {
        if ($this['status'] == self::STATUS_SUCCESS
            && $this['type'] != self::TYPE_CORRECTION)
        {
            $api = new CashRegisterApi();
            return $api->getReceiptUrl($this);
        } else {
            return '';
        }
    }

    /**
     * Возвращает провайдера, через которого был выбит чек
     *
     * @return  AbstractType | AbstractProxy | Stub
     * @throws EventException
     * @throws RSException
     */
    function getProvider()
    {
        return CashRegisterApi::getTypeByShortName($this['provider']);
    }

    /**
     * Возвращает стандартизированный объект информации о чеке
     *
     * @return ReceiptInfo
     * @throws EventException
     * @throws RSException
     */
    function getReceiptInfo()
    {
        return $this->getProvider()->getReceiptInfo($this);
    }

    /**
     * Справочник типов чека
     *
     * @return string[]
     */
    public static function handbookType()
    {
        static $types;
        if ($types === null) {
            $types = [
                self::TYPE_SELL => t('Чек продажи'),
                self::TYPE_REFUND => t('Чек возврата'),
                self::TYPE_CORRECTION => t('Чек корректировки'),
            ];
        }
        return $types;
    }

    /**
     * Справочник статусов чека
     *
     * @return string[]
     */
    public static function handbookStatuses()
    {
        static $statuses;
        if ($statuses === null) {
            $statuses = [
                self::STATUS_SUCCESS => t('Успешно'),
                self::STATUS_FAIL => t('Ошибка'),
                self::STATUS_WAIT => t('Ожидание ответа провайдера'),
            ];
        }
        return $statuses;
    }
}
