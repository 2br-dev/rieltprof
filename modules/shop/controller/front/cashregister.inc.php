<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use RS\Controller\Front;
use Shop\Model\CashRegisterApi;
use Shop\Model\Log\LogCashRegister;

/**
* Контроллер для обработки чеков ККТ от онлайн касс
*/
class CashRegister extends Front
{
    /** @var \Shop\Model\CashRegisterType\AbstractType */
    protected $provider; //Провайдер обработки чеков

    protected $log;
    protected $log_file;

    /**
     * Инициализация перед обработкой запроса
     */
    function init()
    {
        $this->log = LogCashRegister::getInstance();
        $log_text = 'Start action ' . $this->getAction() . "\n";
        $log_text .= 'Request url: ' . $this->url->getSelfUrl() . "\n";
        $log_text .= 'Request data: ' . var_export($_REQUEST, true);
        $this->log->write($log_text, LogCashRegister::LEVEL_IN);

        $this->wrapOutput(false);
        $cash_register_type = $this->request('CashRegisterType', TYPE_STRING, null);
        if (!$cash_register_type) { //Если пришёл запрос без указания провайдера
            $error = t('Не указан провайдер для онлайн касс');
            $this->log->write("-------- ERROR --------\n" . $error, $this->log::LEVEL_IN);
            throw new \RS\Exception($error);
        }

        try {
            $cash_register_api = new CashRegisterApi();
            $this->provider = $cash_register_api->getTypeByShortName($cash_register_type);
        } catch (\Exception $e) {
            $this->throwInError($e);
        }
    }
    
    /**
    * Особый action, который вызвается с сервера online касс
    * В REQUEST['sign'] должен содержаться строковый идентификатор чека
    * 
    * http://САЙТ.РУ/cashregister/{CashRegisterType}/{Act}/
    */
    function actionSell()
    {
        ob_start(); //Чтобы собрать все notice'ы, если они есть сохраняем буфер
        try{ 
            $response = $this->provider->onResultSell($this->url);
        }
        catch(\Exception $e){
            $this->throwInError($e);
        }

        if ($this->log) {
            $this->log->write('Your response: ' . ob_get_contents() . $response, LogCashRegister::LEVEL_IN);
        }
        ob_end_flush();
        return $response;
    }
    
    /**
    * Особый action, который вызвается с сервера online касс
    * В REQUEST['sign'] должен содержаться строковый идентификатор чека
    * 
    * http://САЙТ.РУ/cashregister/{CashRegisterType}/{Act}/
    */
    function actionRefund()
    {
        ob_start(); //Чтобы собрать все notice'ы, если они есть сохраняем буфер
        try{ 
            $response = $this->provider->onResultRefund($this->url);
        }
        catch(\Exception $e){
            $this->throwInError($e);
        }
        
        if ($this->log) {
            $this->log->write('Your response: ' . ob_get_contents() . $response, LogCashRegister::LEVEL_IN);
        }
        ob_end_flush();
        return $response;
    }
    
    /**
    * Особый action, который вызвается с сервера online касс
    * В REQUEST['sign'] должен содержаться строковый идентификатор чека
    * 
    * http://САЙТ.РУ/cashregister/{CashRegisterType}/{Act}/
    */
    function actionCorrection()
    {
        ob_start(); //Чтобы собрать все notice'ы, если они есть сохраняем буфер
        try{ 
            $response = $this->provider->onResultCorrection($this->url);
        }
        catch(\Exception $e){
            $this->throwInError($e);
        }
        
        if ($this->log) {
            $this->log->write('Your response: ' . ob_get_contents() . $response, LogCashRegister::LEVEL_IN);
        }
        ob_end_flush();
        return $response;
    }
    
    /**
    * Функция бросает исключение и если нужно записывает в лог
    * 
    * @param \Exception $e - объект исключения
    */
    private function throwInError(\Exception $e)
    {
        if ($this->log){
            $log_text = "-------- ERROR --------\n";
            $log_text .= 'Exception message:' . $e->getMessage() . "\n";
            $log_text .= 'Exception code:' . $e->getCode() . "\n";
            $log_text .= 'Exception file:' . $e->getFile() . "\n";
            $log_text .= 'Exception line:' . $e->getLine() . "\n";
            $log_text .= 'Exception StackTrace:' . $e->getTraceAsString();
            $this->log->write($log_text, LogCashRegister::LEVEL_IN);
        }
        throw $e;
    }
}