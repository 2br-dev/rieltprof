<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Controller\Front;

class CheckForFatal extends \RS\Controller\Front
{
    function actionIndex()
    {
        echo json_encode([
            'success' => true,
        ]);
        exit;
    }
}

