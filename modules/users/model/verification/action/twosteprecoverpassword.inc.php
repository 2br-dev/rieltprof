<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Action;

use RS\Controller\Result\Standard as StandardResult;
use RS\Router\Manager;

/**
 * Класс описывает действие, выполняемое после успешной верификации
 * телефона для восстановления пароля
 */
class TwoStepRecoverPassword extends AbstractVerifyTypeCode
{

    /**
     * Метод вызывается при успешном прохождении верификации
     *
     * @return StandardResult
     */
    public function resolve()
    {
        $session = $this->getVerificationSession();
        $user = $session->getCreatorUser();
        if ($user['id']) {
            $url = Manager::obj()->getUrl('users-front-auth', ['Act' => 'changePassword', 'uniq' => $user['hash']]);

            $result = new StandardResult();
            return $result
                ->setSuccess(true)
                ->setAjaxWindowRedirect($url)
                ->setNoAjaxRedirect($url);
        }
    }

    /**
     * Возвращает название операции в родительном падеже
     * Например (код для): авторизации, ргистрации...
     * @return string
     */
    public function getRpTitle()
    {
        return t('восстановления пароля');
    }
}