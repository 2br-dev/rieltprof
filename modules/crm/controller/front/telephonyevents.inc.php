<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Crm\Controller\Front;

use Crm\Model\Log\LogTelephony;
use Crm\Model\Telephony\Manager;
use RS\Controller\ExceptionPageNotFound;
use RS\Controller\Front;

/**
 * Контроллер обрабатывает входящие сообщения от телефонии
 */
class TelephonyEvents extends Front
{
    /** @var LogTelephony */
    private $log;

    function init()
    {
        $this->log = LogTelephony::getInstance();
    }

    /**
     * Обрабатывает входящий запрос с событием от телефонии
     *
     */
    function actionIndex()
    {
        $this->wrapOutput(false);
        $this->writeLogHeader();

        try {
            $provider_id = $this->url->get('provider', TYPE_STRING);
            $secret = $this->url->get('secret', TYPE_STRING);
            $provider = Manager::getProviderById($provider_id);

            if ($secret !== $provider->getUrlSecret()) {
                $this->e404(t('Некорректный ключ'));
            }

            $call_event = $provider->onEvent($this->url);
            if ($call_event) {
                $result = Manager::registerCallEvent($call_event);
                if ($result === true) {
                    $this->log->write(t('Событие успешно зарегистрировано'), LogTelephony::LEVEL_INCOMING_REQUEST);
                } else {
                    $this->log->write(t('Событие не зарегистрировано. Ошибка: %error', ['error' => $result]), LogTelephony::LEVEL_INCOMING_REQUEST);
                }
            }
        } catch (\Throwable $e) {

            $log_text = t('Ошибка: ') . $e->getMessage();
            if (!($e instanceof ExceptionPageNotFound)) {
                $log_text .= t('Код ошибки: ') . $e->getCode();
                $log_text .= t('Файл: ') . $e->getFile();
                $log_text .= t('Строка: ') . $e->getLine();
                $log_text .= t('Стек вызова: ') . $e->getTraceAsString();
            }
            $this->log->write($log_text, LogTelephony::LEVEL_INCOMING_REQUEST);

            throw $e;

        }

        $this->log->write(t('Запрос успешно принят, ответ: %response', ['response' => $call_event->getReturnData()]), LogTelephony::LEVEL_INCOMING_REQUEST);

        return $call_event->getReturnData();
    }

    /**
     * Записывает сведения о начале запроса в лог файл
     * @throws \RS\Exception
     */
    private function writeLogHeader()
    {
        $log_text = t('Входящий запрос на URL: %url', ['url' => $this->url->getSelfUrl()]) . "\n";
        $log_text .= t('Данные из GET:') . "\n";
        foreach ($this->url->getSource(GET) as $key => $value) {
            $log_text .= $key . '=' . var_export($value, true) . "\n";
        }
        $log_text .= t('Данные из POST:') . "\n";
        foreach ($this->url->getSource(POST) as $key => $value) {
            $log_text .= $key . '=' . var_export($value, true) . "\n";
        }

        $this->log->write($log_text, LogTelephony::LEVEL_INCOMING_REQUEST);
    }
}
