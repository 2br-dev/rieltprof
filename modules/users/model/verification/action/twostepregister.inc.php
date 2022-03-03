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
 * телефона при регистрации пользователя
 */
class TwoStepRegister extends AbstractVerifyTypePhone
{

    /**
     * Метод вызывается при успешном прохождении верификации
     *
     * @return bool
     * @throws VerificationException
     */
    public function resolve()
    {
        //Ничего не делаем, verification session token
        // становится подтвержденным и этого достаточно
        return true;
    }

    /**
     * Возвращает название операции в родительном падеже
     * Например (код для): авторизации, ргистрации...
     * @return string
     */
    public function getRpTitle()
    {
        return t('регистрации');
    }
}