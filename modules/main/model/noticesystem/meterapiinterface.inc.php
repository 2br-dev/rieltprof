<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\NoticeSystem;

/**
 * Интерфейс, содержащий функции по работе со счетчиками
 */
interface MeterApiInterface
{
    /**
     * Возвращает идентификатор счетчика
     *
     * @return string
     */
    function getMeterId();


    /**
     * Возвращает количество непросмотренных объектов
     *
     * @param integer|null $user_id
     * @return integer
     */
    function getUnviewedCounter();

    /**
     * Отмечает просмотренным один объект
     *
     * @param mixed $ids
     * @param integer|null $user_id
     * @return bool
     */
    function markAsViewed($ids);

    /**
     * Отмечает просмотренными все объекты
     *
     * @param integer|null $user_id
     * @return bool
     */
    function markAllAsViewed();

    /**
     * Удаляет сведения о просмотрах объектов
     * Возвращает оличество удаленных записей
     *
     * @return integer
     */
    function removeViewedFlag($ids);
}