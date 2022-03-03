<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType;

use Export\Model\Orm\ExportProfile;

/**
 * Интерфейс описывает поведение класса экспорта,
 * который может производить экспорт данных по API
 */
interface ExchangableInterface
{
    /**
     * Возвращает true, если профиль готов к экспорту по API, все необходимые данные заполнены.
     * Вызывается перед постановкой профиля в планировщик для обмена
     *
     * @return bool
     */
    public function validateDataForExchangeByApi();

    /**
     * Производит один шаг экспорта данных
     *
     * @param ExportProfile $profile Профиль экспорта, который следует экспортировать
     * @return integer | bool Возвращает количество экспортированных данных за текущий шаг или true,
     * если экспорт завершен полностью. В случае, если экспорт невозможно запустить, возвращает false
     */
    public function doExchange();

    /**
     * Возвращает true, если в настоящее время идет или запланирован обмен по API
     *
     * @return bool
     */
    public function isRunning();

    /**
     * Останавливает омен по API, сбрасывает планировщик, очищает файл очереди, снимает флаг необходимости
     *
     * @return bool
     */
    public function stopExchange();

    /**
     * Возвращает true, если профиль поддерживает хранение логов
     *
     * @return bool
     */
    public function canSaveLog();

    /**
     * Очищает лог файл
     *
     * @return bool
     */
    public function clearLog();

    /**
     * Возвращает содержимое лог-файла
     *
     * @return mixed
     */
    public function getLogContent();
}