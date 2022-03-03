<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification;

use RS\Config\Loader;
use RS\Exception;
use Users\Model\Verification\Provider\AbstractProvider;
use Users\Model\Verification\Provider\Sms;

/**
 * Класс отвечает за работу со списком провайдеров верификации
 */
class VerificationProviderManager
{

    static $providers;

    /**
     * Возвращает текущего провайдера для отправки кода
     * @return AbstractProvider
     * @throws Exception
     */
    public static function getCurrentProvider()
    {
        $config = Loader::byModule(__CLASS__);
        $name = $config['type_code_provider'];
        return self::getProviderByName($name);
    }

    /**
     * Собирает доступных провайдеров для отправки кода
     * @throws Exception
     * @throws \RS\Event\Exception
     */
    public static function getAvailableProviders()
    {
        if (self::$providers === null) {
            $event_result = \RS\Event\Manager::fire('verification.getproviders', []);
            $list = $event_result->getResult();
            self::$providers = [];
            foreach ($list as $verification) {
                if (!($verification instanceof AbstractProvider)) {
                    throw new Exception(t('Провайдер должен быть наследником \Users\Model\Verification\AbstractVerification'));
                }
                self::$providers[$verification->getId()] = $verification;
            }
        }
        return self::$providers ?? [];
    }

    /**
     * Возвращает названия всех провайдеров верификации кода
     *
     * @return array
     * @throws Exception
     * @throws \RS\Event\Exception
     */
    public static function getProviderTitles()
    {
        $result = [];
        foreach(self::getAvailableProviders() as $name => $verification) {
            $result[$name] = $verification->getTitle();
        }
        return $result;
    }

    /**
     * Возвращает провайдера для отправки смс по его имени
     * @param $name
     * @return AbstractProvider
     * @throws Exception
     */
    public static function getProviderByName($name)
    {
        self::getAvailableProviders();
        return self::$providers[$name] ?? new Sms();
    }
}

