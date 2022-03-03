<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Orm;

use RS\Config\Loader;
use RS\Exception;
use RS\Helper\Tools;
use RS\Orm\AbstractObject;
use RS\Orm\Request;
use RS\Orm\Type;
use RS\Orm\Type\Checker;
use Users\Model\Verification\Action\AbstractVerifyAction;
use Users\Model\Verification\Provider\AbstractProvider;
use Users\Model\Verification\VerificationEngine;
use Users\Model\Verification\VerificationException;
use Users\Model\Verification\VerificationProviderManager;

/**
 * ORM объект описывает сессию верификации вторым фактором одного действия одним пользователем
 * --/--
 * @property string $uniq Уникальный ключ сессии верификации
 * @property string $ip IP-адрес пользователя
 * @property string $user_session_id ID сессии пользователя
 * @property integer $creator_user_id Пользователь, создатель сессии
 * @property string $verification_provider Идентификатор провайдера доставки кода
 * @property string $phone Номер телефона для отправки кода
 * @property string $code_hash Хэш от кода верификации
 * @property string $code_debug Код врификации в открытом виде (для режима отладки)
 * @property integer $code_expire Время истечения действия кода
 * @property integer $send_counter Счетчик отправки кодов
 * @property integer $send_last_time Последняя дата отправки кода
 * @property integer $try_counter Счетчик ввода кодов
 * @property integer $try_last_time Последняя дата попытки ввода кода
 * @property string $action Идентификатор класса действия
 * @property string $action_data Данные для действия
 * @property integer $last_initialized Последняя дата инициализации сессии
 * @property integer $is_resolved Код был введен успешно
 * @property integer $resolved_time Время успешного ввода кода
 * --\--
 */
class VerificationSession extends AbstractObject
{
    const STATE_INITIALIZED = 'initialized';
    const STATE_CODE_ENTER = 'codeEnter';
    const STATE_RESOLVED = 'resolved';

    protected static $table = 'users_verification_session';
    protected $config;

    function __construct()
    {
        $this->config = Loader::byModule($this);
        parent::__construct();
    }

    /**
     * В данном методе должны быть заданы поля объекта.
     * Вызывается один раз для одного класса объектов в момент первого обращения к свойству
     */
    protected function _init()
    {
        $this->getPropertyIterator()->append([
            'uniq' => new Type\Varchar([
                'description' => t('Уникальный ключ сессии верификации'),
                'allowEmpty' => false,
                'primaryKey' => true
            ]),
            'ip' => new Type\Varchar([
                'description' => t('IP-адрес пользователя'),
                'maxLength' => 50,
            ]),
            'user_session_id' => new Type\Varchar([
                'description' => t('ID сессии пользователя')
            ]),
            'creator_user_id' => new Type\User([
                'description' => t('Пользователь, создатель сессии')
            ]),
            'verification_provider' => new Type\Varchar([
                'description' => t('Идентификатор провайдера доставки кода')
            ]),
            'phone' => new Type\Varchar([
                'description' => t('Номер телефона для отправки кода')
            ]),
            'code_hash' => new Type\Varchar([
                'description' => t('Хэш от кода верификации')
            ]),
            'code_debug' => new Type\Varchar([
                'description' => t('Код врификации в открытом виде (для режима отладки)')
            ]),
            'code_expire' => new Type\Integer([
                'description' => t('Время истечения действия кода')
            ]),
            'send_counter' => new  Type\Integer([
                'description' => t('Счетчик отправки кодов'),
                'allowEmpty' => false
            ]),
            'send_last_time' => new Type\Integer([
                'description' => t('Последняя дата отправки кода')
            ]),
            'try_counter' => new Type\Integer([
                'description' => t('Счетчик ввода кодов'),
                'allowEmpty' => false
            ]),
            'try_last_time' => new Type\Integer([
                'description' => t('Последняя дата попытки ввода кода')
            ]),
            'action' => new type\Varchar([
                'description' => t('Идентификатор класса действия'),
                'maxLength' => 100
            ]),
            'action_data' => new Type\Text([
                'description' => t('Данные для действия')
            ]),
            'last_initialized' => new Type\Integer([
                'description' => t('Последняя дата инициализации сессии'),
            ]),
            'is_resolved' => new Type\Integer([
                'maxLength' => 1,
                'description' => t('Код был введен успешно')
            ]),
            'resolved_time' => new Type\Integer([
                'description' => t('Время успешного ввода кода')
            ])
        ]);

        $this->addIndex(['ip', 'action', 'is_resolved', 'last_initialized'], self::INDEX_KEY);
    }

    /**
     * Возвращает объект пользователя
     *
     * @return User
     */
    public function getCreatorUser()
    {
        return new User($this['creator_user_id']);
    }

    /**
     * Возвращает true, если сессия еще считается верифицированной и не требует повторной верификации
     *
     * @return bool
     */
    public function isResolved()
    {
        return
            $this['is_resolved']
            && ($this->config['lifetime_resolved_session_hours'] == 0
            || time() <= $this['resolved_time'] + $this->config['lifetime_resolved_session_hours'] * 3600);
    }

    /**
     * Возвращает true, если сессия верификации была инициализирована слишком давно и уже протухла
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->config['lifetime_session_hours'] > 0
                && time() > $this['last_initialized'] + $this->config['lifetime_session_hours'] * 3600;
    }

    /**
     * Устанавливает данные для действия
     *
     * @return array
     */
    public function getActionData()
    {
        return @json_decode($this['action_data'], true) ?? [];
    }

    /**
     * Возвращает объект действия, которое будет вызвано, в случае успешной верификации кода
     *
     * @param bool $cache
     * @return AbstractVerifyAction
     * @throws VerificationException
     */
    public function getAction()
    {
        $action = AbstractVerifyAction::makeById($this['action']);
        $action->importData($this->getActionData());
        $action->setVerificationSession($this);

        return $action;
    }

    /**
     * Возвращает данные для действия
     *
     * @param array $data
     */
    public function setActionData(array $data)
    {
        $this['action_data'] = json_encode($data);
    }

    /**
     * Возвращает первичный ключ объекта
     *
     * @return string
     */
    public function getPrimaryKeyProperty()
    {
        return 'uniq';
    }

    /**
     * Возвращает уникальный идентификатор записи
     *
     * @return string
     */
    public function getToken()
    {
        return $this['uniq'];
    }

    /**
     * Возвращает количество секунд, через которое возможно повторно отправить код
     *
     * @return integer
     */
    public function getRefreshCodeDelay()
    {
        //Если превышено число попыток отправки кода, то блокируем на время
        if ($this['send_counter'] > $this->getMaxSendLimit() - 1) {
            $remain = $this['send_last_time'] + $this->config['block_delay_minutes'] * 60 - time();
        }

        //Рассчитываем остаток времени, через который можно отправить повторно код
        elseif ($this['send_last_time']) { //Если до этого были попытки отправки кода
            $remain = $this['send_last_time'] + $this->config['resend_delay_seconds'] - time();
        }

        else {
            $remain = 0;
        }

        if ($remain < 0) $remain = 0;

        return $remain;
    }

    /**
     * Генерирует новый код верификации для отправки
     * @return string
     */
    public function generateCode()
    {
        $code = Tools::generatePassword($this->config['code_length'], range(0,9));
        return $code;
    }

    /**
     * Устанавливает новый код верификации
     *
     * @param $code
     */
    public function setNewCode($code)
    {
        $this['code_hash'] = $this->getCodeHash($code);

        if ($this->config['two_factor_demo_mode']) {
            $this['code_debug'] = $code;
        }

        $this['code_expire'] = time() + $this->config['lifetime_code_minutes'] * 60;
        $this['is_resolved'] = 0;
        $this['resolved_time'] = null;
    }

    /**
     * Возвращает хэш от кода
     *
     * @param string $code
     * @return string
     */
    public function getCodeHash($code)
    {
        return sha1($code.\Setup::$SECRET_KEY.\Setup::$SECRET_SALT);
    }

    /**
     * Возвращает true, если еще не истекло время ввода кода
     *
     * @return bool
     */
    public function canEnterCode()
    {
        return $this['code_expire'] > time();
    }

    /**
     * Возвращает максимальное возможное количество
     * попыток ввода кода
     *
     * @return integer
     */
    public function getMaxCodeEnterLimit()
    {
        return $this->config['try_count_limit'];
    }

    /**
     * Возвращает максимальное количество попыток отправки кода
     *
     * @return integer
     */
    public function getMaxSendLimit()
    {
        return $this->config['send_count_limit'];
    }

    /**
     * Увеличивает счетчик попыток ввода кода, если интервал меньше срока блокировки
     * Сбрасывает счетчик, если интервал больше срока блокировки
     */
    private function incrementTryCounter()
    {
        if (time() - $this['try_last_time'] <  $this->config['block_delay_minutes']) {
            $this['try_counter'] = $this['try_counter'] + 1;
        } else {
            $this['try_counter'] = 1;
        }

        $this['try_last_time'] = time();
    }

    /**
     * Увеличивает сетчик отправок кода
     */
    private function incrementSendCounter()
    {
        if (time() < $this['send_last_time'] + $this->config['block_delay_minutes'] * 60) {
            $this['send_counter'] = $this['send_counter'] + 1;
        } else {
            $this['send_counter'] = 1;
        }

        $this['send_last_time'] = time();
        $this['try_counter'] = 0;
    }


    /**
     * Возвращает объект провайдера верификации (Например, SMS)
     *
     * @return AbstractProvider
     * @throws Exception
     */
    private function getVerificationProvider()
    {
        return VerificationProviderManager::getProviderByName($this['verification_provider']);
    }

    /**
     * Возвращает true, если превышен лимит на количество созданных, не верифицированных сессий с одного IP
     *
     * @return bool
     */
    public function isIpSessionLimit()
    {
        $hour_ago = time() - 3600;
        return Request::make()
            ->from($this)
            ->where([
                'ip' => $this['ip'],
                'action' => $this['action'],
                'is_resolved' => 0
            ])
            ->where('last_initialized > #time', ['time' => $hour_ago])
            ->count() > $this->config['ip_limit_session_count'];
    }

    /**
     * Отправляет код верификации
     *
     * @return bool
     * @throws Exception
     */
    public function sendVerificationCode()
    {
        if ($this->isIpSessionLimit()) {
            return $this->addError(t('Превышено количество сессий верификации с вашего IP. Повторите попытку позже.'));
        }

        if ($this['phone'] == '') {
            return $this->addError(t('Не указан номер телефона'));
        }

        $check_phone_result = Checker::chkPhone(null, $this['phone']);
        if ($check_phone_result !== true) {
            return $this->addError($check_phone_result);
        }

        $send_code_delay = $this->getRefreshCodeDelay();

        if ($send_code_delay == 0) {
            $code = $this->generateCode();

            if (!$this->config['two_factor_demo_mode']) {
                try {
                    $this->getVerificationProvider()->send($this, $code);
                } catch(Exception $e) {
                    return $this->addError(t('Не удалось отправить код верификации.').$e->getMessage());
                }
            }

            $this->setNewCode($code);
            $this->incrementSendCounter();
            $this->update();
            return true;
        } else {
            return $this->addError(t('Отправить код можно будет через %0 секунд', [$this->formatSecond($send_code_delay)]));
        }
    }

    /**
     * Проверяет код и вызывает действие в случае успеха
     *
     * @param $code
     * @return bool(false) | mixed
     */
    public function checkCodeAndResolve($code)
    {
        if ($this->checkVerificationCode($code)) {
            try {

                $result = $this->getAction()->resolve();
                $this['is_resolved'] = 1;
                $this['resolved_time'] = time();
                $this->update();

                return $result;

            } catch(VerificationException $e) {
                return $this->addError($e->getMessage());
            }
        } else {
            return false;
        }
    }

    /**
     * Возвращает true, если проверка кода прошла успешно
     *
     * @param string $code код верификации
     * @return bool
     */
    public function checkVerificationCode($code)
    {
        if (time() < $this['code_expire']) {
            if ($code !== '') {
                if ($this['code_hash'] != '') {
                    if ($this['try_counter'] < $this->config['try_count_limit'] - 1) {
                        $this->incrementTryCounter();

                        if ($this->getCodeHash($code) === $this['code_hash']) {
                            //Обнуляем счетчик отправок кода
                            $this['send_counter'] = 0;
                            $this['try_counter'] = 0;
                            $result = true;
                        } else {
                            $remain_try = $this->config['try_count_limit'] - $this['try_counter'];
                            $result = $this->addError(t('Неверно указан код. Осталось %0 попыток', [$remain_try]));
                        }

                        $this->update();
                        return $result;

                    } else {
                        return $this->addError(t('Превышено количество попыток ввода кода, получите новый код'));
                    }
                } else {
                    return $this->addError(t('Не установлен код верификации'));
                }
            } else {
                return $this->addError(t('Не введен код верификации'));
            }
        } else {
            return $this->addError(t('Код просрочен, получите новый код'));
        }
    }

    /**
     * Форматирует количество секунд
     *
     * @param integer $second
     * @return string
     */
    public function formatSecond($second)
    {
        $hours = floor($second/3600);
        $minutes = floor(($second - $hours*3600)/60);
        $seconds = $second - ($minutes * 60 + $hours * 3600);

        return
            ($hours ? sprintf('%02d', $hours).':' : '').
            sprintf('%02d', $minutes).':'.
            sprintf('%02d', $seconds);
    }

    /**
     * Возвращает номер телефона с пропусками в виде звездочек
     *
     * @return string
     */
    public function getPhoneMask()
    {
        return substr($this['phone'], 0, 6).'****'.substr($this['phone'], 10);
    }

    /**
     * Возвращает количество оставшихся попыток отправки кода
     *
     * @return integer
     */
    public function getSendTryRemain()
    {
        return  $this->getMaxSendLimit() - $this['send_counter'];
    }

    /**
     * Возвращает длину кода верификации
     *
     * @return integer
     */
    public function getCodeLength()
    {
        return $this->config['code_length'];
    }

    /**
     * Возвращает текущий статус верификации.
     *
     * @return string
     */
    public function getState()
    {
        if ($this->isResolved() && !$this->isExpired()) {
            return self::STATE_RESOLVED;
        }

        if ($this->canEnterCode()) {
            return self::STATE_CODE_ENTER;
        }

        return self::STATE_INITIALIZED;
    }

    /**
     * Сбрасывает флаг подтверждения у сессии
     *
     * @return bool
     */
    public function reset()
    {
        $this['is_resolved'] = 0;
        $this['resolved_time'] = null;
        //$this['phone'] = null;

        $this['code_debug'] = null;
        $this['code_hash'] = null;
        $this['code_expire'] = null;

        $this->update();

        return true;
    }
}