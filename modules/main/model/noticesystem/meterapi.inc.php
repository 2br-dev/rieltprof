<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\NoticeSystem;

/**
 * Класс предоставляет API для управления счетчиком просмотра одного объекта
 */
class MeterApi implements MeterApiInterface
{
    private
        $readed_items_api,
        $orm_object,
        $user_id,
        $site_id,
        $meter_id;

    /**
     * MeterApi constructor.
     *
     * @param \RS\Orm\AbstractObject $orm_object ORM объект, с которым будет работать счетчик
     * @param string $meter_id ID счетчика
     * @param integer|null $site_id ID текущего сайта. Если не указан, то будет взят из системы
     * @param integer|null $user_id ID текущего пользователя. Если не указан, то будет взят из системы
     */
    function __construct(\RS\Orm\AbstractObject $orm_object, $meter_id, $site_id = null, $user_id = null)
    {
        $this->orm_object = $orm_object;
        $this->meter_id   = $meter_id;
        $this->site_id    = $site_id ?: \RS\Site\Manager::getSiteId();
        $this->user_id    = $user_id ?: \RS\Application\Auth::getCurrentUser()->id;

        $this->readed_items_api = new ReadedItemApi($this->site_id, $this->user_id);
        $this->readed_items_api->setDefaultEntity($this->meter_id);
    }

    /**
     * Возвращает идентификатор счетчика
     *
     * @return string
     */
    function getMeterId()
    {
        return $this->meter_id;
    }

    /**
     * Возвращает количество непросмотренных объектов
     *
     * @param integer|null $user_id
     * @return integer
     */
    function getUnviewedCounter()
    {
        return $this->readed_items_api->getUnreadCount($this->orm_object);
    }


    /**
     * Отмечает просмотренным один объект
     *
     * @param mixed $ids
     * @return integer
     */
    function markAsViewed($ids)
    {
        $this->readed_items_api->markAsReaded($ids);

        //Получаем новый счетчик непросмотренных объектов
        $new_counter = $this->getUnviewedCounter();

        //Обновляем хранилище счетчиков на сервере
        $meters = \Main\Model\NoticeSystem\Meter::getInstance();
        $meters->updateNumber($this->getMeterId(), $new_counter);

        return $new_counter;
    }

    /**
     * Отмечает просмотренными все объекты
     *
     * @param integer|null $user_id
     * @return integer
     */
    function markAllAsViewed()
    {
        $this->readed_items_api->markAllAsReaded($this->orm_object, null, 'site_id', false);

        //Обновляем счетчик на сервере
        $meters = \Main\Model\NoticeSystem\Meter::getInstance();
        $meters->updateNumber($this->getMeterId(), 0);

        return 0;
    }

    /**
     * Удаляет сведения о просмотрах объектов
     *
     * @return integer
     */
    function removeViewedFlag($ids)
    {
        $this->readed_items_api->removeReadFlag($ids);

        $new_counter = $this->getUnviewedCounter();

        //Обновляем счетчик на сервере
        $meters = \Main\Model\NoticeSystem\Meter::getInstance();
        $meters->updateNumber($this->getMeterId(), $new_counter);

        return $new_counter;
    }
}