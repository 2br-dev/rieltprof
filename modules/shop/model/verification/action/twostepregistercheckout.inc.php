<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Verification\Action;

use Users\Model\Verification\Action\TwoStepRegister;

/**
 * Класс описывает действие, выполняемое после успешной верификации
 * телефона при регистрации пользователя во время оформления заказа
 */
class TwoStepRegisterCheckout extends TwoStepRegister
{}