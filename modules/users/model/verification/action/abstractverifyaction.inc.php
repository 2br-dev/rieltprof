<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Action;

use Users\Model\Orm\VerificationSession;
use Users\Model\Verification\VerificationException;

/**
 * Абстрактный класс одного действия,
 * которое вызывается при успешном подтверждении кода верификации.
 */
abstract class AbstractVerifyAction
{
    /**
     * Проверка кода на отдельной странице или окне
     */
    const TYPE_VERIFICATION_CODE = 'code';
    /**
     * Проверка номера телефона внутри формы
     */
    const TYPE_VERIFICATION_PHONE_INLINE = 'phone-inline';

    /**
     * Произвольные данные
     * @var array
     */
    protected $data = [];

    /**
     * @var VerificationSession
     */
    private $session;

    /**
     * Возвращает название операции в родительном падеже
     * Например (код для): авторизации, ргистрации...
     * @return string
     */
    abstract public function getRpTitle();

    /**
     * Метод вызывается при успешном прохождении верификации
     *
     * @return bool
     * @throws VerificationException
     */
    abstract public function resolve();

    /**
     * Возвращает тип верификации, который характерен для данного действия
     *
     * @return string
     */
    public function getTypeVerification() {}

    /**
     * Возвращает готовый HTML формы верификации
     *
     * @return string
     */
    public function getFormView() {}

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        $class = get_class($this);
        return str_replace(['\\', '-model-verification-action'], ['-', ''], strtolower($class));
    }

    /**
     * Создает экземпляр класса по его строковому идентификатору
     * @return self
     * @throws VerificationException
     */
    public static function makeById($id)
    {
        if (preg_match('/^(.*?)-(.*)$/', $id, $match)) {
            $class = str_replace('-',  '\\', $match[1].'-model-verification-action-'.$match[2]);
            if (is_subclass_of($class, __CLASS__)) {
                return new $class();
            }
        }

        throw new VerificationException(t('Класс действия %0 должен быть наследником класса \Users\Model\Verification\Action\AbstractVerifyAction', [$id]));
    }

    /**
     * Устанавливает сессию
     *
     * @param VerificationSession $session
     */
    public function setVerificationSession($session)
    {
        $this->session = $session;
    }

    /**
     * Возвращает сессию, которая запускает действие
     *
     * @return VerificationSession
     * @throws VerificationException
     */
    public function getVerificationSession()
    {
        if (!$this->session) {
            throw new VerificationException(t('В действии %0 не установлена сессия', [$this->getId()]));
        }

        return $this->session;
    }

    /**
     * Добавляет произвольные данные в текущее действие.
     * Данные должны добавляться отдельными методами
     *
     * @param string $key
     * @param mixed $value
     */
    protected function addData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Возвращает произвольные данные по ключу
     * @param string $key ключ данных
     * @param mixed $default значение по умолчанию
     * @return mixed
     */
    protected function getData($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * Возвращает данные, установленные у данного действия
     *
     * @return array
     */
    public function exportData()
    {
        return $this->data;
    }

    /**
     * Загружает данные, которымиможет пользоваться действие
     *
     * @param $data
     */
    public function importData($data)
    {
        $this->data = $data;
    }
}