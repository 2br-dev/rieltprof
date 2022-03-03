<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\NoticeSystem;
use RS\Event\Manager as EventManager;
use \RS\Router\Manager as RouterManager;

/**
 * Класс отвечает за внутренние уведомления ReadyScript
 * К внутренним уведомлениям относятся:
 * - уведомления о Trial периоде
 * - уведомления о достижении лимитов тарифа в облаке
 * - уведомления о наличии обновлений
 */
class InternalAlerts
{
    const
        METER_KEY = 'rs-notice',

        STATUS_WARNING = 'warning',
        STATUS_CRITICAL = 'critical';

    private
        $messages = [];

    private static
        $instance;

    /**
     * Возвращает экземпляр текущего класса (Singleton)
     *
     * @return InternalAlerts
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * InternalAlerts constructor.
     */
    function __construct()
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
        //Получаем системные уведомления
        if ($notice = __GET_ADMIN_NOTICE()) {
            $href = defined('CLOUD_UNIQ') ? null : RouterManager::obj()->getAdminUrl(false, [], 'main-license');
            $this->addMessage($notice, $href);
        }
        if (function_exists('__GET_PACKAGE_NOTICE') && $notice = __GET_PACKAGE_NOTICE()) {
            $href = RouterManager::obj()->getAdminUrl(false, [], 'modcontrol-control');
            $this->addMessage($notice, $href);
        }

        EventManager::fire('internalalerts.get', [
            'internal_alerts' => $this
        ]);
    }

    /*
     * Добавляет уведомление к списку
     *
     * @param string $message Сообщение
     * @param string|null $href Ссылка. Если null, то сообщение будет не кликабельным
     * @param string|null $target Значение для атрибута target ссылки
     * @param string $status Статус сообщения (предупреждение или критическое). см. константы: STATUS_WARNING или STATUS_CRITICAL
     * @param string $description Пояснение
     * @param array $close_button_data массив с параметрами для кнопки "закрыть уведомление". Допустимые ключи:
     * - url - ссылка, которая будет вызвана AjAX'ом
     * - class - CSS класс
     * @return void
     */
    public function addMessage($message,
                               $href = null,
                               $target = null,
                               $status = self::STATUS_WARNING,
                               $description = '',
                               $close_button_data = [])
    {
        $this->messages[] = [
            'message' => $message,
            'href' => $href,
            'target' => $target,
            'status' =>$status,
            'description' => $description,
            'close' => $close_button_data
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
     * Возвращает количество системных уведомлений
     *
     * @return int
     */
    public function getCount()
    {
        return count($this->messages);
    }

    /**
     * Возвращает статус наиболее критичного уведомления
     *
     * @return string см. константы: STATUS_WARNING или STATUS_CRITICAL
     */
    public function getStatus()
    {
        foreach($this->messages as $message) {
            if ($message['status'] == self::STATUS_CRITICAL)
                return self::STATUS_CRITICAL;
        }
        return self::STATUS_WARNING;
    }
}
