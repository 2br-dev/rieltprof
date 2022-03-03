<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Telephony\Provider;

use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\Telephony\CallEvent;
use Crm\Model\Telephony\Requester;
use RS\Config\Loader;
use RS\Http\Request;
use RS\Router\Manager;
use RS\View\Engine;

/**
 * Базовый класс провайдера услуг телефонии
 */
abstract class AbstractProvider
{
    protected $settings_info_template = '';
    protected $last_error;
    private $url_secret;

    const EVENT_TYPE_DIAL_IN = 'dial-in';
    const EVENT_TYPE_DIAL_OUT = 'dial-out';
    const EVENT_TYPE_ANSWER = 'answer';
    const EVENT_TYPE_HANGOUT = 'hangup';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * Возвращает название провайдера телефонии
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Возвращает внутренний строковый идентификатор провайдера связи
     *
     * @return string
     */
    abstract public function getId();

    /**
     * Обрабатывает входящий запрос с событием от сервиса телефонии
     *
     * @param Request $url
     * @return CallEvent
     */
    abstract public function onEvent(Request $url);

    /**
     * Устанавливает секретный ключ, который будет использоваться в URL для событий
     *
     * @param $secret
     */
    public function setUrlSecret($secret)
    {
        $this->url_secret = $secret;
    }

    /**
     * Возвращает секретный ключ, который будет использоваться в URL для событий
     *
     * @return string
     */
    public function getUrlSecret()
    {
        if ($this->url_secret === null) {
            return Loader::byModule($this)->tel_secret_key;
        }

        return $this->url_secret;
    }

    /**
     * Возвращает URL обработчика события
     *
     * @param string $event_type Зарезервировано для различных типов событий
     * @return string
     */
    public function getEventGateUrl($event_type = null)
    {
        return Manager::obj()->getUrl('crm-front-telephonyevents', [
            'secret' => $this->getUrlSecret(),
            'provider' => $this->getId()
        ], true);
    }

    /**
     * Возвращает поддерживаемые типы входящих событий
     *
     * @return array
     */
    public function getAllowEventTypes()
    {
        return [
            self::EVENT_TYPE_DIAL_IN => self::METHOD_POST,
            self::EVENT_TYPE_DIAL_OUT => self::METHOD_POST,
            self::EVENT_TYPE_ANSWER => self::METHOD_POST,
            self::EVENT_TYPE_HANGOUT => self::METHOD_POST
        ];
    }

    /**
     * Возвращает объект, который описывает тесты
     * для данного провайдера.
     *
     * @return AbstractProviderTest
     */
    abstract public function getEventTestObject();

    /**
     * Возвращает список действий, который можно произвести со звонком
     * в зависимости от статуса звонка
     *
     * @param CallHistory $call
     * @return array
     * [
     *  ['text' => 'ТЕКСТ КНОПКИ','attr' => ['data-url' => '', АТТРИБУТЫ...]],
     *  ['text' => 'ТЕКСТ КНОПКИ','attr' => ['data-url' => '', АТТРИБУТЫ...]],
     * ]
     *
     * data-url - может содержать ссылку на контроллер действий звонков crm-callactions, с GET параметрами:
     * - call_id ID звонка
     * - call_action имя действия. В этом случае должен присутствовать метод do{ИМЯ_ДЕЙСТВИЯ} в классе провайдера
     */
    abstract public function getActionsByCall(CallHistory $call);

    /**
     * Возвращает добавочный номер для администратора user_id, если таковой задан. Иначе - false
     *
     * @param integer $user_id
     * @return integer|bool(false)
     */
    abstract public function getExtensionIdByUserId($user_id);

    /**
     * Возвращает ID пользователя по добавочному номеру
     *
     * @param $extension_id
     * @return mixed
     */
    abstract public function getUserIdByExtensionId($extension_id);


    /**
     * Возвращает HTML со сведениями о настройке телефонии
     *
     * @return string
     */
    public function getConnectSettingsInfo()
    {
        if ($this->settings_info_template) {
            $view = new Engine();
            $view->assign([
                'provider' => $this
            ]);

            return $view->fetch($this->settings_info_template);
        }

        return '';
    }


    /**
     * Возвращает объект для запросов, сразу инициализированный функцией
     *
     * @return Requester
     */
    public function getRequester()
    {
        $requester = new Requester($this);
        $requester->setAuthorizeCallback([$this, 'authorizeRequester']);

        return $requester;
    }

    /**
     * Получает авторизационный токен и устанавливает его в Requester
     *
     * @param Requester $requester
     * @param bool $force
     * @return mixed
     */
    abstract public function authorizeRequester(Requester $requester, $force = false);


    /**
     * Возвращает последний полученный AccessToken. Если force=true, то происходит принудительная переполучение токена
     *
     * @param bool $force
     * @return string | bool(false)
     */
    abstract public function getAccessToken($params = [], $force = false);

    /**
     * Возвращает true, если заполнены все данные для проведения исходящих запросов к API
     *
     * @return bool
     */
    abstract public function canApiRequest();

    /**
     * Возвращает true, если включена автоматическая загрузка записей разговоров после отбоя
     *
     * @return bool
     * @throws \RS\Exception
     */
    abstract public function isEnableAutoDownloadRecord();

    /**
     * Возвращает путь записи на локальном диске. Или false - в случае, если провайдер
     * не поддерживает работу с записями
     *
     * @param CallHistory $call
     * @return string|bool(false)
     */
    abstract function getRecordDataLocalPath(CallHistory $call);


    /**
     * Возвращает true, если запись разговора присутствует локально
     *
     * @param CallHistory $call
     * @return bool
     */
    abstract function issetRecordLocal(CallHistory $call);


    /**
     * Производит попытку загрузки записи на локальный диск
     *
     * @param CallHistory $call
     * @return bool
     */
    abstract public function downloadRecord(CallHistory $call);

    /**
     * Возвращает содержимое файла записи телефонного разговора
     *
     * @param CallHistory $call
     * @param bool $find_local
     * @return bool
     */
    abstract function getRecordData(CallHistory $call, $find_local = true);

    /**
     * Возвращает Mime тип аудиозаписи
     *
     * @return string
     */
    abstract public function getRecordContentType();


    /**
     * Возвращает true, если телфония поддерживает исходящие звонки
     *
     * @return bool
     */
    abstract public function canCalling();

    /**
     * Отправляет запрос на исходящий вызов
     *
     * @param $number
     * @return bool
     */
    abstract public function CallPhoneNumber($number);

    /**
     * Возвращает true, если удается определить, что это внутренний вызов между сотрудниками телефонии.
     * Такие вызовы должны игнорироваться и не регистрироваться в административной панели
     *
     * @param CallHistory $call Здесь будет объект звонка, который еще не присутствует в базе (ID = null)
     * @return mixed
     */
    abstract public function isInternalCall(CallHistory $call);


    /**
     * Устанавливает ошибку
     *
     * @param $error
     * @return bool(false)
     */
    public function setError($error)
    {
        $this->last_error = $error;
        return false;
    }

    /**
     * Возвращает последнюю ошибку
     *
     * @return string
     */
    public function getLastError()
    {
        return $this->last_error;
    }

    /**
     * Возвращает базовый абсолютный путь к папке с записями
     *
     * @return string
     */
    public static function getRecordLocalBaseDir()
    {
        return \Setup::$ROOT.\Setup::$STORAGE_DIR.'/records';
    }
}