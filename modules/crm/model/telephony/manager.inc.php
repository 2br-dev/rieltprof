<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Crm\Model\Telephony;

use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\Telephony\Provider\AbstractProvider;
use RS\Config\Loader;
use RS\Event\Manager as EventManager;
use RS\Exception;
use RS\File\Tools;
use RS\Orm\Request;

/**
 * Класс содержит методы для работы с провайдерами телефонии
 */
class Manager
{
    const FILTER_NO = '';
    const FILTER_ONLY_WITH_TEST = 'with-test';
    const FILTER_ONLY_WITH_CALLING = 'with-calling';
    /**
     * Возвращает список объектов провайдеров телефонии, зарегистрированных в системе
     *
     * @param bool $cache
     * @return AbstractProvider[]
     * @throws Exception
     */
    public static function getProviders($cache = true)
    {
        static $result;

        if (!$cache || $result === null) {
            $result = [];
            $event_result = EventManager::fire('crm.telephony.getproviders', []);
            foreach($event_result->getResult() as $item) {
                if ($item instanceof AbstractProvider) {
                    $result[$item->getId()] = $item;
                } else {
                    throw new Exception(t('Провайдер телефонии должен быть наследником Crm\Model\Telephony\Provider\AbstractProvider'));
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает список названий провайдеров телефонии
     *
     * @param string $filter
     * @param array $first
     * @param bool $cache
     * @return array
     * @throws Exception
     */
    public static function getProvidersTitles($filter = self::FILTER_NO, $first = [], $cache = true)
    {
        $providers = self::getProviders($cache);
        $result = [];
        foreach($providers as $id => $provider) {
            switch($filter) {
                case self::FILTER_ONLY_WITH_TEST:
                    if ($provider->getEventTestObject() !== null) {
                        $result[$id] = $provider->getTitle();
                    }
                    break;
                case self::FILTER_ONLY_WITH_CALLING:
                    if ($provider->canCalling()) {
                        $result[$id] = $provider->getTitle();
                    }
                    break;
                default:
                    $result[$id] = $provider->getTitle();
            }

        }
        return $first + $result;
    }

    /**
     * Возвращает провайдера по ID
     *
     * @param $provider_id
     * @param bool $cache
     * @return AbstractProvider
     * @throws Exception
     */
    public static function getProviderById($provider_id, $cache = true)
    {
        $providers = self::getProviders($cache);
        if (isset($providers[$provider_id])) {
            return $providers[$provider_id];
        } else {
            throw new Exception(t('Не найден провайдер телефонии с идентификатором `%id`', ['id' => $provider_id]));
        }
    }

    /**
     * Регистрирует в системе входящее событие от телефонии
     *
     * @param CallEvent $call_event
     * @return bool(true) | string
     */
    public static function registerCallEvent(CallEvent $call_event)
    {
        $call_history_item = Request::make()
            ->from(new CallHistory())
            ->where([
                'call_id' => $call_event->getCallIdWithProvider()
            ])->object();

        if (!$call_history_item) {
            $call_history_item = new CallHistory();
        }

        $call_history_item->fillFromCallEvent($call_event);
        if (Loader::byModule(__CLASS__)->tel_enable_call_notification) {
            $call_history_item['send_to_browser'] = true;
        }
        $call_history_item['is_closed'] = 0;

        if ($call_history_item->getProvider()->isInternalCall($call_history_item)) {
            return t('Это внутренний вызов, пропущено');
        }

        if ($call_history_item['id']) {
            $result = $call_history_item->update();
        } else {
            $result = $call_history_item->insert();
        }

        if (!$result) {
            $result = $call_history_item->getErrorsStr();
        }

        return $result;
    }

    /**
     * Возвращает сообщения, которые в настоящий момент должны отображаться у администратора
     *
     * @param $user_id
     * @return array
     */
    public static function getCurrentUserMessages($user_id, $cache = true)
    {
        $messages = [];
        $where = [];
        foreach(self::getProviders() as $provider) {
            $extension_id = $provider->getExtensionIdByUserId($user_id);
            if ($extension_id) {
                $where[] = "(provider = '{$provider->getId()}' AND call_flow = 'in' AND called_number = '{$extension_id}')";
                $where[] = "(provider = '{$provider->getId()}' AND call_flow = 'out' AND caller_number = '{$extension_id}')";
            }
        }

        if ($where) {
            $q = Request::make()
                ->from(new CallHistory())
                ->whereIn('call_status', [
                    CallHistory::CALL_STATUS_CALLING,
                    CallHistory::CALL_STATUS_ANSWER
                ])
                ->where([
                    'is_closed' => 0
                ])
                ->where(implode(' OR ', $where))
                ->limit(20);

            $call_events = $q->objects();

            foreach($call_events as $call_history_object) {
                $messages[] = $call_history_object->buildMessage();
            }
        }
        return $messages;
    }

    /**
     * Возвращает ссылку для совершения исходящего звонка на номер $phone
     *
     * @return string
     */
    public static function getCallUrl($phone)
    {
        return \RS\Router\Manager::obj()->getAdminUrl('calling', ['number' => $phone], 'crm-callactions');
    }

    /**
     * Возвращает занимаемое место на диске Записями разговоров
     *
     * @return integer
     */
    public static function getRecordsSize($format = true)
    {
        $path = AbstractProvider::getRecordLocalBaseDir();
        $recIterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS);
        $objects = new \RecursiveIteratorIterator($recIterator);

        $total_bytes = 0;

        foreach($objects as $object) {
            if ($object->isFile() && !$object->isLink()) {
                if ($object->getFilename() == '.htaccess') continue;
                $total_bytes += $object->getSize();
            }
        }

        return $format ? Tools::fileSizeToStr($total_bytes) : $total_bytes;
    }

    /**
     * Удаляет локально сохраненные файлы с записями разговоров для всех провайдеров телефонии
     *
     * @param bool $delete_all Флаг - удалять все.
     * @param string $delete_before_date - дата, ранее которой нужно все удалить, если $delete_all = false
     * @param bool $delete_call_history_link - очищать ссылку на запись у звонка
     * @param int $timeout
     *
     * @return bool | integer Возвращает true, в случае полного завершения удаления, иначе - число (количество удаленных записей)
     * Возвращает false, если недостаточно прав на удаление
     */
    public static function deleteCallRecords($delete_all,
                                             $delete_before_date,
                                             $timeout = 20)
    {
        $counter = 0;
        $start_time = microtime(true);
        $delete_before_date_tm = strtotime($delete_before_date);

        $path = AbstractProvider::getRecordLocalBaseDir();
        $recIterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
        $items = new \RecursiveIteratorIterator($recIterator, \RecursiveIteratorIterator::CHILD_FIRST);

        $call_history = new CallHistory();
        $q = Request::make()
            ->update($call_history)
            ->set(['record_id' => '']);

        foreach($items as $item) {

            if ($item->isFile()) {

                if ($delete_all || $item->getMTime() < $delete_before_date_tm) {

                    @unlink($item->getRealPath());

                    $q->where = '';
                    $q->where([
                        'record_id' => $item->getFilename()
                    ])->exec();

                    $counter++;
                }
            } else {
                if ($delete_all) {
                    @rmdir($item->getRealPath());
                }
            }

            if (microtime(true)- $start_time > $timeout) return $counter;
        }

        return true;
    }
}
