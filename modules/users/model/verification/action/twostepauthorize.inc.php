<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Action;

use RS\Application\Auth as AppAuth;
use RS\Controller\Result\Standard as StandardResult;
use Users\Model\Orm\User;
use Users\Model\Verification\VerificationException;

/**
 * Класс описывает действие, выполняемое после верификации
 * кода при авторизации. Завершает авторизацию пользователя
 */
class TwoStepAuthorize extends AbstractVerifyTypeCode
{

    /**
     * Метод вызывается при успешном прохождении верификации
     *
     * @return StandardResult
     * @throws VerificationException
     */
    public function resolve()
    {
        $session = $this->getVerificationSession();

        $user = new User($session['creator_user_id']);
        AppAuth::setCurrentUser($user);
        $success = AppAuth::onSuccessLogin($user, $this->data['remember'] ?? 0);

        if ($success) {
            return $this->successResult();
        } else {
            throw new VerificationException(AppAuth::getError());
        }
    }

    /**
     * Возвращает успешный ответ, который контроллер затем вернет клиенту
     *
     * @return StandardResult
     */
    protected function successResult()
    {
        $result = new StandardResult();
        return $result
            ->setSuccess(true)
            ->setNoAjaxRedirect($this->getReferer())
            ->addSection('reloadPage', true);
    }

    /**
     * Устанавливает значение флага "Запомнить меня" при авторизации
     *
     * @param bool $is_remember
     */
    public function setRememberUser($is_remember)
    {
        $this->addData('remember', $is_remember);
    }

    /**
     * устанавливает URL, на который необходимо вернуть пользователя после автоизации
     */
    public function setReferer($referer)
    {
        $this->addData('referer', $referer);
    }

    /**
     * Возвращает URL, на который необходимо вернуть пользователя после авторизации
     *
     * @param string $default
     * @return mixed
     */
    public function getReferer($default = '/')
    {
        return $this->getData('referer', $default);
    }


    /**
     * Возвращает название операции в родительном падеже
     * Например (код для): авторизации, ргистрации...
     *
     * @return string
     */
    public function getRpTitle()
    {
        return t('авторизации');
    }
}