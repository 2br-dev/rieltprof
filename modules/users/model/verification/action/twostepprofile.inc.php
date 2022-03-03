<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Action;

use Users\Model\Verification\VerificationException;

/**
 * Класс описывает действие, выполняемое после успешной верификации
 * телефона при редактировании профиля
 */
class TwoStepProfile extends TwoStepRegister
{

    /**
     * Возвращает название операции в родительном падеже
     * Например (код для): авторизации, ргистрации...
     * @return string
     */
    public function getRpTitle()
    {
        return t('изменения номера телефона');
    }
}