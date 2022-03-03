<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\NoticeSystem;
use RS\Event\Manager as EventManager;
use RS\Http\Request;

/**
 * Класс отвечает за отображение общих системных уведомлений,
 * которые отображаются в шапке административной панели и их
 * невозможно скрыть навсегда, только на некоторый срок.
 *
 * Днный класс можно использовать только для особо важных уведомлений,
 * например, связанных с безопасностью.
 *
 * Через данный инструмент отображаются сообщения:
 * - о необходимости продления подписки на обновление
 * - об отсутствии свободного места на диске в облаке для обновления
 * - о необходимости обновить версию PHP
 */
class VisibleAlerts
{
    const
        HIDE_BLOCK_TIME = 1209600, //В секундах, время скрытия
        COOKIE_SHOW_KEY = 'visible_alerts';

    private
        $messages = [];

    private static
        $instance;

    /**
     * Возвращает экземпляр текущего класса (Singleton)
     *
     * @return self
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * VisibleAlerts constructor.
     */
    protected function __construct()
    {
        $this->init();
    }

    /**
     * Инициализирует системные уведомления
     * Вызывает событие internalalerts.get для получения системных уведомлений
     *
     * @return void
     */
    public function init()
    {
        EventManager::fire('visiblealerts.get', [
            'visible_alerts' => $this
        ]);
    }

    /*
     * Добавляет уведомление к списку
     *
     * @param string $message Сообщение
     * @param string|null $href Ссылка. Если null, то сообщение будет не кликабельным
     * @param string|null $target Значение для атрибута target ссылки
     * @return void
     */
    public function addMessage($message,
                               $href = null,
                               $target = null,
                               $link_title = null)
    {
        $this->messages[] = [
            'message' => $message,
            'href' => $href,
            'target' => $target,
            'link_title' => $link_title ?: t('Подробнее')
        ];
    }

    /**
     * Возвращает список системных уведомлений
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Удаляет все сообщения
     */
    public function cleanMessages()
    {
        $this->messages = [];
    }

    /**
     * Возвращает количество системных уведомлений
     *
     * @return int
     */
    public function getCount()
    {
        return count($this->messages);
    }

    /**
     * Возвращает хэш сообщений
     *
     * @return string
     */
    public function getMessagesHash()
    {
        return md5(serialize($this->messages));
    }

    /**
     * Возвращает true, если следует показать блок с уведомлениями
     *
     * @return bool
     */
    public function canShow()
    {
        if (!$this->messages) {
            return false; //Нет сообщений, не показываем блок
        }

        $request = Request::commonInstance();
        //Исключаем отображение на некоторых маршрутах
        $module_controller = $request->get('mod_controller', TYPE_STRING);
        if (in_array($module_controller, [
                'mobilesiteapp-appctrl',
                'marketplace-ctrl']))
        {
            return false;
        }

        $cookie = $request->cookie(self::COOKIE_SHOW_KEY, TYPE_STRING);
        if ($cookie && preg_match('/^(.*)_(.*)$/', $cookie, $match)) {
            //Если текст сообщение изменится, то оно заново должно появиться, несмотря на закрытие
            if ($match[1] == $this->getMessagesHash() && time() - $match[2] < self::HIDE_BLOCK_TIME ) {
                return false;
            }
        }

        return true;
    }
}