<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification;

use RS\Config\Loader;
use RS\Module\AbstractModel\BaseModel;
use RS\Orm\Request;
use RS\View\Engine;
use Users\Model\Orm\VerificationSession;
use Users\Model\Verification\Action\AbstractVerifyAction;

/**
 * Класс предназначен для обеспечение двухфакторного подтверждения данных.
 * Код верификации будет отправлен с помощью провайдера связи (Например, SMS)
 * Далее, введенный код будет верифицирован и вызвано действие (Action).
 *
 * Данный класс является высокоуровневой упрощенной оберткой для VerificationSession.
 */
class VerificationEngine extends BaseModel
{
    private $verification_session;

    function __construct()
    {
        $this->verification_session = new VerificationSession();
    }

    /**
     * Устанавливает пользователя, который создал сессию верификации
     *
     * @param integer $user_id
     * @return VerificationEngine
     */
    public function setCreatorUserId($user_id)
    {
        $this->verification_session['creator_user_id'] = $user_id;
        return $this;
    }

    /**
     * Устанавливает действие, которое будет выполнено после успешного подтверждения кода верификации
     *
     * @param AbstractVerifyAction $action
     * @return VerificationEngine
     * @throws VerificationException
     */
    public function setAction(AbstractVerifyAction $action)
    {
        $this->verification_session['action'] = $action->getId();
        $this->verification_session->setActionData( $action->exportData() );
        return $this;
    }

    /**
     * Возвращает действие, которое будет выполнено после успешного подтверждения кода верификации
     *
     * @return AbstractVerifyAction
     * @throws VerificationException
     */
    public function getAction()
    {
        return $this->verification_session->getAction();
    }

    /**
     * Устанавливает телефон, на который необходимо отправить код верификации
     *
     * @param string $phone
     * @return VerificationEngine
     */
    public function setPhone($phone)
    {
        $this->verification_session['phone'] = $phone;
        return $this;
    }

    /**
     * Генерирует уникальный ключ сессии проверки
     *
     * @return string
     * @throws VerificationException
     */
    protected function generateToken()
    {
        return sha1(session_id().$this->getAction()->getId().\Setup::$SECRET_KEY);
    }

    /**
     * Создает токен со всей информацией о текущей верификации в БД.
     * Создает сессию или загружает существующую
     *
     * @param bool $skip_if_resolved Если true, то не будет переинициализировать успешно верифицированные ранее сессии
     * @return boolean
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    public function initializeSession($skip_if_resolved = true)
    {
        $this->probablyCleanOldSessions();

        $uniq = $this->generateToken();
        $session = $this->getSession();

        $old_session = VerificationSession::loadByWhere([
            'uniq' => $uniq
        ]);

        if ($skip_if_resolved
            && $old_session['uniq'] == $uniq
            && !$old_session->isExpired()
            && $old_session->isResolved()
            && $old_session['user_session_id'] == session_id()
            && $old_session['phone'] == $this->verification_session['phone']
            && $old_session['creator_user_id'] == $this->verification_session['creator_user_id']
            && $old_session['action'] == $this->verification_session['action'])
        {
            $this->verification_session = $old_session;
            return true; //Устанавливаем предыдущую сессию, если они была успешно верифицирована
        }

        $session['send_counter'] = $old_session['send_counter'];
        $session['send_last_time'] = $old_session['send_last_time'];

        $session['uniq'] = $uniq;
        $session['user_session_id'] = session_id();
        $session['verification_provider'] = VerificationProviderManager::getCurrentProvider()->getId();
        $session['last_initialized'] = time();
        $session['ip'] = $_SERVER['REMOTE_ADDR'];
        $session['is_resolved'] = 0;
        $session['resolved_time'] = null;

        //Обновляем сессию для текущего пользователя
        return $session->replace();
    }


    /**
     * Загружает все параметры (action, phone, ...) по токену
     * Возвращает true, в случае успешной зарузки сессии верификации
     *
     * @param string $token
     * @return bool
     * @throws VerificationException
     */
    public function initializeByToken($token)
    {
        $session = VerificationSession::loadByWhere([
            'uniq' => $token
        ]);

        if ($session['uniq']
            && $session['user_session_id'] == session_id()
            && !$session->isExpired()) {

            $this->verification_session = $session;
            $this->setAction($this->verification_session->getAction());

            return true;
        }

        return $this->addError(t('Токен для текущего пользователя не найден'));
    }

    /**
     * Возвращает объект текущей сессии верификации
     *
     * @return VerificationSession
     */
    public function getSession()
    {
        return $this->verification_session;
    }

    /**
     * Возвращает HTML формы подтврждения второго фактора
     *
     * @return string
     * @throws \SmartyException
     * @throws VerificationException
     */
    public function getVerificationFormView()
    {
        return $this->getAction()->getFormView();
    }

    /**
     * Отправляет код верификации
     *
     * @return bool
     * @throws \RS\Exception
     */
    public function sendCode()
    {
        $session = $this->getSession();
        $result = $session->sendVerificationCode();

        if (!$result) {
            $this->importErrors($session->exportErrors());
        }
        return $result;
    }

    /**
     * Возвращает true, в случае успешной авторизации, иначе false.
     * Текст ошибки можно получить с помощью метода $this->getErrors()
     *
     * @param $code
     * @return bool(false) | mixed
     */
    public function checkCode($code)
    {
        $session = $this->getSession();
        $result = $session->checkCodeAndResolve($code);

        if (!$result) {
            $this->importErrors($session->exportErrors());
        }

        return $result;
    }

    /**
     * Сбрасывает флаг подтверждения у сессии
     *
     * @return bool
     */
    public function reset()
    {
        return $this->getSession()->reset();
    }

    /**
     * Удаляет устаревшие сессии
     *
     * @return bool
     */
    public function cleanOldSessions()
    {
        $config = Loader::byModule($this);
        $lifetime_hours = max($config['lifetime_session_hours'], $config['lifetime_resolved_session_hours']);
        $time = time() - ($lifetime_hours * 3600);

        Request::make()
            ->delete()
            ->from($this->verification_session)
            ->where("last_initialized < '#time'", ['time' => $time])
            ->exec();

        return true;
    }

    /**
     * Запускает очистку старых сессий по вероятности
     *
     * @param float $ratio Вероятность запуска очистки от 0 до 100
     * @return bool
     */
    public function probablyCleanOldSessions($run_percent = 40)
    {
        if (rand(1, 100) <= $run_percent) {
            return $this->cleanOldSessions();
        }
        return false;
    }
}