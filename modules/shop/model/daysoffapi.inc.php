<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use RS\File\Tools as FileTools;

/**
 * Api выходных дней
 */
class DaysOffApi
{
    const BASE_URL = 'https://data.gov.ru/api/json/dataset/';
    const CALENDAR_ID = '7708660670-proizvcalendar';
    const CALENDAR_FILE = '/cache/daysoff/days_off.txt';
    const API_KEY = '0c77ac24929631fa067a75f179f99e71';
    const MINUTES_IN_DAY = 86400;

    protected static $months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

    /**
     * Возвращает временную метку даты, сдвинутой на указанное количество рабочих дней
     *
     * @param int $dela_days - величина сдвига в днях
     * @param null $date_from - метка времени или стрка с датой начала отсчёта, если не указана то текущая дата
     * @return int
     */
    public static function getShiftedDate($dela_days, $date_from = null)
    {
        $date_from = self::convertInputDate($date_from);
        $shift = 0;
        $shifted_date = $date_from;

        if (self::isDayOff($date_from)) {
            $dela_days++;
        }
        while ($shift < $dela_days) {
            $shifted_date += self::MINUTES_IN_DAY;
            if (!self::isDayOff($shifted_date)) {
                $shift++;
            }
        }

        return $shifted_date;
    }

    /**
     * Возвращает является указанный день выходным
     *
     * @param int|string $date - метка времени или стрка с датой, если не указана то текущая дата
     * @return bool
     */
    public static function isDayOff($date = null)
    {
        $date = self::convertInputDate($date);
        $days_off = self::getDaysOff();

        if ($days_off) {
            $year = date('Y', $date);
            $month = date('n', $date);
            $day = date('j', $date);
            return isset($days_off[$year][$month][$day]);
        } else {
            return date('N', $date) >= 6;
        }
    }

    /**
     * Конвертирует переданную дату во временную метку
     *
     * @param int|string $date - метка времени или стрка с датой
     * @return int
     */
    protected static function convertInputDate($date = null)
    {
        if (gettype($date) == 'string') {
            $date = strtotime($date);
        }
        if (gettype($date) != 'integer') {
            $date = time();
        }
        return $date;
    }

    /**
     * Возвращает производственный календарь, подготовленный для поиска
     *
     * @return array
     */
    protected static function getDaysOff()
    {
        static $days_off = null;

        if ($days_off === null) {
            $days_off = [];
            if ($raw_data = self::loadDaysOffData()) {
                foreach ($raw_data as $year_data) {
                    $year_id = $year_data['Год/Месяц'];
                    foreach (self::$months as $key => $month_name) {
                        $month_id = $key + 1;
                        foreach (explode(',', $year_data[$month_name]) as $day) {
                            if (strpos($day, '*') === false) {
                                $days_off[$year_id][$month_id][(int)$day] = true;
                            }
                        }
                    }
                }
            }
        }

        return $days_off;
    }

    /**
     * Возвращает производственный календарь, загруженный по api
     * Результат кэширует в файл
     *
     * @return array|null
     */
    protected static function loadDaysOffData()
    {
        $file_name = self::getCalendarFileName();

        if (file_exists($file_name)) {
            $content = @unserialize(file_get_contents($file_name));
            if (is_array($content)) {
                return $content;
            }
        }

        $url = self::BASE_URL . self::CALENDAR_ID . '/version/?access_token=' . self::API_KEY;
        if ($response = @file_get_contents($url)) {
            $versions = json_decode($response, true);
            if (isset($versions[0]['created'])) {
                $version = $versions[0]['created'];
                $url = self::BASE_URL . self::CALENDAR_ID . '/version/' . $version . '/content?access_token=' . self::API_KEY;
                if ($response = @file_get_contents($url)) {
                    $content = json_decode($response, true);
                    if (is_array($content)) {
                        FileTools::makePath($file_name, true);
                        file_put_contents($file_name, serialize($content));
                        return $content;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Возвращает путь к файлу кэша производственного календаря
     *
     * @return string
     */
    protected static function getCalendarFileName()
    {
        return \Setup::$PATH . \Setup::$STORAGE_DIR . self::CALENDAR_FILE;
    }
}
