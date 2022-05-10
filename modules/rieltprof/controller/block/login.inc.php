<?php
/**
 * ReadyScript (http://readyscript.ru)
 *
 * @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
 * @license http://readyscript.ru/licenseAgreement/
 */

namespace Rieltprof\Controller\Block;

use rieltprof\Model\PartnersApi;
use RS\Controller\StandartBlock;

/**
 * Контроллер - топ товаров из указанных категорий одним списком
 */
class Login extends StandartBlock
{
    protected static $controller_title = 'Авторизация';
    protected static $controller_description = 'Отображает шаблон авторизации';

    protected $default_params = [
        'indexTemplate' => '%users%/authorization.tpl', //Должен быть задан у наследника
    ];

    function actionIndex()
    {
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
