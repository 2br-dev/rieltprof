<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Telephony;

use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\Telephony\Provider\AbstractProvider;
use Users\Model\Api as UserApi;

/**
 * Класс описывает стандартизированный объект события от телефонии.
 * Такой объект ожидается от любого провайдера, после обработки входящего запроса от телефонии.
 */
class CallEvent
{
    private $provider;
    private $eventType;
    private $callID;
    private $callerNumber;
    private $callerId;
    private $calledNumber;
    private $calledId;
    private $callStatus;
    private $callSubStatus;
    private $callFlow;
    private $subCallID;
    private $recID;
    private $duration;
    private $callAPIID;
    private $eventTime;
    private $calledDID;
    private $data = [];
    private $returnData;

    /**
     * Конструктор
     *
     * @param AbstractProvider $provider
     */
    function __construct(AbstractProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Возвращает провайдера телефонии
     *
     * @return AbstractProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Возвращает тип события. Может принимать значния dial-in, dial-out, hangup или answer в зависимости от типа события.
     *
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * Устанавливает тип события
     *
     * @param string $eventType
     * @return self
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
        return $this;
    }

    /**
     * Возвращает ID вызова
     *
     * @return string
     */
    public function getCallID()
    {
        return $this->callID;
    }

    /**
     * Устанавливает ID вызова
     *
     * @param string $callID
     * @return self
     */
    public function setCallID($callID)
    {
        $this->callID = $callID;
        return $this;
    }

    /**
     * Возвращает номер вызывающего абонента в нормализованом виде
     *
     * @return string
     */
    public function getCallerNumber()
    {
        return $this->callerNumber;
    }

    /**
     * Устанавливает номер вызывающего абонента
     *
     * @param string $callerNumber
     * @return self
     */
    public function setCallerNumber($callerNumber)
    {
        $this->callerNumber = UserApi::normalizePhoneNumber($callerNumber);
        return $this;
    }

    /**
     * Возвращает номер вызываемого абонента
     *
     * @return string
     */
    public function getCalledNumber()
    {
        return $this->calledNumber;
    }

    /**
     * Устанавливает номер вызываемого абонента
     *
     * @param string $calledNumber
     * @return self
     */
    public function setCalledNumber($calledNumber)
    {
        $this->calledNumber = UserApi::normalizePhoneNumber($calledNumber);
        return $this;
    }

    /**
     * Возвращает статус звонка
     *
     * @return string
     */
    public function getCallStatus()
    {
        if ($this->callStatus === null) {
            $result = null;
            switch($this->getEventType()) {
                case AbstractProvider::EVENT_TYPE_DIAL_IN:
                case AbstractProvider::EVENT_TYPE_DIAL_OUT:
                    $result = CallHistory::CALL_STATUS_CALLING;
                    break;
                case AbstractProvider::EVENT_TYPE_ANSWER:
                    $result = CallHistory::CALL_STATUS_ANSWER;
                    break;
                case AbstractProvider::EVENT_TYPE_HANGOUT:
                    $result = CallHistory::CALL_STATUS_HANGUP;
                    break;
            }

            return $result;
        }
        return $this->callStatus;
    }

    /**
     * Устанавливает статус звонка (Идет звонок, идет разговор, разговор завершен)
     *
     * @param string $callStatus
     * @return self
     */
    public function setCallStatus($callStatus)
    {
        $this->callStatus = $callStatus;
        return $this;
    }

    /**
     * Возвращает направление звонка
     *
     * @return string
     */
    public function getCallFlow()
    {
        return $this->callFlow;
    }

    /**
     * Устанавливает направление звонка
     *
     * @param string $callFlow
     * @return self
     */
    public function setCallFlow($callFlow)
    {
        $this->callFlow = $callFlow;
        return $this;
    }

    /**
     * Возвращает ID звонка с учетом переадресации
     *
     * @return string
     */
    public function getSubCallID()
    {
        return $this->subCallID;
    }

    /**
     * Устанавливает ID звонка с учетом переадресации
     *
     * @param string $subCallID
     * @return self
     */
    public function setSubCallID($subCallID)
    {
        $this->subCallID = $subCallID;
        return $this;
    }

    /**
     * Возвращает ID файла с записью разговора
     *
     * @return string
     */
    public function getRecID()
    {
        return $this->recID;
    }

    /**
     * Устанавливает ID файла с записью разговора
     *
     * @param string $recID
     * @return self
     */
    public function setRecID($recID)
    {
        $this->recID = $recID;
        return $this;
    }

    /**
     * Возвращает длительность разговора, в микросекундах
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Устанавливает длительность разговора, в микросекундах
     *
     * @param int $duration
     * @return self
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Возвращает содержимое SIP заголовка "Client-Api-Id"
     *
     * @return string
     */
    public function getCallAPIID()
    {
        return $this->callAPIID;
    }

    /**
     * Устанавливает содержимое SIP заголовка "Client-Api-Id"
     *
     * @param string $callAPIID
     * @return self
     */
    public function setCallAPIID($callAPIID)
    {
        $this->callAPIID = $callAPIID;
        return $this;
    }

    /**
     * Возвращает время события в формате YYYY-MM-DD HH:MM:SS
     *
     * @return string
     */
    public function getEventTime()
    {
        return $this->eventTime;
    }

    /**
     * Устанваливает время события в формате YYYY-MM-DD HH:MM:SS
     *
     * @param string $eventTime
     * @return self
     */
    public function setEventTime($eventTime)
    {
        $this->eventTime = $eventTime;
        return $this;
    }

    /**
     * Возвращает дополнительные произвольные сведения
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Устанавливает дополнительные произвольные сведения
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Возвращает публичный номер вызываемого абонента (если есть)
     *
     * @return string
     */
    public function getCalledDID()
    {
        return $this->calledDID;
    }

    /**
     * Устанавливает публичный номер вызываемого абонента (если есть)
     *
     * @param string $calledDID
     * @return self
     */
    public function setCalledDID($calledDID)
    {
        $this->calledDID = $calledDID;
        return $this;
    }

    /**
     * Возвращает данные, которые необходимо вернуть в ответ на запрос
     *
     * @return string
     */
    public function getReturnData()
    {
        return $this->returnData;
    }

    /**
     * Устанавливает данные, которые необходимо вернуть в ответ на запрос
     *
     * @param string $returnData
     * @return self
     */
    public function setReturnData($returnData)
    {
        $this->returnData = $returnData;
        return $this;
    }

    /**
     * Возвращает статус звонка на момент завершения разговора
     *
     * @return mixed
     */
    public function getCallSubStatus()
    {
        return $this->callSubStatus;
    }

    /**
     * Устанавливает статус звонка на момент завершения разговора
     *
     * @param string $callSubStatus
     * @return self
     */
    public function setCallSubStatus($callSubStatus)
    {
        $this->callSubStatus = $callSubStatus;
        return $this;
    }


    /**
     * Возвращает ID звонка вместе с провайдером
     *
     * @return string
     */
    public function getCallIdWithProvider()
    {
        return $this->getProvider()->getId().'-'.$this->getCallID();
    }

    /**
     * Возвращает ID звонящего в телефонии
     *
     * @return mixed
     */
    public function getCallerId()
    {
        return $this->callerId;
    }

    /**
     * Устанавливает ID звонящего в телефонии
     *
     * @param mixed $callerId
     * @return self
     */
    public function setCallerId($callerId)
    {
        $this->callerId = $callerId;
        return $this;
    }

    /**
     * Возвращает ID вызываемого в телефонии
     *
     * @return mixed
     */
    public function getCalledId()
    {
        return $this->calledId;
    }

    /**
     * Устанавливает ID вызываемого в телефонии
     *
     * @param mixed $calledId
     * @return self
     */
    public function setCalledId($calledId)
    {
        $this->calledId = $calledId;
        return $this;
    }
}