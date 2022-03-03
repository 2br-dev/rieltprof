<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;

use Main\Model\Comet\LongPolling;
use Main\Model\Comet\LongPollingLoop;
use RS\Config\Loader;
use RS\Controller\Admin\Front;

/**
 * Контроллер, который обеспечивает отдачу сообщений о событиях.
 * Контроллер обеспечивает online соединение с браузером и возможность мгновенной
 * отправки событий в браузер администратору
 */
class LongPollingGate extends Front
{
    const REMOVE_OLD_EVENTS_PROBABILITY_PERCENT = 30; //В 30 случаях из 100 запросов будет очищаться мусор из таблицы

    /**
     * Держит соединение с браузером, пока не произойдет событие или не наступит timeout
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     */
    function actionIndex()
    {
        $last_id = $this->url->get('last_id', TYPE_INTEGER, 0);

        $config = Loader::byModule($this);
        $timeout = $config->long_polling_timeout_sec;
        $request_interval = $config->long_polling_event_listen_interval_sec;
        $user_id = $this->user->id;

        //Закрываем сессию, чтобы не блокировать другие AJAX запросы
        session_write_close();

        //Удаляем старые записи
        if (rand(0,100) < self::REMOVE_OLD_EVENTS_PROBABILITY_PERCENT) {
            LongPolling::getInstance()->removeExpireEvents();
        }

        //Запускаем петлю ожидания событий
        $events = LongPollingLoop::listenData($timeout, $request_interval, $user_id, $last_id);

        if ($events === false) {
            $this->result
                ->setSuccess(false);
        } else {
            $this->result
                ->setSuccess(true)
                ->addSection('events', $events)
                ->addSection('last_id', LongPollingLoop::getLastId());
        }

        return $this->result;
    }
}