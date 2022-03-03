<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller;

/**
* Ислючение - 404 страница не найдена
*/
class ExceptionPageNotFound extends \RS\Exception
{    
    
    function __construct($message = '', $controller_class = '', $code = 404, Exception $previous = null)
    {
        $message = t('Страницы, которую вы запросили, не существует. ').$message;
        parent::__construct($message, $code, $previous, $controller_class);
    }

}

