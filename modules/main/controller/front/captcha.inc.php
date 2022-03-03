<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Front;

/**
* Фронт контроллер. Позволяющий получить отпечаток CMS.
* URL по умолчанию для данного фронт контроллера /cms-sign/
*/
class Captcha extends \RS\Controller\Front
{
    function actionIndex()
    {
        $this->wrapOutput(false);
        $method = strtolower('action' . $this->url->request('do', TYPE_STRING, 'default'));
        $captcha = \RS\Captcha\Manager::currentCaptcha();
        $response = '';
        if (method_exists($captcha, $method)) {
            $response = $captcha->{$method}();
        }
        return $response;
    }
}
