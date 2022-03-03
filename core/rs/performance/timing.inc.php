<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Performance;

use RS\Router\Manager;
use RS\Router\Manager as RouterManager;

/**
 * Класс позволяет собирать и возвращать информацию о времени исполнения различных участков кода,
 * количестве и времени SQL запросах различных блоков.
 * Класс использует сессию для хранения данных.
 *
 * Важно учитывать: данный класс инстанцируется сразу после старта сессии, когда еще не активированы подписчики на события.
 * В некоторых случаях, пользователь на этом этапе еще будет не авторизован, так как Auth::init сработает позже и auth-ticket применится позже.
 * Работать с объектами (ORM), расширяемыми с помощью событий в данном класе нельзя. Допустимо использовать только
 * низкоуровневые обращения к БД и суперглобальным массивам.
 */
class Timing
{
    const SESSION_VAR = 'performance';
    /**
     * Кол-во страниц, по которым будут сохраняться данные
     */
    const PAGE_LIMIT = 10;

    /**
     * Замер начинается в классе AbstractSetup
     */
    const TYPE_INITIALIZE = 'initialize';

    /**
     * Замер происходит в RS\Controller\Front
     */
    const TYPE_CONTROLLER_FRONT = 'controller-front';

    /**
     * Замер происходит в Application\Block\Template
     */
    const TYPE_CONTROLLER_BLOCK = 'controller-block';

    /** Зарезервировано */
    const TYPE_OTHER = 'other';

    const SECTION_DATA = 'data';
    const SECTION_INFO = 'info';
    const SUBSECTION_INFO_DATE = 'date';
    const SUBSECTION_INFO_ABSOLUTE_URL = 'absolute_url';
    const SUBSECTION_INFO_TOTAL_TIME = 'total_time';
    const SUBSECTION_INFO_TOTAL_SQL_TIME = 'total_sql_time';
    const SUBSECTION_INFO_TOTAL_SQL_QUERIES = 'total_sql_queries';

    protected static $instance = [];
    protected $page_id;
    protected $current_measure = [];
    protected $enable;

    /**
     * Singleton, Получать экземпляр через ::getInstance()
     * 
     * @param integer $page_id Уникальный идентификатор страницы, строится на основе REQUEST_URI
     */
    protected function __construct($page_id)
    {
        $this->page_id = $page_id;
        $this->enable = \Setup::$ENABLE_DEBUG_PROFILING
                        && !defined('CLOUD_UNIQ')
                        && (!RouterManager::obj()->isAdminZone());
    }

    /**
     * Возвращает экземпляр 
     * 
     * @param null $page_id
     * @return self
     */
    public static function getInstance($page_id = null)
    {
        $page_id = $page_id ?? self::getCurrentPageId();

        if (!isset(self::$instance[$page_id])) {
            self::$instance[$page_id] = new self($page_id);
        }

        return self::$instance[$page_id];
    }

    /**
     * Возвращает уникальный идентификатор текущей страницы
     *
     * @return string
     */
    public static function getCurrentPageId()
    {
        return !empty($_SERVER['REQUEST_URI']) ? crc32($_SERVER['REQUEST_URI']) : false;
    }

    /**
     * Инициализирует данные для сохранения сведений о странице
     */
    public function initializePageInfo()
    {
        if (!isset($_SESSION[self::SESSION_VAR])
            || !is_array($_SESSION[self::SESSION_VAR])) {
            $_SESSION[self::SESSION_VAR] = [];
        }

        $_SESSION[self::SESSION_VAR][$this->page_id] = [
            self::SECTION_INFO => [
                self::SUBSECTION_INFO_ABSOLUTE_URL => !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : '',
                self::SUBSECTION_INFO_DATE => date('Y-m-d H:i:s'),
                self::SUBSECTION_INFO_TOTAL_TIME => 0,
                self::SUBSECTION_INFO_TOTAL_SQL_TIME => 0,
                self::SUBSECTION_INFO_TOTAL_SQL_QUERIES => 0
            ],
            self::SECTION_DATA => []
        ];
    }

    /**
     * Возвращает название этапа
     *
     * @param string $type
     * @param string $default
     * @return mixed|string
     */
    public function getTypeTitle($type, $default = '')
    {
        $reference = [
            self::TYPE_INITIALIZE => t('Инициализация'),
            self::TYPE_CONTROLLER_FRONT => t('Фронт-контроллер'),
            self::TYPE_CONTROLLER_BLOCK => t('Блок-контроллер'),
            self::TYPE_OTHER => t('Другое')

        ];

        return isset($reference[$type]) ? $reference[$type] : $default;
    }

    /**
     * Начинает замер времени выполнения и подсчет SQL запросов.
     * Замер времени для данного блока завершается, когда повторно вызывается данный
     * метод или когда вызывается метод endMeasure
     *
     * @param string $type Идентификатор из констант self::TYPE_
     * @param string $block_uniq Уникальный идентификатор или "-"
     * @param string $title Название блока замера времени
     * @param null $start_time
     */
    public function startMeasure($type, $block_uniq = '-', $title = '', $start_time = null)
    {
        if ($this->enable) {
            if (!$start_time) {
                $start_time = microtime(true);
            }
            $this->endMeasure();

            $_SESSION[self::SESSION_VAR]
                [$this->page_id]
                [self::SECTION_DATA]
                [$type]
                [$block_uniq] = [
                'title' => $this->getTypeTitle($type),
                'subtitle' => $title,
                'start_time' => $start_time,
                'end_time' => null,
                'duration_sec' => 0,
                'duration_sql_sec' => 0,
                'sql_queries' => []
            ];

            $this->current_measure = [
                'type' => $type,
                'block_uniq' => $block_uniq
            ];

            //Очищаем старые записи
            if (count($_SESSION[self::SESSION_VAR]) > self::PAGE_LIMIT) {
                $offset = count($_SESSION[self::SESSION_VAR]) - self::PAGE_LIMIT;

                $keys = array_keys($_SESSION[self::SESSION_VAR]);
                $values = $_SESSION[self::SESSION_VAR];
                $_SESSION[self::SESSION_VAR] = array_combine(
                    array_splice($keys, $offset),
                    array_splice($values, $offset)
                );
            }
        }
    }

    /**
     * Добавляет сведения по SQL запросу в лог
     *
     * @param $query
     * @param $duration_sec
     * @param $stack_trace
     */
    public function addSqlQueryToMeasure($query, $duration_sec, $stack_trace = [])
    {
        if ($this->current_measure) {
            $item = &$_SESSION[self::SESSION_VAR]
                [$this->page_id]
                [self::SECTION_DATA]
                [$this->current_measure['type']]
                [$this->current_measure['block_uniq']];

            if ($item) {
                $item['duration_sql_sec'] += $duration_sec;
                $item['sql_queries'][] = [
                    'query' => $query,
                    'duration_sec' => $duration_sec,
                    'stack_trace' => $stack_trace
                ];
            }
        }
    }

    /**
     * Завершаем замер времени выполненияи подсчет SQL
     *
     * @return void
     */
    public function endMeasure()
    {
        if ($this->current_measure) {

            $item = &$_SESSION[self::SESSION_VAR]
                [$this->page_id]
                [self::SECTION_DATA]
                [$this->current_measure['type']]
                [$this->current_measure['block_uniq']];

            if ($item) {
                $item['end_time'] = microtime(true);
                $item['duration_sec'] = $item['end_time'] - $item['start_time'];
            }

            $this->current_measure = null;
        }
    }

    /**
     * Возвращает отчет по одной странице
     *
     * @param string $sort поле для сортировки
     * @param bool $asc Направление сортировки true - по возрастанию, false - по убыванию
     * @param string|null $block_uniq - фильтровать по block_id конкретного контроллера. "-" - по фронт контроллеру
     * @return array | bool
     */
    public function getReport($sort, $asc = true, $block_uniq = null)
    {
        if (isset($_SESSION[self::SESSION_VAR][$this->page_id])) {
            $result = $this->prepareReport($sort, $asc, $block_uniq);

            //Сортируем
            if ($sort == 'time') {
                $field = 'duration_sec';
            } elseif ($sort == 'sql_time') {
                $field = 'duration_sql_sec';
            }

            if (isset($field)) {
                usort($result[self::SECTION_DATA], function ($a, $b) use ($field, $asc) {
                    $n = $asc ? 1 : -1;
                    return ($a[$field] < $b[$field]) ? -$n : $n;
                });
            }

            return $result;
        }

        return false;
    }

    /**
     * Добавляет суммарные сведения в отчет
     *
     * @param string $sort Поле для сортировки
     * @param bool $asc Направление сортировки по возрастанию
     * @param string | null $block_uniq Фильтр по контроллеру
     * @return array
     */
    protected function prepareReport($sort, $asc = true, $block_uniq = null)
    {
        $info = [
            self::SUBSECTION_INFO_TOTAL_TIME => 0,
            self::SUBSECTION_INFO_TOTAL_SQL_TIME => 0,
            self::SUBSECTION_INFO_TOTAL_SQL_QUERIES => 0
        ];

        $report_data = []; //Плосский список данных

        foreach($_SESSION[self::SESSION_VAR][$this->page_id][self::SECTION_DATA] as $type => $blocks) {
            foreach($blocks as $uniq => $data) {
                //фильтрация по block_uniq
                if ($block_uniq == '-' && $type != self::TYPE_CONTROLLER_FRONT) continue;
                if ($block_uniq !== null && $block_uniq != $uniq) continue;

                $info[self::SUBSECTION_INFO_TOTAL_TIME] += $data['duration_sec'];

                //Подсчитываем общее время SQL запросов и их количество
                foreach($data['sql_queries'] as $query_data) {
                    $info[self::SUBSECTION_INFO_TOTAL_SQL_TIME] += $query_data['duration_sec'];
                    $info[self::SUBSECTION_INFO_TOTAL_SQL_QUERIES] += 1;
                }

                if ($sort == 'time' || $sort == 'sql_time') {
                    usort($data['sql_queries'], function ($a, $b) use ($asc) {
                        $n = $asc ? 1 : -1;
                        return ($a['duration_sec'] < $b['duration_sec']) ? -$n : $n;
                    });
                }

                $report_data[] = $data + [
                    'type' => $type,
                    'uniq' => $uniq
                ];
            }
        }

        return [
                self::SECTION_INFO => $info + $_SESSION[self::SESSION_VAR][$this->page_id][self::SECTION_INFO],
                self::SECTION_DATA => $report_data
            ];
    }

    /**
     * Возвращает true, если включен сбор данных о времени выполнения
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->enable;
    }

    /**
     * Возвращает ссылку на страницу отчета по текущей странице
     *
     * @return string
     */
    public function getReportUrl()
    {
        return Manager::obj()
            ->getAdminUrl(false, ['page_id' => $this->page_id, 'do' => 'pageReport'], 'main-debug');
    }

    /**
     * Возвращает данные для построения диаграммы
     *
     * @return array
     */
    public function getPlotData()
    {
        $plot_data = [];
        if (isset($_SESSION[self::SESSION_VAR][$this->page_id][self::SECTION_DATA])) {
            foreach ($_SESSION[self::SESSION_VAR][$this->page_id][self::SECTION_DATA] as $type => $blocks) {
                foreach ($blocks as $uniq => $data) {
                    $plot_data[] = [
                        'label' => $data['title'] . ' ' . $data['subtitle'],
                        'data' => round($data['duration_sec'], 5),
                    ];
                }
            }
        }

        return $plot_data;
    }
}