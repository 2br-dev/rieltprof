<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\NoticeSystem;

/**
 * Интерфейс сообщает, что класс поддерживает учет просмотров одного объекта
 * и готов вернуть API для работы со счетчиками. Данный интерфейс следует применять у DAO(EntityList) классов,
 * если в Crud контроллере требуется поддержка отображения просмотренных/непросмотренных объектов
 */
interface HasMeterInterface
{
    /**
     * Возвращает API по работе со счетчиками
     *
     * @return \Main\Model\NoticeSystem\MeterApiInterface
     */
    function getMeterApi();
}