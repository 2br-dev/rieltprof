<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\Comet;

use Main\Model\Orm\LongPollingEvent;
use RS\Orm\Request;

/**
 * Класс обеспечивает работу механизма выборки сообщений для системы обмена сообщениями LongPolling
 */
class LongPollingLoop
{
    /**
     * Опрашивает бзу данных до появления в ней сообщений, которые следует передать в браузер
     *
     * @param integer $timeout количество секунд которое нужно опрашивать базу
     * @param integer $request_interval интервал запросов к БД в секундах
     * @param integer $user_id ID пользователя
     * @param integer $last_id
     * @return array|bool(false)
     * [
     *      'event_name' => 'ID события',
     *      'event_data' => 'Параметры события'
     * ]
     */
    public static function listenData($timeout, $request_interval, $user_id, $last_id)
    {
        $query = Request::make()
                    ->from(new LongPollingEvent())
                    ->where('expire > NOW()')
                    ->where([
                        'user_id' => $user_id
                    ])
                    ->where("id > #last_id", [
                        'last_id' => (int)$last_id
                    ]);

        $start_time = time();
        while(time()-$start_time < $timeout) {
            $events = $query->objects();
            if ($events) {
                //Удаляем из базы, а затем возвращаем записи
                $result = [];
                foreach($events as $event) {
                    $result[] = [
                        'event_name' => $event['event_name'],
                        'event_data' => json_decode($event['json_data'])
                    ];
                }
                return $result;
            }

            sleep($request_interval);
        }
        return false;
    }

    /**
     * Возвращает ID последнего события в очереди
     *
     * @return integer
     */
    public static function getLastId()
    {
        return Request::make()
            ->select('MAX(id) as max_id')
            ->from(new LongPollingEvent())
            ->exec()->getOneField('max_id', 0);
    }
}