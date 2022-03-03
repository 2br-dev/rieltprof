<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\NoticeSystem;

/**
 * Класс, отвечает за формирование счетчиков, отображащихся в админ. панели возле различных пунктов.
 * Уведомления считаются для каждого пользователя отдельно в рамках каждого сайта
 * Данный класс является хранилищем счетчиков для разных ключей.
 */
class Meter
{
    const
        CACHE_METER_TAG = 'meter';

    protected static
        $instance;

    protected
        $cache,
        $cache_key,
        $user_id,
        $last_calculate_timestamp,
        $recalculate_interval,
        $numbers = [];

    /**
     * Возвращает экземпляр
     *
     * @param integer|null $user_id ID пользователя
     * @return Meter
     */
    public static function getInstance($user_id = null, $site_id = null)
    {
        if ($user_id === null) {
            $user_id = \RS\Application\Auth::getCurrentUser()->id;
        }
        if ($site_id === null) {
            $site_id = \RS\Site\Manager::getSiteId();
        }
        if (!isset(self::$instance[$user_id])) {
            self::$instance[$user_id] = new self($user_id, $site_id);
        }
        return self::$instance[$user_id];
    }


    /**
     * Создавать экземпляр данного класса нужно через
     * статический метод ::getInstance($user_id)
     *
     * @param $user_id
     */
    protected function __construct($user_id, $site_id)
    {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->cache = \RS\Cache\Manager::obj();
        $this->cache_key = $this->cache->tags(self::CACHE_METER_TAG)->generateKey('meter'.$this->user_id.$this->site_id);
        $this->setCalculateInterval(\Setup::$METER_RECALCULATE_INTERVAL);
        $this->load();
    }

    /**
     * Возвращает число для заданного ключа
     *
     * @param string $key Ключ
     * @return array
     */
    public function getNumber($key)
    {
        if (isset($this->numbers[$key])) {
            return $this->numbers[$key];
        } else {
            return;
        }
    }

    /**
     * Возвращает полный массив ключей и чисел (счетчиков)
     *
     * @return array
     */
    public function getNumbers()
    {
        return $this->numbers;
    }

    /**
     * Возвращает время последнего пересчета счетчиков
     *
     * @return int
     */
    public function getLastCalculateTimestamp()
    {
        return $this->last_calculate_timestamp;
    }

    /**
     * Устанавливает интервал пересчета счетчиков
     *
     * @param integer $sec Количество секунд
     * @return void
     */
    public function setCalculateInterval($sec)
    {
        $this->recalculate_interval = $sec;
    }

    /**
     * Возвращает интервал обновления счетчиков
     *
     * @return int
     */
    public function getCalculateInterval()
    {
        return $this->recalculate_interval;
    }

    /**
     * Возвращает число секунд, через сколько нужно будет обновить счетчики
     *
     * @return int
     */
    public function getNextRecalculateInterval()
    {
        if (!$this->last_calculate_timestamp) {
            return 0;
        }

        $next_time = $this->last_calculate_timestamp + $this->getCalculateInterval() - time();
        return $next_time > 0 ? $next_time : 0;
    }

    /**
     * Производит пересчет всех счетчиков. Может занимать продолжительное время.
     *
     * @return void
     */
    public function recalculateNumbers()
    {
        //Любой модуль может вернуть свой счетчик
        $event_result = \RS\Event\Manager::fire('meter.recalculate', []);

        $this->numbers = [];
        $this->last_calculate_timestamp = time();
        $this->updateNumber($event_result->getResult());
    }

    /**
     * Обновляет один счетчик
     *
     * @param string | array $key
     * @param int $number
     */
    public function updateNumber($key, $number = null)
    {
        if (is_array($key)) {
            $this->numbers = array_merge($this->numbers, $key);
        } else {
            $this->numbers[$key] = $number;
        }

        $this->flush();
    }

    /**
     * Сохраняет текущее состояние на диск
     *
     * @return void
     */
    protected function flush()
    {
        $data = [
            'last_recalculate_time' => $this->last_calculate_timestamp,
            'numbers' => $this->numbers
        ];

        return $this->cache->write($this->cache_key, $data);
    }

    /**
     * Загружает текущее состояние счетчиков
     *
     * @return void
     */
    protected function load()
    {
        if ($this->cache->exists($this->cache_key)) {
            $data = $this->cache->read($this->cache_key);
            if ($data) {
                $this->last_calculate_timestamp = $data['last_recalculate_time'];
                $this->numbers = $data['numbers'];
            };
        }

        //Добавляем счетчик системных уведомлений ReadyScript
        //Данный счетчик всегда рассчитывается в режиме реального времени
        $internal_alerts = InternalAlerts::getInstance();
        $this->numbers[InternalAlerts::METER_KEY] = $internal_alerts->getCount();
    }

    /**
     * Возвращает URL, для запроса на рекалькуляцию счетчиков
     *
     * @return string
     */
    function getRecalculationUrl()
    {
        return \RS\Router\Manager::obj()->getAdminUrl(false, ["Act" => "RecalculateMeters"], false);
    }

}