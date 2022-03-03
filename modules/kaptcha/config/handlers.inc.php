<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Kaptcha\Config;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this->bind('captcha.gettypes');
    }

    /**
     * Для совместимости со старыми версиями
     */
    public static function getRoute($routes)
    {}

    /**
    * Добавляем стандартную капчу
    */
    public static function captchaGetTypes($list)
    {
        $list[] = new \Kaptcha\Model\CaptchaType\RSDefault();
        $list[] = new \Kaptcha\Model\CaptchaType\ReCaptcha3();
        return $list;
    }
}
