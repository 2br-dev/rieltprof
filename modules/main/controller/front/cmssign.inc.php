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
class CmsSign extends \RS\Controller\Front
{
    function actionIndex()
    {
        $this->wrapOutput(false);
        return 'ReadyScript Shop CMS (<a href="http://readyscript.ru">http://readyscript.ru</a>)';
    }
}