<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Provider;

use RS\Exception;
use Users\Model\Orm\VerificationSession;
use Users\Model\Verification\VerificationException;

/**
 * Абстрактный класс провайдера доставки проверочного кода для верификации.
 * Например, SMS
 */
abstract class AbstractProvider
{
    /**
     * Доставляет код к пользователю
     *
     * @param VerificationSession $session Сессия верификации
     * @param string $code Код верификации
     * @return bool
     * @throws Exception Бросает исключение в случае ошибки
     */
    abstract public function send(VerificationSession $session, $code);

    /**
     * Возвращает название
     *
     * @return mixed
     */
    abstract public static function getTitle();

    /**
     * Возвращает строковый идентификатор провайдера
     * @return mixed
     */
    abstract public static function getId();
}