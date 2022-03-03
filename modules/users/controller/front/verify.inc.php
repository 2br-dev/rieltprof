<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Front;

use RS\Controller\Front;
use Users\Model\Verification\Action\AbstractVerifyAction;
use Users\Model\Verification\VerificationEngine;

/**
 * Front-контроллер, обеспечивающий работу двухфакторной авторизации
 */
class Verify extends Front
{
    protected $token;
    /**
     * @var VerificationEngine
     */
    protected $verification_engine;

    /**
     *
     */
    function init()
    {
        $this->token = $this->url->post('token', TYPE_STRING);
        $this->verification_engine = new VerificationEngine();

        if (!$this->verification_engine->initializeByToken($this->token)) {
            $this->e404($this->verification_engine->getErrorsStr());
        }

        $this->wrapOutput(false);
    }

    /**
     * Отправляет код верификации
     */
    public function actionSendCode()
    {
        if ($this->verification_engine->getAction()->getTypeVerification() == AbstractVerifyAction::TYPE_VERIFICATION_PHONE_INLINE) {
            //установим телефон, на который необходимо отправить код
            $phone = $this->url->request('phone', TYPE_STRING);
            $this->verification_engine->setPhone($phone);
        }

        $this->result->setSuccess(
            $this->verification_engine->sendCode()
        );

        return $this->result
            ->setHtml( $this->verification_engine->getVerificationFormView() );
    }

    /**
     * Проверяет код верификации
     */
    public function actionCheckCode()
    {
        $code = $this->url->post('code', TYPE_STRING);

        $this->result->setSuccess(
            $this->verification_engine->checkCode($code)
        );

        return $this->result
            ->setHtml( $this->verification_engine->getVerificationFormView() );
    }

    /**
     * Сбрасывает верификацию. Позволяет изменить номер телефона
     */
    public function actionReset()
    {
        $this->result->setSuccess(
            $this->verification_engine->reset()
        );

        return $this->result
            ->setHtml( $this->verification_engine->getVerificationFormView() );
    }
}