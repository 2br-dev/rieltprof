<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\OrmType;

use RS\Config\Loader;
use RS\Http\Request;
use RS\Orm\AbstractObject;
use RS\Orm\Type\Varchar;
use Users\Model\Verification\Action\AbstractVerifyAction;
use Users\Model\Verification\Action\AbstractVerifyTypePhone;
use Users\Model\Verification\VerificationEngine;

/**
 * Orm тип свойства, описывает номер телефона, подтвержденный с помощью SMS
 * В случае, когда подтверждение недопустимо в связи с настройками в модуле Users, отображает
 * обычное поле ввода номера телефона
 */
class VerifiedPhone extends Varchar
{
    protected $verified_phone = null;
    protected $enable_verification = true;

    /**
     * Возвращает массив с функциями валидации
     *
     * @return array
     */
    public function getCheckers()
    {
        $internal_checker = [[
            'callmethod' => [$this, 'verifyCode'],
            'errortext' => null,
            'param' => [],
        ]];

        return array_merge($this->checkers, $internal_checker);
    }

    /**
     * Проверяет подтвержден ли номер телефона или нет
     *
     * @param AbstractObject $orm_object
     * @param $value
     *
     * @return bool|string
     * @throws \Users\Model\Verification\VerificationException
     */
    public function verifyCode($orm_object, $value)
    {
        if (!$this->isEnabledVerification()) {
            return true;
        }

        $verified_phone = $this->getVerifiedPhone();
        if ($verified_phone !== null && $this->get() === $verified_phone) {
            return true;
        }

        //Проверяет на корректность токена верификации
        $token = $this->getTokenFromPost();

        if ($token) {
            $verification_engine = new VerificationEngine();
            $verification_engine->initializeByToken($token);

            if ($verification_engine->getSession()->isResolved()) {
                return true;
            }
        }

        return t('%0 не подтвержден', [$this->getDescription()]);
    }

    /**
     * Возвращает имя формы для верификации кода
     *
     * @return string
     */
    public function getTokenInputName()
    {
        if ($this->form_name) {
            return $this->form_name.'_token';
        }
        return $this->name . '_token';
    }

    /**
     * Возвращает токен, который должен быть подтвержден
     *
     * @return string
     */
    public function getTokenFromPost()
    {
        return Request::commonInstance()
            ->post($this->getTokenInputName(), TYPE_STRING);
    }

    /**
     * Возвращает движок верификации
     */
    public function getVerificationEngine()
    {
        $action = $this->getVerificationAction();
        if (!$action) {
            throw new \RS\Exception(t('Не установлено действие с помощью метода setVerificationAction'));
        }

        $action->setPhoneInputName($this->getFormName());
        $action->setPhoneInputAttrLine($this->getAttr());
        $action->setTokenInputName($this->getTokenInputName());

        $verification_engine = new VerificationEngine();
        $verification_engine->setAction($action);
        $verification_engine->setPhone($this->get());
        $verification_engine->initializeSession();

        //Переустанавливаем принудительно новые параметры и атрибуты поля ввода
        $session = $verification_engine->getSession();
        $session->setActionData($action->exportData());
        $session->update();

        $verified_phone = $this->getVerifiedPhone();
        if ($verified_phone !== null && $this->get() === $verified_phone) {
            $session = $verification_engine->getSession();
            $session['is_resolved'] = 1;
            $session['resolved_time'] = time();
            $session->update();
        }

        return $verification_engine;
    }

    /**
     * Устанавливает действие, которое будт выполнено после верификации
     *
     * @param AbstractVerifyTypePhone $action
     */
    public function setVerificationAction(AbstractVerifyTypePhone $action)
    {
        $this->verification_action = $action;
    }

    /**
     * Возвращает действие, которое будт выполнено после верификации
     *
     * @return AbstractVerifyAction
     */
    public function getVerificationAction()
    {
        return $this->verification_action;
    }

    /**
     * Возвращает шаблон по-умолчанию для данного поля
     *
     * @return string
     */
    function getOriginalTemplate()
    {
        if ($this->isEnabledVerification()) {
            return '%users%/ormtype/verifiedphone.tpl';
        } else {
            return $this->form_template;
        }
    }

    /**
     * Возвращает true, если необходимо отобразить поле со включенной двухфакторной верификацией
     *
     * @return bool
     */
    public function isEnabledVerification()
    {
        if ($this->enable_verification === false) {
            return false;
        }

        $config = Loader::byModule(__CLASS__);
        return $config->isEnabledTwoFactorRegister();
    }

    /**
     * Принудительно устанавливает статус верификации.
     * true - верифицировать, когда это возможно в настройках модуля Users
     * false - никогда не верифицировать
     *
     * @param bool $bool
     */
    public function setEnableVerification($bool)
    {
        $this->enable_verification = $bool;
    }

    /**
     * Устанавливает, какой телефон нужно считать уже подтвержденным
     *
     * @param string $phone
     */
    public function setVerifiedPhone($phone)
    {
        $this->verified_phone = $phone;
    }

    /**
     * Возвращает номер телефона, который считается подтвержденным
     *
     * @return string
     */
    public function getVerifiedPhone()
    {
        return $this->verified_phone;
    }
}