<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm\Telephony;

use Crm\Config\ModuleRights;
use Crm\Model\Telephony\CallEvent;
use Crm\Model\Telephony\Manager;
use Crm\Model\Telephony\Provider\AbstractProvider;
use Crm\Model\Telephony\Provider\StubProvider;
use Crm\Model\Utils;
use Main\Model\Comet\LongPolling;
use RS\Application\Auth;
use RS\Exception;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use RS\View\Engine;
use Users\Model\Api as UserApi;
use Users\Model\Orm\User;

/**
 * История звонков
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $provider Провайдер тефонных услуг
 * @property string $call_id Внутренний ID вызова
 * @property string $call_api_id Внешний ID вызова
 * @property string $caller_number Номер вызывающего абонента
 * @property string $caller_id ID вызывающего абонента
 * @property string $called_number Номер вызываемого абонента
 * @property string $called_id ID вызываемого абонента
 * @property string $called_public_number Публичный номер на который звонит абонент
 * @property string $event_time Дата и время звонка
 * @property integer $duration Время разговора, в микросекундах
 * @property string $record_id ID файла записи разговора
 * @property string $call_status Статус звонка
 * @property string $call_sub_status Результат звонка
 * @property string $call_flow Направление вызова
 * @property array $custom_data Произвольные данные
 * @property string $_custom_data Произвольные данные
 * @property integer $send_to_browser Уведомить пользователя о событии в браузер
 * @property integer $is_closed Звонок принудительно закрыт пользователем
 * --\--
 */
class CallHistory extends OrmObject
{
    /** идет звонок */
    const CALL_STATUS_CALLING = 'CALLING';

    /** идет разговор */
    const CALL_STATUS_ANSWER = 'ANSWER';

    /** вызов завершен */
    const CALL_STATUS_HANGUP = 'HANGUP';


    /** вызов был отвечен  */
    const CALL_SUBSTATUS_ANSWER = 'ANSWER';

    /** вызов получил сигнал "занято" */
    const CALL_SUBSTATUS_BUSY = 'BUSY';

    /** звонок не отвечен (истек таймер ожидания на сервере) */
    const CALL_SUBSTATUS_NOANSWER = 'NOANSWER';

    /** звонящий отменил вызов до истечения таймера ожидания на сервере */
    const CALL_SUBSTATUS_CANCEL = 'CANCEL';

    /** произошла ошибка во время вызова */
    const CALL_SUBSTATUS_CONGESTION = 'CONGESTION';

    /** у вызываемого абонента отсутствует регистрация */
    const CALL_SUBSTATUS_CHANUNAVAIL = 'CHANUNAVAIL';

    /** Направление звонка - входящий */
    const CALL_FLOW_IN = 'in';

    /** Направление звонка - исходящий */
    const CALL_FLOW_OUT = 'out';

    protected static $table = 'crm_tel_call_history';

    /** Сопоставление статусов с иконками zmdi */
    protected static $icon_map = [
        self::CALL_STATUS_CALLING => 'phone-ring',
        self::CALL_STATUS_ANSWER => 'phone-in-talk',
        self::CALL_STATUS_HANGUP => 'phone-end',
    ];

    function _init()
    {
        parent::_init()->append([
            'provider' => new Type\Varchar([
                'description' => t('Провайдер тефонных услуг'),
                'visible' => false,
            ]),
            'call_id' => new Type\Varchar([
                'description' => t('Внутренний ID вызова'),
                'visible' => false,
            ]),
            'call_api_id' => new Type\Varchar([
                'description' => t('Внешний ID вызова'),
                'visible' => false,
            ]),
            'caller_number' => new Type\Varchar([
                'description' => t('Номер вызывающего абонента')
            ]),
            'caller_id' => new Type\Varchar([
                'description' => t('ID вызывающего абонента'),
                'visible' => false,
            ]),
            'called_number' => new Type\Varchar([
                'description' => t('Номер вызываемого абонента')
            ]),
            'called_id' => new Type\Varchar([
                'description' => t('ID вызываемого абонента'),
                'visible' => false,
            ]),
            'called_public_number' => new Type\Varchar([
                'description' => t('Публичный номер на который звонит абонент')
            ]),
            'event_time' => new Type\DateTime([
                'description' => t('Дата и время звонка'),
                'visible' => false,
            ]),
            'duration' => new Type\Bigint([
                'description' => t('Время разговора, в микросекундах'),
                'visible' => false,
            ]),
            'record_id' => new Type\Varchar([
                'description' => t('ID файла записи разговора'),
                'allowEmpty' => false,
                'visible' => false
            ]),
            'call_status' => new Type\Varchar([
                'description' => t('Статус звонка'),
                'listFromArray' => [self::getCallStatuses()]
            ]),
            'call_sub_status' => new Type\Varchar([
                'description' => t('Результат звонка'),
                'listFromArray' => [self::getCallSubStatuses()]
            ]),
            'call_flow' => new Type\Enum(array_keys(self::getCallFlows()), [
                'description' => t('Направление вызова'),
                'listFromArray' => [self::getCallFlows()]
            ]),
            'custom_data' => new Type\ArrayList([
                'description' => t('Произвольные данные'),
                'visible' => false,
            ]),
            '_custom_data' => new Type\Text([
                'description' => t('Произвольные данные'),
                'visible' => false
            ]),
            'send_to_browser' => new Type\Integer([
                'runtime' => true,
                'description' => t('Уведомить пользователя о событии в браузер')
            ]),
            'is_closed' => new Type\Integer([
                'description' => t('Звонок принудительно закрыт пользователем'),
                'allowEmpty' => false,
                'listFromArray' => [[
                    0 => t('Нет'),
                    1 => t('Да')
                ]]
            ])
        ]);

        $this->addIndex(['call_id'], self::INDEX_UNIQUE);
        $this->addIndex(['caller_number'], self::INDEX_KEY);
        $this->addIndex(['called_number'], self::INDEX_KEY);
        $this->addIndex(['record_id'], self::INDEX_KEY);
    }


    /**
     * Обработчик
     *
     * @param string $save_flag
     */
    public function afterWrite($save_flag)
    {
        $call_admin_user = $this->getAdminUser();

        if ($this['send_to_browser'] && $call_admin_user->id) {
            $message = $this->buildMessage();

            if ($call_admin_user->id) {
                LongPolling::getInstance()->pushEvent('crm.telephony.event', $message, $call_admin_user->id, 60);
            }
        }

        $provider = $this->getProvider();
        if ($this['call_status'] == self::CALL_STATUS_HANGUP
            && $this['record_id']
            && $provider->isEnableAutoDownloadRecord()
            && !$provider->issetRecordLocal($this))
        {

            $provider->downloadRecord($this);
        }
    }

    /**
     * Создает сообщение, которое необходимо отправить
     */
    public function buildMessage()
    {
        $user = $this->getOtherUser();
        $username = $user['id'] ? implode(' ', [$user['surname'], $user['name']]) : $user['phone'];

        $message = [
            'id' => $this['call_id'],
            'username' => $username
        ];

        if ($this->needShowCallWindow()) {
            $message['html'] = $this->getNoticeHtml();
        } else {
            //Отправляем команду на закрытие окна
            $message['closeCall'] = true;
        }

        return $message;
    }

    /**
     * Возвращает true, если необходимо отобразить всплывающее окно
     */
    public function needShowCallWindow()
    {
        if (in_array($this['call_status'], [self::CALL_STATUS_CALLING, self::CALL_STATUS_ANSWER])) {
            return true;
        }

        return false;
    }

    /**
     * Возвращает в случае входящего звонка пользователя администратора, которому звонят,
     * а в случае исходящего звонка администратора, который звонит.
     *
     * @return User
     */
    public function getAdminUser()
    {
        if ($this['call_flow'] == self::CALL_FLOW_IN) {
            return $this->getCalledUser();
        } else {
            return $this->getCallerUser();
        }
    }

    /**
     * Возвращает добавочный номер пользователя, которому звонят - при входящем звонке,
     * а при исходящем - добавочный номер пользователя, который совершает вызов
     *
     * @return integer
     */
    public function getAdminExtensionId()
    {
        if ($this['call_flow'] == CallHistory::CALL_FLOW_IN) {
            return $this['called_id'];
        } else {
            return $this['caller_id'];
        }
    }

    /**
     * Возвращает в случае входящего звонка пользователя, звонящего,
     * а в случае исходящего звонка пользователя, которому звонят
     *
     * @return User
     */
    public function getOtherUser()
    {
        if ($this['call_flow'] == self::CALL_FLOW_IN) {
            return $this->getCallerUser();
        } else {
            return $this->getCalledUser();
        }
    }

    /**
     * Возвращает готовый HTML для всплывающего окна
     *
     * @return string
     */
    public function getNoticeHtml()
    {
        $view = new Engine();
        $view->assign([
            'call_history' => $this
        ]);

        return $view->fetch('%crm%/telephony/notice_body.tpl');
    }

    /**
     * Возвращает объект провайдера телефонии, через который данное событие создано
     *
     * @return AbstractProvider
     */
    public function getProvider()
    {
        try {
            $provider = Manager::getProviderById($this['provider']);
            return $provider;
        } catch (Exception $e) {
            return new StubProvider($this['provider']);
        }
    }

    /**
     * Возвращает объект вызывающего пользователя
     *
     * @return User
     */
    public function getCallerUser()
    {
        if ($this['call_flow'] == self::CALL_FLOW_IN) {
            return $this->getUserByPhone($this['caller_number']);
        } else {
            return $this->getUserByExtensionId($this['caller_number']);
        }
    }

    /**
     * Возвращает вызываемого пользователя
     *
     * @return User
     */
    public function getCalledUser()
    {
        if ($this['call_flow'] == self::CALL_FLOW_OUT) {
            return $this->getUserByPhone($this['called_number']);
        } else {
            return $this->getUserByExtensionId($this['called_number']);
        }
    }

    /**
     * Возвращает найденного пользователя по номеру телефона
     *
     * @param $phone
     * @return User
     * @throws \RS\Orm\Exception
     */
    private function getUserByPhone($phone)
    {
        $phone_normilized = UserApi::normalizePhoneNumber($phone);
        $user = User::loadByWhere([
            'phone' => $phone_normilized
        ]);

        if (!$user['id']) {
            $user['name'] = t('Неизвестный пользователь');
            $user['phone'] = $phone;
        }

        return $user;
    }

    /**
     * Возвращает найденного администратора по добавочному номеру
     *
     * @param $extension_id
     * @return User
     */
    private function getUserByExtensionId($extension_id)
    {
        $user_id = $this->getProvider()->getUserIdByExtensionId($extension_id);
        if ($user_id) {
            $user = new User($user_id);
        }

        if (!isset($user) || !$user['id']) {
            $user = new User();
            $user['name'] = t('Неизвестный администратор');
            $user['phone'] = $extension_id;
        }

        return $user;
    }

    /**
     * Обработчик сохранения
     *
     * @param string $flag
     * @return false|void|null
     */
    public function beforeWrite($flag)
    {
        $this['_custom_data'] = json_encode($this['custom_data'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Обработчик загрузки объекта
     *
     * @return void
     */
    public function afterObjectLoad()
    {
        $this['custom_data'] = @json_decode($this['_custom_data'], true) ?: [];
    }

    /**
     * Возвращает список из возможных статусов звонка
     *
     * @return array
     */
    public static function getCallStatuses()
    {
        return [
            self::CALL_STATUS_CALLING => t('Идет звонок'),
            self::CALL_STATUS_ANSWER => t('Идет разговор'),
            self::CALL_STATUS_HANGUP => t('Вызов завершен')
        ];
    }

    /**
     * Возвращает возможные причины прекращения звонка
     *
     * @return array
     */
    public static function getCallSubStatuses()
    {
        return [
            self::CALL_SUBSTATUS_ANSWER => t('Отвечен'),
            self::CALL_SUBSTATUS_BUSY => t('Занято'),
            self::CALL_SUBSTATUS_NOANSWER => t('Нет ответа'),
            self::CALL_SUBSTATUS_CANCEL => t('Отменен'),
            self::CALL_SUBSTATUS_CONGESTION => t('Ошибка'),
            self::CALL_SUBSTATUS_CHANUNAVAIL => t('Абонент незарегистрирован')
        ];
    }

    /**
     * Возвращает возможные направления вызова
     *
     * @return array
     */
    public static function getCallFlows()
    {
        return [
            self::CALL_FLOW_IN => t('Входящий'),
            self::CALL_FLOW_OUT => t('Исходящий')
        ];
    }

    /**
     * Заполняет объект из данных события
     *
     * @param CallEvent $call_event
     */
    public function fillFromCallEvent(CallEvent $call_event)
    {
        $mapped_data = [];
        $mapped_data['provider'] = $call_event->getProvider()->getId();
        $mapped_data['call_id'] = $call_event->getCallIdWithProvider();
        $mapped_data['call_api_id'] = $call_event->getCallAPIID();
        $mapped_data['caller_number'] = $call_event->getCallerNumber();
        $mapped_data['caller_id'] = $call_event->getCallerId();
        $mapped_data['called_number'] = $call_event->getCalledNumber();
        $mapped_data['called_id'] = $call_event->getCalledId();
        $mapped_data['called_public_number'] = $call_event->getCalledDID();
        $mapped_data['event_time'] = $call_event->getEventTime();
        $mapped_data['duration'] = $call_event->getDuration();
        $mapped_data['record_id'] = $call_event->getRecID();
        $mapped_data['call_status'] = $call_event->getCallStatus();
        $mapped_data['call_sub_status'] = $call_event->getCallSubStatus();
        $mapped_data['call_flow'] = $call_event->getCallFlow();
        $mapped_data['custom_data'] = $call_event->getData();

        foreach($mapped_data as $key => $value) {
            if ($value) {
                $this[$key] = $value;
            }
        }
    }

    /**
     * Возвращает класс иконки для отображения во всплывающем уведомлении
     *
     * @return string
     */
    public function getCallStatusIconClass()
    {
        if (isset(self::$icon_map[$this['call_status']])) {
            return self::$icon_map[$this['call_status']];
        }

        return 'phone'; //класс иконки по умолчанию
    }

    /**
     * Возвращает список действий, который можно произвести со звонком
     * в зависимости от статуса звонка
     *
     * @return array
     */
    public function getCallActions()
    {
        $provider = $this->getProvider();
        return $provider->getActionsByCall($this);
    }

    /**
     * Возвращает продолжительность разговора в читаемом виде
     *
     * @return string
     */
    public function getDurationString()
    {
        $seconds = floor($this['duration'] / 1000000);
        return Utils::renderSecondsToString($seconds);
    }

    /**
     * Возвращает публичное наименование звонка
     *
     * @return string
     */
    public function getPublicTitle()
    {
        if ($this['call_flow'] == CallHistory::CALL_FLOW_IN) {
            $phrase = 'Входящий звонок №%id от %number, %date %duration';
        } else {
            $phrase = 'Исходящий звонок №%id к %number, %date %duration';
        }

        $duration = $this->getDurationString();

        return t($phrase, [
            'id' => $this->id,
            'number' => $this->getOtherUser()->phone,
            'date' => date('d.m.Y H:i', strtotime($this->event_time)),
            'duration' => $duration ? "({$duration})" : ""
        ]);
    }

    /**
     * Возвращает URL записи
     *
     * @return string
     */
    public function getRecordUrl($absoulte = false)
    {
        if ($this['record_id']) {
            return \RS\Router\Manager::obj()->getAdminUrl(false, [
                'do' => 'getRecord',
                'call_id' => $this['call_id'],
                'ext' => '.mp3' //Исключительно, для обхода валидатора расшерений'а soundManager2
            ], 'crm-callactions', $absoulte);
        }

        return false;
    }

    /**
     * Возвращает true, если звонок инициирован текущим пользователем или предназначен ему
     *
     * @return bool
     */
    public function isMyCall($user_id = null)
    {
        if ($user_id === null) {
            $user_id = Auth::getCurrentUser()->id;
        }

        $provider = $this->getProvider();
        $extension_id = $provider->getExtensionIdByUserId($user_id);

        return
            $extension_id &&
            ($this['caller_number'] == $extension_id
            || $this['called_number'] == $extension_id);
    }

    /**
     * Возвращает идентификатор права на чтение для данного объекта
     *
     * @return string
     */
    public function getRightRead()
    {
        return ModuleRights::CALL_HISTORY_READ;
    }

    /**
     * Возвращает идентификатор права на создание для данного объекта
     *
     * @return string
     */
    public function getRightCreate()
    {
        return ModuleRights::CALL_HISTORY_READ;
    }

    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        return ModuleRights::CALL_HISTORY_READ;
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        if (!$this['id'] || $this->isMyCall()) {
            return ModuleRights::CALL_HISTORY_DELETE;
        } else {
            return ModuleRights::CALL_HISTORY_OTHER_DELETE;
        }
    }
}
