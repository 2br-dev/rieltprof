<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
/**
* Обработчик исключений, используемый во время разработки
* @param Exception $exception
*/
function exceptionHandlerDevelop($exception)
{
    $type = get_class($exception);
    $extra_info = ($exception instanceof \RS\Exception) ? $exception->getExtraInfo() : '';
    list($code, $message) = getCodeMessage($exception);
    \RS\Application\Application::getInstance()->headers->cleanHeaders()->setStatusCode($code)->sendHeaders();
    
    echo "<div style='background-color: #f3f3f3;'>
          <div><b>".t('Исключение')." - \"{$exception->getMessage()}\"</b></div>
          <table valign='top'>".
          (!empty($extra_info) ? "<tr><td>".t('Подробности').":</td><td>{$extra_info}</td></tr>" : "").
          "<tr><td>".t('Код ошибки').":</td><td>{$exception->getCode()}</td></tr>
          <tr><td>".t('Тип ошибки').":</td><td>$type</td></tr>
          <tr><td>".t('Файл').":</td><td>{$exception->getFile()}</td></tr>  
          <tr><td>".t('Строка').":</td><td>{$exception->getLine()}</td></tr>  
          <tr><td>".t('Стек вызова').":</td><td><pre>{$exception->getTraceAsString()}</pre></td></tr>  
          </table>
          </div>";
}

/**
* Общий обработчик исключений
* @param Exception $exception
*/
function exceptionHandler($exception)
{
    list($code, $message) = getCodeMessage($exception);
    \RS\Application\Application::getInstance()->showException($code, $message);
}

/**
 * @param \Exception $exception
 */
function commonExceptionHandler($exception)
{
    if (\Setup::$WRITE_EXCEPTIONS_TO_FILE) {
        $type = get_class($exception);

        $text = date('[Y.m.d H:i:s] ').t("Исключение")." - \"{$exception->getMessage()}\"\n";
        if (!empty($extra_info)) {
            $text .= t("Подробности").":{$extra_info}\n";
        }
        $text .= t("Код ошибки").":{$exception->getCode()}\n";
        $text .= t("Тип ошибки").":{$type}\n";
        $text .= t("Файл").":{$exception->getFile()}\n";
        $text .= t("Строка").":{$exception->getLine()}\n";
        $text .= t("Стек вызова").":{$exception->getTraceAsString()}\n\n";

        file_put_contents(\Setup::$PATH.\Setup::$EXCEPTIONS_FILE, $text, FILE_APPEND);
    }
    return \Setup::$DETAILED_EXCEPTION ? exceptionHandlerDevelop($exception) : exceptionHandler($exception);
}

/**
* Возвращает код статуса, который необходимо возвращать браузеру и публичный текст ошибки
* 
* @param Exception $exception
* @return array
*/
function getCodeMessage($exception)
{
    if ($exception instanceof \RS\Controller\ExceptionPageNotFound) {
        $code = 404;
        $message = $exception->getMessage();
    } else {
        $code = 503;
        $message = t('Извините, произошла ошибка. Сервис временно недоступен. Повторите попытку позже.');
    }
    return [$code, $message];
}

// Устанавливает обработчик исключений в соответствии с опцией $DETAILED_EXCEPTION в \Setup.
set_exception_handler('commonExceptionHandler');  