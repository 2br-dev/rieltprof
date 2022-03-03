<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\Comet;

use Main\Model\Orm\LongPollingEvent;
use RS\Application\Application;
use RS\Orm\Request;
use RS\Router\Manager;

/**
 * Класс, обеспечивающий работу очереди событий, мгновенно доставляемой авторизованному администратору.
 */
class LongPolling
{
    protected static $instance;
    private $is_enable = false;

    /**
     * SingleTon. Использовать LongPolling::getInstance() вместо конструктора
     */
    protected function __construct()
    {}

    /**
     * Возвращает общий экземпляр текущего класса
     *
     * @return LongPolling
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Включает Long polling для администратора на сайте
     *
     * @return void
     */
    public function enable()
    {
        $this->is_enable = true;
    }

    /**
     * Отключает long polling для администратора на сайте
     *
     * @return void
     */
    public function disable()
    {
        $this->is_enable = false;
    }

    /**
     * Возвращает true, если включен режим long polling
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->is_enable;
    }

    /**
     * Добавляет событие в стек для отдачи пользователю.
     * Сообщение будет передано при следующем запросе пользователя
     *
     * @param string $event_name
     * @param mixed $data данные
     * @param integer $user_id ID пользователя
     * @param integer $expire - время истечения актуальности события, в секундах
     */
    public function pushEvent($event_name, $data, $user_id, $expire)
    {
        $event = new LongPollingEvent();
        $event['user_id'] = $user_id;
        $event['date_create'] = date('c');
        $event['event_name'] = $event_name;
        $event['json_data'] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $event['expire'] = date('c', time() + $expire);
        $event->insert();

        return $event;
    }

    /**
     * Удаляет событие из стека
     *
     * @param $event
     * @param $data
     * @param $user_id
     * @param null $expire
     *
     * @return int
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    public function removeEvents($event_name, $data = null, $user_id = null)
    {
        $q = Request::make()
                ->delete()
                ->from(new LongPollingEvent());

        if (!$event_name) {
            throw new \RS\Exception(t('Не передано имя события'));
        }

        $where = [
            'event_name' => $event_name
        ];
        if ($data) {
            $where['json_data'] = json_encode($data);
        }
        if ($user_id) {
            $where['user_id'] = $user_id;
        }

        return  $q->where($where)->exec()->affectedRows();
    }

    /**
     * Удаляет все события, потерявшие актуальность
     *
     * @return integer возвращает количество удаленных событий
     */
    public function removeExpireEvents()
    {
        $q = Request::make()
            ->delete()
            ->from(new LongPollingEvent())
            ->where('expire <= NOW()');

        return  $q->exec()->affectedRows();
    }
}