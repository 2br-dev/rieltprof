<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Helper\Debug;

/**
 * Класс помогает отлаживать время выполнения между различными точками
 */
class Time
{
    private static
        $time_points = [];

    /**
     * Устанавливает временную метку для начала отсчета
     *
     * @param string|null $point_name Идентификатор временной метки
     * @return void
     */
    public static function setTimePoint($point_name = null)
    {
        self::$time_points[(string)$point_name] = microtime(true);
    }

    /**
     * Возвращает разницу во времени, прошедшего с момента установки или обновления временной метки $point_name
     *
     * @param null $point_name
     * @param bool $update_time_point
     */
    public static function getDeltaTime($point_name = null, $update_time_point = true)
    {
        if (!isset(self::$time_points[$point_name])) {
            return false;
        }

        $time = microtime(true);
        $before_time = self::$time_points[$point_name];

        if ($update_time_point) {
            self::setTimePoint($point_name);
        }

        return round($time - $before_time, 6);

    }
}