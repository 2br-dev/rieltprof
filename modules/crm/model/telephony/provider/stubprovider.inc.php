<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Telephony\Provider;

use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\Telephony\Requester;
use RS\Exception;
use RS\Http\Request;

/**
 * Заглушка для удаленного провайдера телефонии
 */
class StubProvider extends AbstractProvider
{
    private $deleted_provider_id;

    public function __construct($deleted_provider_id)
    {
        $this->deleted_provider_id = $deleted_provider_id;
    }

    /**
     * Возвращает название провайдера телефонии
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Провайдер удален (%id)', ['id' => $this->deleted_provider_id]);
    }

    /**
     * Возвращает внутренний строковый идентификатор провайдера связи
     *
     * @return string
     */
    public function getId()
    {
        return $this->deleted_provider_id;
    }

    /**
     * Обрабатывает входящий запрос с событием от сервиса телефонии
     *
     * @param Request $url
     * @return CallEvent
     */
    public function onEvent(Request $url)
    {
        throw new Exception(t('Провайдер %id был удален', ['id' => $this->deleted_provider_id]));
    }

    /**
     * Возвращает объект, который описывает тесты
     * для данного провайдера.
     *
     * @return AbstractProviderTest
     */
    function getEventTestObject()
    {
        return null;
    }

    /**
     * Возвращает список действий, который можно произвести со звонком
     * в зависимости от статуса звонка
     *
     * @param CallHistory $call
     * @return array
     */
    public function getActionsByCall(CallHistory $call)
    {
        return [];
    }

    /**
     * Возвращает добавочный номер для администратора user_id, если таковой задан. Иначе - false
     *
     * @param integer $user_id
     * @return integer|bool(false)
     */
    public function getExtensionIdByUserId($user_id)
    {
        return false;
    }

    /**
     * Возвращает ID пользователя по добавочному номеру
     *
     * @param $extension_id
     * @return mixed
     */
    public function getUserIdByExtensionId($extension_id)
    {
        return false;
    }

    /**
     * Получает авторизационный токен и устанавливает его в Requester
     *
     * @param Requester $requester
     * @param bool $force
     * @return mixed
     */
    public function authorizeRequester(Requester $requester, $force = false)
    {}

    /**
     * Возвращает последний полученный AccessToken. Если force=true, то происходит принудительная переполучение токена
     *
     * @param bool $force
     * @return string | bool(false)
     */
    public function getAccessToken($params = [], $force = false)
    {
        return false;
    }

    /**
     * Возвращает true, если заполнены все данные для проведения исходящих запросов к API
     *
     * @return bool
     */
    public function canApiRequest()
    {
        return false;
    }

    /**
     * Возвращает true, если включена автоматическая загрузка записей разговоров после отбоя
     *
     * @return bool
     * @throws \RS\Exception
     */
    public function isEnableAutoDownloadRecord()
    {
        return false;
    }

    /**
     * Возвращает путь записи на локальном диске. Или false - в случае, если провайдер
     * не поддерживает работу с записями
     *
     * @param CallHistory $call
     * @return string|bool(false)
     */
    function getRecordDataLocalPath(CallHistory $call)
    {
        return false;
    }

    /**
     * Возвращает true, если запись разговора присутствует локально
     *
     * @param CallHistory $call
     * @return bool
     */
    function issetRecordLocal(CallHistory $call)
    {
        return false;
    }

    /**
     * Производит попытку загрузки записи на локальный диск
     *
     * @param CallHistory $call
     * @return bool
     */
    public function downloadRecord(CallHistory $call)
    {
        return false;
    }

    /**
     * Возвращает содержимое файла записи телефонного разговора
     *
     * @param CallHistory $call
     * @param bool $find_local
     * @return bool
     */
    function getRecordData(CallHistory $call, $find_local = true)
    {
        return false;
    }

    /**
     * Возвращает Mime тип аудиозаписи
     *
     * @return string
     */
    public function getRecordContentType()
    {
        return '';
    }

    /**
     * Возвращает true, если телфония поддерживает исходящие звонки
     *
     * @return bool
     */
    public function canCalling()
    {
        return false;
    }

    /**
     * Отправляет запрос на исходящий вызов
     *
     * @param $number
     * @return bool
     */
    public function CallPhoneNumber($number)
    {
        return false;
    }

    /**
     * Возвращает true, если удается определить, что это внутренний вызов между сотрудниками телефонии.
     * Такие вызовы должны игнорироваться и не регистрироваться в административной панели
     *
     * @param CallHistory $call Здесь будет объект звонка, который еще не присутствует в базе (ID = null)
     * @return mixed
     */
    public function isInternalCall(CallHistory $call)
    {
        return false;
    }
}