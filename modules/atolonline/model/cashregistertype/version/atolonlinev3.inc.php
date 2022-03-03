<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace AtolOnline\Model\CashRegisterType\Version;

use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Http\Request as HttpRequest;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Router\Manager as RouterManager;
use Shop\Model\CashRegisterApi;
use Shop\Model\Orm\Receipt;
use RS\Orm\Type;
use Shop\Model\Orm\Transaction;
use Shop\Model\PaymentType\PersonalAccount;
use Shop\Model\ReceiptInfo;

/**
 * Класс интерграции с АТОЛ Онлайн
 */
class AtolOnlineV3 extends \Shop\Model\CashRegisterType\AbstractType
{
    protected $tokenid = ""; //Токен полученный при авторизации
    protected $group   = ""; //Группа ККТ
    protected $inn     = ""; //ИНН
    
    const API_URL = "https://online.atol.ru/possystem/v3/";
    const API_RECEIPT_URL = "https://lk.platformaofd.ru/web/noauth/cheque";
    //Операции
    const OPERATION_AUTH = "getToken"; //авторизация

    const PAYMENT_TYPE_CACHLESS = 1;
    const PAYMENT_TYPE_FROM_ADVANCE = 2;

    /**
     * Возвращает URL для запросов
     *
     * @return string
     */
    function getApiUrl()
    {
        if ($url = ConfigLoader::byModule(__CLASS__)->service_url){

            return $url;
        }
        else {
            return static::API_URL;
        }
    }
    /**
     * Возвращает поддерживаемый список налогов
     *
     * @return array
     */
    public static function getTaxesList()
    {
        return [
            static::TAX_NONE => t('Без НДС'),
            static::TAX_VAT0 => t('НДС по ставке 0%'),
            static::TAX_VAT10 => t('НДС чека по ставке 10%;'),
            static::TAX_VAT18 => t('НДС чека по ставке 18%'),
            static::TAX_VAT20 => t('НДС чека по ставке 20%'),
            static::TAX_VAT110 => t('НДС чека по расчетной ставке 10/110'),
            static::TAX_VAT118 => t('НДС чека по расчетной ставке 18/118'),
            static::TAX_VAT120 => t('НДС чека по расчетной ставке 20/120'),
        ];
    }
    
    /**
    * Возвращает название расчетного модуля (онлайн кассы)
    * 
    * @return string
    */
    function getTitle() 
    {
        return t('Атол онлайн');
    }                                                      
    
    /**
    * Возвращает идентификатор данного типа онлайн кассы. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'atolonline';
    }
    
    
    /**
    * Делает запрос на авторизацию
    * 
    * @return array|false
    */
    private function makeAuthRequest()
    {
        $login = $this->getOption('login', ''); 
        $pass  = $this->getOption('pass', ''); 
        if (empty($login) || empty($pass)){
            $this->addError(t('Логин или пароль не указан'));
        }
        $this->group = $this->getOption('group_code');
        if (empty($this->group)){
           $this->addError(t('Необходимо обязательно указать группу.'));
        }
        $this->inn = $this->getOption('inn');
        if (empty($this->inn)){
           $this->addError(t('Необходимо обязательно указать ИНН организации.'));
        }
        if ($this->hasError()){
           return false; 
        }
        $url = $this->getTokenUrl();
        $params = [
            'login' => $login,
            'pass' => $pass
        ];
        $response = $this->createRequest($url, $params);   
        if ($response && isset($response['token']) && !empty($response['token'])){
            $this->tokenid = $response['token'];
        } elseif (!$this->checkAuthError($response)){
            $this->addError(t('Произошла неизвестная ошибка запроса.'));
        }
        return $response;
    }

    /**
     * Добавляет ошибку по коду. Возвращает false, если ошибки нет.
     * Возвращает true, если ошибка была
     *
     * @param array $response
     * @return bool
     */
    protected function checkAuthError($response)
    {
        if ($response && $response['code']) {
            switch ($response['code']) {
                case 17:
                    $this->addError(t('Неудалось авторизоваться. Некорректная ссылка наавторизацию'));
                    break;
                case 18:
                    $this->addError(t('Неудалось авторизоваться. Необходимо повторить запрос.'));
                    break;
                case 19:
                    $this->addError(t('Неудалось авторизоваться. Необходимо повторить запрос с корректными данными.'));
                    break;
                default:
                    $this->addError(t('Неудалось авторизоваться. Произошла неизвестная ошибка.'));
                    break;
            }
            return true;
        }
        return false;
    }
    
    /**
    * Делает запрос на авторизацию
    * 
    * @return boolean
    */
    function makeAuth()
    {
       $this->makeAuthRequest();
       return (!empty($this->tokenid)); 
    }
    
    /**
    * Возвращает часть url адреса для возврата.
    * 
    * @param string $operation - текущая операция
    *
    * @return string
    */
    private function getCorrectOperationAct($operation)
    {
        switch($operation){
            case "sell_refund":
                return "refund";
                break;
            case "sell_correction":
                return "correction";
                break;
            case "sell":
            default:
                return "sell";
                break; 
        }
    }
    
    /**
    * Возвращает URL для возврата для определённой операции
    * 
    * @param string $operation - нужная операция
    * @param string $sign - уникальная подпись транзакции
    *
    * @return string
    */
    protected function getCallbackUrl($operation, $sign)
    {
        return RouterManager::obj()->getUrl('shop-front-cashregister', [
            'CashRegisterType' => $this->getShortName(),
            'Act' => $this->getCorrectOperationAct($operation),
            'sign' => $sign
        ], true);
    }
    
    /**
    * Возвращает URL для авторизации
    * 
    */
    protected function getTokenUrl()
    {
        return $this->getApiUrl().static::OPERATION_AUTH;
    }

    /**
     * Возвращает url для нужной операции
     *
     * @param string $operation - нужная операция
     *
     * @return string
     * @throws \RS\Exception
     */
    protected function getOperationUrl($operation)
    {
        return $this->getApiUrl().$this->getOption('group_code')."/".$operation."?tokenid=".urlencode($this->tokenid);
    }

    /**
     * Возвращает url для получения информации по чеку
     *
     * @param string $uuid - уникальный идентификатор чека от провайдера
     *
     * @return string
     * @throws \RS\Exception
     */
    protected function getReportUrl($uuid)
    {
        return $this->getApiUrl().$this->getOption('group_code')."/report/".$uuid."?tokenid=".urlencode($this->tokenid);
    }   

    /**
    * Подготавливает телефон для экспорта
    * 
    * @param string $phone - телефон
    *
    * @return string
    */
    protected function preparePhone($phone)
    {
        if ($phone[0] == "8"){//Уберём восьмёрку из номера
            $phone  = ltrim($phone, "8");    
        }
        $search  = ["+7", "(", ")", "-", "_", "*", "[", "]", " "];
        $replace = ["", "", "", "", "", "", "", "", ""];
        $phone   = str_replace($search, $replace, $phone);
        
        return $phone;    
    }   
    
    /**
    * Генерирует уникальный идентификатор операции с чеком
    * 
    * @param integer $receipt_number - порядковый номер выбитого чека в рамках сессии
    *
    * @return string
    */
    protected function getReceiptExternalId($receipt_number = 0)
    {
        return sha1(\Setup::$SECRET_SALT.$this->transaction['id'].$receipt_number.time());
    }

    /**
     * Возвращает секцию данных о налогах
     *
     * @param string $tax_id - идентификатор налога
     * @return array
     */
    protected function getItemTaxData(string $tax_id)
    {
        return ['tax' => $tax_id];
    }
    
    /**
     * Возвращает стандартизированный объект информации о чеке
     *
     * @param Receipt $receipt Объект чека
     *
     * @return ReceiptInfo
     */
    public function getReceiptInfo(Receipt $receipt)
    {
        $receipt_info = new ReceiptInfo($receipt);

        $response_data = $receipt->getExtraInfo('success_info');
        $receipt_info->setFiscalReceiptNumber($response_data['fiscal_receipt_number']);
        $receipt_info->setShiftNumber($response_data['shift_number']);
        $receipt_info->setReceiptDatetime($response_data['receipt_datetime']);
        $receipt_info->setTotal($response_data['total']);
        $receipt_info->setFnNumber($response_data['fn_number']);
        $receipt_info->setEcrRegistrationNumber($response_data['ecr_registration_number']);
        $receipt_info->setFiscalDocumentNumber($response_data['fiscal_document_number']);
        $receipt_info->setFiscalDocumentAttribute($response_data['fiscal_document_attribute']);
        $receipt_info->setReceiptOftUrl($response_data['ofd_receipt_url']);

        $my_config = $this->getCashRegisterTypeConfig();
        if ($my_config->sno) {
            $receipt_info->setTaxMode($my_config->sno);
        }

        return $receipt_info;
    }
    
    /**
    * Добавляет в чек секцию со служебной информацией
    * 
    * @param array $receipt - чек для отправки
    * @param string $operation_type - тип операции
    * @param string $sign - подпись
    *
    * @return array
    */
    protected function getServicePart($receipt, $operation_type, $sign)
    {
        $receipt['document_type'] = $operation_type;

        $my_config = $this->getCashRegisterTypeConfig();
        $receipt['service']['inn']             = $this->getOption('inn'); //ИНН  
        $receipt['service']['callback_url']    = $this->getCallbackUrl($operation_type, $sign);
        $receipt['service']['payment_address'] = $my_config->domain ? $my_config->domain : $this->getCurrentDomainUrl();

        //Аттрибуты чека
        $sno = $this->getOption('sno', 0);
        if ($sno){
            $receipt['receipt']['attributes']['sno'] = $sno;
        }
        return $receipt;
    }
    
    
    /**
    * Добавляет ошибку при создании чека по её ответному коду
    * 
    * @param string $response - ответ сервера АТОЛ на опрерацию регистрации документа продажи, возврата
    * @return bool
    */
    protected function checkCreateReceiptError($response)
    {
        $error = '';
        if (isset($response['code']) && $response['code']) {
            switch ($response['code']) {
                case 1:
                    $error = t('Ошибка при парсинге JSON. Необходимо повторить запрос с корректными данными.');
                    break;
                case 2:
                    $error = t('Переданы пустые значения <group_code> и/или <operation>. Необходимо повторить запрос с корректными данными.');
                    break;
                case 3:
                    $error = t('Передано некорректное значение <operation>. Необходимо повторить запрос с корректными данными.');
                    break;
                case 4:
                    $error = t('Передан некорректный <tokenid>. Необходимо повторить запрос с корректными данными.');
                    break;
                case 5:
                    $error = t('Переданный <tokenid> не выдавался. Необходимо повторить запрос с корректными данными.');
                    break;
                case 6:
                    $error = t('Срок действия, переданного <tokenid> истёк (срок действия 24 часа). Необходимо запросить новый <tokenid>.');
                    break;
                case 10:
                    $error = t('Документ с переданными значениями <external_id> и <group_code> уже существует в базе. В ответе на ошибку будет передан UUID первого присланного чека с данными параметрами. Можно воспользоваться запросом на получение результат регистрации, указав UUID.');
                    break;
                default:
                    $error = t('Произошла неизвестная ошибка');
                    break;
            }

            $this->addError($error);
        }

        if (!empty($response['error'])){
            $error = $response['error']['text'];
            $this->addError($error);
        }

        return $error != '';
    }


    protected function getClientInfo($receipt, $user)
    {
        $receipt['receipt']['attributes']['email'] = $user['e_mail'];
        $receipt['receipt']['attributes']['phone'] = $this->preparePhone($user['phone']);

        return $receipt;
    }

    /**
     * Выполняет запрос на создание чека продажи или возврата
     *
     * @param array $receipt - объект чека
     * @param string $operation_type - тип чека
     * @throws RSException
     */
    protected function createReceiptRequest($receipt, $operation_type = 'sell')
    {
        if (!$this->hasError()) {
            //Отправим запрос   
            $response = $this->createRequest($this->getOperationUrl($operation_type), $receipt, [], true, 'POST');

            if (!$response) {
                $this->addError(t('Произошла неизвестная ошибка'));
            }

            $this->checkCreateReceiptError($response);
            
            //Запишем сведения о транзакции по порции чека
            $receipt_transaction                   = new \Shop\Model\Orm\Receipt();
            $receipt_transaction['sign']           = $receipt['external_id'];
            $receipt_transaction['type']           = $operation_type;
            $receipt_transaction['provider']       = $this->getShortName();
            $receipt_transaction['transaction_id'] = $this->transaction['id'];
            $receipt_transaction['total']          = $receipt['receipt']['total'];
            $receipt_transaction['error']          = (isset($response['error']) && $response['error']) ? $response['error'] : "";
            $receipt_transaction['answer']         = serialize($response);

            if (!$this->hasError()) {
                $receipt_transaction['uniq_id'] = $response['uuid'];
                $receipt_transaction['status'] = \Shop\Model\Orm\Receipt::STATUS_WAIT;
            } else {
                $receipt_transaction['status'] = \Shop\Model\Orm\Receipt::STATUS_FAIL;
                $receipt_transaction['error'] = $this->getErrorsStr();
            }
            $receipt_transaction->insert();
        }
    }


    /**
     * Обрабатывает дополнительную информацию о налогах и вписывает её в чек коррекции
     *
     * @param array $receipt
     * @param FormObject|array $data - объект с данными для чека коррекции
     * @return array
     */
    protected function getCorrectionTax($receipt, $data)
    {
        $receipt['correction']['tax'] = $data['tax'];

        return $receipt;
    }

    /**
     * Создаёт транзакцию на выставление чека коррекции в ОФД
     *
     * @param integer $transaction_id - id транзакции
     * @param \RS\Orm\FormObject|array $data - объект с данными для чека коррекции
     * @return boolean
     * @throws RSException
     */
    public function createCorrectionReceipt($transaction_id, $data){
        $this->makeAuth();
        if (!$this->hasError()){ //Если удалось авторизоваться
            $sum = $data['sum'];

            $sign = $this->getReceiptExternalId(); //Уникальная подпись
                
            $receipt['timestamp']     = date("d.m.Y H:i:s");
            $receipt['external_id']   = $sign;
            
            //Служебный раздел
            $receipt = $this->getServicePart($receipt, static::OPERATION_SELL_CORRECTION, $sign);
            
            //Коррекция
            $sno = $this->getOption('sno', 0);
            if ($sno){
                $receipt['correction']['attributes']['sno'] = $sno;    
            }

            //Тип оплаты
            $payment_type = [
                'sum' => (float)$sum,
                'type' => 1  //Всегда онлайн оплата
            ];
            $receipt['correction']['payments'] = [$payment_type];

            $receipt = $this->getCorrectionTax($receipt, $data);
            
            //Отправим запрос
            $response = $this->createRequest($this->getOperationUrl(static::OPERATION_SELL_CORRECTION), $receipt, [], true, 'POST');

            if (!$response){
                $this->addError(t('Произошла неизвестная ошибка'));
            }
            
            if (isset($response['code']) && $response['code']){
                $this->addCreateReceiptErrorByCode($response['code']);
            }

            $response_error = false;
            if (isset($response['error']) && $response['error']){
                $response_error = is_array($response['error']) ? var_export($response['error'], true) : $response['error'];
                $this->addError($response_error);
            }
            
            //Запишем сведения о транзакции по порции чека
            $receipt_transaction                   = new \Shop\Model\Orm\Receipt();
            $receipt_transaction['sign']           = $sign;                 
            $receipt_transaction['type']           = static::OPERATION_SELL_CORRECTION;                 
            $receipt_transaction['provider']       = $this->getShortName();                 
            $receipt_transaction['transaction_id'] = $transaction_id; 
            $receipt_transaction['total']          = (float)$sum; 
            $receipt_transaction['error']          = ($response_error) ? $response_error : "";
            $receipt_transaction['answer']         = serialize($response); 
            
            if (!$this->hasError()){
                $receipt_transaction['uniq_id'] = $response['uuid']; 
                $receipt_transaction['status']  = \Shop\Model\Orm\Receipt::STATUS_WAIT;  
            }else{
                $receipt_transaction['status']  = \Shop\Model\Orm\Receipt::STATUS_FAIL; 
                $receipt_transaction['error']   = $this->getErrorsStr(); 
            }  
            $receipt_transaction->insert();   
        }
        
        return (!$this->hasError()) ? true : false;
    }


    /**
     * Делает запрос на запрос статуса чека и возвращаетданные записывая их в чек, если произошли изменения
     *
     * @param \Shop\Model\Orm\Receipt $receipt - объект чека
     *
     * @return string|false
     * @throws \RS\Exception
     */
    public function getReceiptStatus(\Shop\Model\Orm\Receipt $receipt)
    {
        $this->makeAuth();
        if (!$this->hasError()){ //Если удалось авторизоваться
            $response = $this->createRequest($this->getReportUrl($receipt['uniq_id'])); 
            
            if (!$response){ //Если получить ответ не получилось, то делаем статус, что ещё ожидается ответ
                return 0;
            }  
            
            if (!empty($response['error']) && $response['status'] != "wait"){ //Если ошибок в чеке нет
                $this->addError($response['error']['text']);
                $receipt['status'] = \Shop\Model\Orm\Receipt::STATUS_FAIL;
                $receipt['error']  = $response['error']['text'];
                $receipt->update();
            }else{ //Если есть ошибки в чеке
                switch($response['status']){
                    case "wait": //Если чек ещё в статусе ожидаем
                        return 0;
                        break;
                    case "done": //Чек зарегистрирован
                        $receipt['status'] = \Shop\Model\Orm\Receipt::STATUS_SUCCESS;
                        $receipt->setExtraInfo('success_info', $response['payload']); //Сохраним данные чека
                        $receipt->update();
                        return true;
                        break;
                }
            }
        }
        return ($this->hasError()) ? $this->getErrorsStr() : true;
    }
    
    /**
    * Обрабавтывает результат обработки чека
    * 
    * @param \RS\Http\Request $url - объект текущего пришедшего запроса
    * @throws \RS\Exception
    *
    * @return string
    */
    public function onResult(\RS\Http\Request $url)
    {
        $sign = $url->request('sign', TYPE_STRING, "");
        
        //Поищем наш чек для изменения
        $receipt_api = new \Shop\Model\ReceiptApi();
        $receipt     = $receipt_api->getReceiptBySign($sign);
        
        if (!$receipt){
            throw new \RS\Exception(t('Чек с таким идентификатором не найден.'));
        }
        
        //Проверим статус чека, если он в статусе ожидание, то запросим состояние чека
        $this->getReceiptStatus($receipt);
        return "OK";
    }


    /**
     * Функция обработки запроса от провайдера чека продажи
     *
     * @param HttpRequest $url - объект запроса
     * @return string
     * @throws RSException
     */
    public function onResultSell(HttpRequest $url)
    {
        return $this->onResult($url);
    }

    /**
     * Функция обработки запроса от провайдера чека возврата
     *
     * @param HttpRequest $url - объект запроса
     * @return string
     * @throws RSException
     */
    public function onResultRefund(HttpRequest $url)
    {
        return $this->onResult($url);
    }

    /**
     * Функция обработки запроса от провайдера чека корректировки
     *
     * @param HttpRequest $url - объект запроса
     * @return string
     * @throws RSException
     */
    public function onResultCorrection(\RS\Http\Request $url)
    {
        return $this->onResult($url);
    } 
    
    
    /**
    * Добавляет сообщение об ошибке
    * 
    * @param string $message - сообщение об ошибке
    * @param string $fieldname - название поля
    * @param string $form - техническое имя поля (например, атрибут name у input)
    */
    public function addError($message, $fieldname = null, $form = null)
    {
        if ($this->log){
            $this->log->write('[Error]: '.$message, $this->log::LEVEL_OUT);
        }
        parent::addError($message);
    }

    /**
     * Возвращает объект формы чека коррекции
     *
     * @return \RS\Orm\FormObject | false Если false, то это означает, что кассовый модуль не поддерживает чеки коррекции
     */
    public function getCorrectionReceiptFormObject()
    {
        //Получаем объект для отображения формы
        return new \RS\Orm\FormObject(new PropertyIterator([
            'transaction_id' => new Type\Integer([
                'description' => t('ID транзакции'),
                'hint' => t('Транзакция, для которой делается чек корректировки')
            ]),
            'sum' => new Type\Varchar([
                'description' => t('Сумма корректировки'),
                'hint' => t('Только положительные числа'),
                'checker' => [function($orm, $value, $error) {
                    if ($value > 0) {
                        return true;
                    } else {
                        return $error;
                    }
                }, t('Сумма должна быть больше нуля')]
            ]),
            'tax' => new Type\Varchar([
                'description' => t('Налог'),
                'hint' => t('Указание налога для чека коррекции'),
                'list' => [[__CLASS__, 'getTaxesList']]
            ]),
        ]));
    }

    /**
     * Позволяет модифицировать данные по умолчанию для позиции в чеке
     *
     * @param array $item_data - данные позиции в чеке
     * @return void
     */
    protected function modifyReceiptItemData(array &$item_data)
    {
        $item_data['name'] = mb_substr($item_data['name'], 0, 64);
    }

    /**
     * Возвращает содержимое чека без списка товарных позиций
     *
     * @param array $receipt - уже имеющиеся данные
     * @param string $operation_type - тип операции
     * @param int $receipt_number - порядковый номер чека в группе
     * @return array
     */
    protected function addReceiptOtherData(array $receipt, string $operation_type, int $receipt_number)
    {
        $sign = $this->getReceiptExternalId($receipt_number);
        $user = $this->transaction->getUser();
        if (empty($user['e_mail']) && empty($user['phone'])) {
            $this->addError(t('Не указан E-mail или телефон пользователя'));
        }

        $result = [
            'external_id' => $sign,
            'timestamp' => date("d.m.Y H:i:s"),
            'receipt' => [
                'items' => $receipt['items'],
                'total' => $receipt['total'],
            ],
        ];

        $result = $this->getServicePart($result, $operation_type, $sign);
        $result = $this->getClientInfo($result, $user);

        $payment_type_object = $this->transaction->getOrder()->getPayment()->getTypeObject();
        if (!$this->transaction['order_id']) {
            $payment_type_value = ($this->transaction['sum'] < 0) ? self::PAYMENT_TYPE_FROM_ADVANCE : self::PAYMENT_TYPE_CACHLESS;
        } elseif ($this->transaction['entity'] == Transaction::ENTITY_SHIPMENT) {
            $payment_type_value = static::PAYMENT_TYPE_FROM_ADVANCE;
        } else {
            if ($payment_type_object instanceof PersonalAccount && $this->config['personal_account_payment_method'] == CashRegisterApi::PAYMENT_METHOD_ADVANCE) {
                $payment_type_value = static::PAYMENT_TYPE_FROM_ADVANCE ; //предварительная оплата (зачет аванса и (или) предыдущих платежей);
            } else {
                $payment_type_value = static::PAYMENT_TYPE_CACHLESS; //Безналичный платеж
            }
        }

        $result['receipt']['payments'] = [
            [
                'sum' => $receipt['total'],
                'type' => $payment_type_value,
            ]
        ];

        return $result;
    }
}
