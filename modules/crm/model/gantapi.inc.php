<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Model\Orm\Task;
use Crm\Model\Orm\TaskFilter;
use RS\Config\Loader;

/**
 * Класс содержит методы для работы с диаграммой ганта
 */
class GantApi
{
    const DATE_PRESET_WEEK = 'week';
    const DATE_PRESET_MONTH = 'month';
    const DATE_PRESET_YEAR = 'year';

    const VIEW_TYPE_TASK = 'task';
    const VIEW_TYPE_USER = 'user';

    protected $current_date_filter = [
        'from' => self::DATE_PRESET_WEEK,
        'to' => self::DATE_PRESET_WEEK
    ];

    protected $current_task_preset = 0;
    protected $current_view_type = self::VIEW_TYPE_TASK;

    /**
     * Возвращает набор пресетов для фильтра по дате
     *
     * @return array
     */
    public function getDateFilterPresets()
    {
        return [
            self::DATE_PRESET_WEEK => [
                'label' => t('Неделя'),
                'from' => $this->formatDate('Monday this week'),
                'to' => $this->formatDate('Sunday this week'),
            ],
            self::DATE_PRESET_MONTH => [
                'label' => t('Месяц'),
                'from' => $this->formatDate('first day of this month'),
                'to' => $this->formatDate('last day of this month'),
            ],
            self::DATE_PRESET_YEAR => [
                'label' => t('Год'),
                'from' => $this->formatDate('first day of january this year'),
                'to' => $this->formatDate('last day of december this year'),
            ],
        ];
    }

    /**
     * Возвращает текущие значения фильтра по дате
     *
     * @return []
     */
    public function getCurrentDateRange()
    {
        $ranges = $this->getDateFilterPresets();
        if (isset($ranges[  $this->current_date_filter['from'] ])) {
            return $ranges[  $this->current_date_filter['from'] ];
        } else {
            return [
                'label' => t('Диапазон'),
                'from' => $this->current_date_filter['from'],
                'to' => $this->current_date_filter['to'],
            ];
        }
    }

    /**
     * Возвращает дату в текстовом формате Y-m-d
     *
     * @param string $string - строковое представление даты
     * @return false|string
     */
    private function formatDate($string)
    {
        if ($string instanceof \DateTime) {
            return $string->format('Y-m-d');
        }
        return date('Y-m-d', strtotime($string));
    }

    /**
     * Устанавливает текущий фильтр по дате
     *
     * @param $date_from
     * @param $date_to
     */
    public function setDateFilter($date_from, $date_to)
    {
        $this->current_date_filter = [
            'from' => $date_from,
            'to' => $date_to
        ];
    }

    /**
     * Устанавливает вид отображения диаграммы.
     * В левой колонке будут Пользователи или Задачи
     *
     * @param $view_type
     */
    public function setViewType($view_type)
    {
        $this->current_view_type = $view_type;
    }

    /**
     * Возвращает полный список возможных отображений диаграммы
     *
     * @return array
     */
    public function getViewTypes()
    {
        return [
            self::VIEW_TYPE_TASK => t('График по задачам'),
            self::VIEW_TYPE_USER => t('График по пользователям')
        ];
    }

    /**
     * Устанавливает ID фильтра для задач
     *
     * @param integer $preset
     */
    public function setTaskPreset($preset)
    {
        $this->current_task_preset = $preset;
    }

    /**
     * Возращает подготовленные данные для отображения
     *
     * @return array
     */
    public function getChartData()
    {
        $task_api = new TaskApi();
        if ($this->current_task_preset) {
            $filter = new TaskFilter($this->current_task_preset);
            if ($filter['id']) {
                $task_api->applyFilter($filter);
            }
        }

        $date_range = $this->getCurrentDateRange();
        $days = $this->getRangeDates($date_range);
        $task_api->setFilter([
            [
                '' => [
                    'date_of_create:>' => $date_range['from'].' 00:00:00',
                    'date_of_create:<' => $date_range['to'].' 23:59:00',
                ],
                '|' => [
                    'date_of_planned_end:>' => $date_range['from'].' 00:00:00',
                    'date_of_planned_end:<' => $date_range['to'].' 23:59:59',
                ]
            ]
        ]);
        $task_api->setOrder('implementer_user_id, date_of_create');
        $tasks = $task_api->getList();
        $tasks = $this->addOffsetData($tasks, $date_range);
        $time_line = $this->getTimeLineData($date_range);

        $result = [
            'days' => $days,
            'tasks' => $tasks,
            'time_line' => $time_line
        ];

        $result = $this->groupByUsers($result);

        return $result;
    }

    /**
     * Трансформирует данные для отображения, с учетом группировки по исполнителю
     *
     * @param array $result
     * @return array
     */
    protected function groupByUsers($result)
    {
        $rows = [];
        $config = Loader::byModule($this);

        if ($this->current_view_type == self::VIEW_TYPE_USER) {
            foreach($result['tasks'] as $task) {
                $user_id = $task['implementer_user_id'] ?: 0;
                $rows[$user_id]['tasks'][] = $task;
            }

            //Пост-обработка
            $now_datetime = date('Y-m-d H:i:s');
            foreach($rows as $user_id => $row) {
                $completed_task = 0;
                $expired_task = 0;
                $total_task = count($row['tasks']);

                foreach ($row['tasks'] as $task) {
                    if (in_array($task['status_id'], $config['complete_task_statuses'])) {
                        $completed_task++;
                    }
                    if ($task['date_of_planned_end'] < $now_datetime
                        && !in_array($task['status_id'], (array)$config['cancel_task_statuses'])
                        && (!$task['date_of_end'] || $task['date_of_end'] > $task['date_of_planned_end'])) {
                        $expired_task++;
                    }
                }
                $rows[$user_id]['total_task'] = $total_task;
                $rows[$user_id]['completed_task'] = $completed_task;
                $rows[$user_id]['completed_task_percent'] = round($completed_task / $total_task * 100);
                $rows[$user_id]['expired_task'] = $expired_task;
                $rows[$user_id]['short_fio'] = $task->getImplementerUser()->getShortFio();
            }
        }

        $result['rows'] = $rows;
        return $result;
    }

    /**
     * Возращает сведения по линии текущего времени
     *
     * @param [] $date_range Фильтр по дате
     * @return array|bool(false)
     */
    protected function getTimeLineData($date_range) {
        $from = strtotime($date_range['from'].' 00:00:00') - 1;
        $to = strtotime($date_range['to'].' 23:59:59');
        $now = time();

        if ($from < $now && $now <= $to) {
            $day_start_second = $this->getDayOffsetSeconds($now);
            $start_offset = $day_start_second / 86400;

            $result = [
                'now' => $now,
                'column_start' => ceil(($now - $from) /  86400),
                'margin_start' => round($start_offset * 100, 4),
            ];

            return $result;
        }

        return false;
    }

    /**
     * Добавляет информацию о позиционировании внутри grid layout, используемой для отображения диаграммы.
     * Помимо смещения (измеряемое в колонках) к задаче добавляется еще смещение внутри первого дня и последнего
     * в виде готового значения, которое нужно удет добаить в margin-left и margin-right
     *
     * @param Task[] $tasks
     * @param [] $days
     *
     * @return Task[]
     */
    function addOffsetData($tasks, $date_range)
    {
        $from = strtotime($date_range['from'].' 00:00:00') - 1;
        $to = strtotime($date_range['to'].' 23:59:59');

        $range_width = ceil(($to - $from) / 86400) + 1;

        foreach($tasks as $task) {
            $tm_start = strtotime($task['date_of_create']);
            //Если у задачи не указан планируемы срок окончания, на диаграмме она будет +1 день от начала
            $tm_end = $task['date_of_planned_end'] ? strtotime($task['date_of_planned_end']) : $tm_start + 86400;
            $task['grid_column_start'] = ceil(($tm_start - $from) /  86400);
            $task['grid_column_end'] = ceil(($tm_end - $from) /  86400) + 1;

            //Рассчитываем смещение по времени в процентах, относительно начала дня
            $day_start_second = $this->getDayOffsetSeconds($tm_start);
            $day_end_second = $this->getDayOffsetSeconds($tm_end);
            $start_time_start_offset = $day_start_second / 86400; //Процент отступа от начала дня
            $start_time_end_offset = (86400 - $day_end_second) / 86400; //Процент отступа от конца дня

            $task['is_start_at_range'] = true;
            $task['is_end_at_range'] = true;

            if ($task['grid_column_start'] < 1) {
                $task['grid_column_start'] = 1;
                $task['grid_margin_start'] = 0;
                $task['is_start_at_range'] = false;
            }

            if ($task['grid_column_end'] > $range_width) {
                $task['grid_column_end'] = $range_width;
                $task['grid_margin_end'] = 0;
                $task['is_end_at_range'] = false;
            }

            //Ширина одного дня в процентах
            $day_width_percent = 100 / ($task['grid_column_end'] - $task['grid_column_start']);

            if ($task['is_start_at_range']) {
                $task['grid_margin_start'] = round($start_time_start_offset * $day_width_percent, 4);
            }
            if ($task['is_end_at_range']) {
                $task['grid_margin_end'] = round($start_time_end_offset * $day_width_percent, 4);
            }
        }

        return $tasks;
    }

    /**
     * Возвращает массив с днями для заданного диапазона
     *
     * @param array $date_range Массив с ключами from и to, означающими начало и конец видимого диапазона
     * @return array
     */
    function getRangeDates($date_range)
    {
        $days = [];
        $start = strtotime($date_range['from'].' 00:00:00');
        $end = strtotime($date_range['to'].' 23:59:59');
        $current = $start;
        while($current < $end) {
            $days[] = [
                'timestamp' => $current,
                'label' => date('d.m', $current),
                'is_weekend' => in_array(date('w', $current), [0, 6])
            ];
            $current += 86400;
        }

        return $days;
    }

    /**
     * Возвращает смещение времени относительно начала дня в секундах
     *
     * @param int $time timestamp
     * @return int
     */
    protected function getDayOffsetSeconds($time)
    {
        return (date('G', $time) * 3600 + date('i', $time) * 60 + date('s', $time));
    }
}