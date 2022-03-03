<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace RS\Log;

use RS\Helper\Tools;

/**
 * Класс, обеспечивающий чтение лог-файлов
 */
class LogReader
{
    /** @var resource|null */
    protected $file;
    /** @var string */
    protected $buffer;
    /** @var int */
    protected $records_count;
    /** @var int */
    protected $filtered_records_count;
    /** @var int */
    protected $record_from;
    /** @var int */
    protected $record_to;
    /** @var string */
    protected $date_from;
    /** @var string */
    protected $date_to;
    /** @var string */
    protected $time_from;
    /** @var string */
    protected $time_to;
    /** @var string[] */
    protected $levels;
    /** @var string */
    protected $text;

    /**
     * Открывает файл и перемещает курсор на первую запись, начинающуюся с даты
     *
     * @param string $filename
     * @return bool
     */
    public function openFile(string $filename): bool
    {
        $this->file = fopen($filename, 'r');
        do {
            $this->buffer = fgets($this->file);
        } while ($this->buffer !== false && !preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \[[^\]]+\] /', $this->buffer));

        $this->records_count = 0;
        $this->filtered_records_count = 0;

        return true;
    }

    /**
     * Возвращает одну запись лога.
     *
     * Считаем, что запись лога заканчивается когда либо заканчивается файл,
     * либо встречаем маску даты новой записи.
     *
     * @return \Generator
     */
    public function readRecord()
    {
        while ($this->buffer !== false) {
            $line_data = [];
            do {
                $line_data[] = $this->buffer;
                $this->buffer = fgets($this->file);
            } while ($this->buffer !== false && !preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \[[^\]]+\] /', $this->buffer));
            $line = implode("", $line_data);
            $line = Tools::toEntityString($line);
            preg_match('/^\[(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})\] \[([^\]]+)\] (.*)/sm', $line, $matches);
            $record = [
                'date' => $matches[1],
                'time' => $matches[2],
                'level' => $matches[3],
                'text' => trim($matches[4], "\n"),
            ];

            $this->records_count++;

            if (!$this->isMatchesFilters($record)) {
                continue;
            }

            $this->filtered_records_count++;

            if (!$this->isMatchesPagination($record)) {
                continue;
            }

            yield $record;
        }
    }

    /**
     * Проверяет запись на соответствие фильтрам
     *
     * @param array $record
     * @return bool
     */
    protected function isMatchesFilters(array $record)
    {
        $datetime = $record['date'] . ' ' . $record['time'];

        if (!empty($this->date_from)) {
            $date_from = $this->date_from . ' ' . $this->time_from;
            if ($datetime < $date_from) {
                return false;
            }
        }
        if (!empty($this->date_to)) {
            $date_to = $this->date_to . ' ' . $this->time_to;
            if ($datetime > $date_to) {
                return false;
            }
        }

        if (!empty($this->levels) && !in_array($record['level'], $this->levels)) {
            return false;
        }

        if (!empty($this->text) && strpos($record['text'], $this->text) === false) {
            return false;
        }

        return true;
    }

    /**
     * Проверяет запись на соответствие пагинации
     *
     * @param array $record
     * @return bool
     */
    protected function isMatchesPagination(array $record)
    {
        return $this->getRecordsCount() >= $this->record_from && $this->getRecordsCount() <= $this->record_to;
    }

    /**
     * Возвращает количество записей в лог-файле
     *
     * @return int
     */
    public function getRecordsCount(): int
    {
        return $this->records_count;
    }

    /**
     * Возвращает количество записей в лог-файле, соответствующих фильтрам
     *
     * @return int
     */
    public function getFilteredRecordsCount(): int
    {
        return $this->filtered_records_count;
    }

    /**
     * Устанавливает пагинацию
     *
     * @param int $page Номер страницы, начиная с 1
     * @param int $page_size Количество элементов на страницу
     */
    public function setPagination(int $page, int $page_size): void
    {
        $this->record_from = (($page - 1) * $page_size) +1;
        $this->record_to = $page * $page_size;
    }

    /**
     * Устанавливает, от какой даты отображать записи
     *
     * @param string $date_from
     */
    public function setDateFrom(string $date_from): void
    {
        $this->date_from = $date_from;
    }

    /**
     * Устанавливает, до какой даты отображать записи
     *
     * @param string $date_to
     */
    public function setDateTo(string $date_to): void
    {
        $this->date_to = $date_to;
    }

    /**
     * Устанавливает от какого времени отображать записи
     *
     * @param string $time_from
     */
    public function setTimeFrom(string $time_from): void
    {
        $this->time_from = $time_from;
    }

    /**
     * устанавливает до какого времени отображать записи
     *
     * @param string $time_to
     */
    public function setTimeTo(string $time_to): void
    {
        $this->time_to = $time_to;
    }

    /**
     * Устанавливает записи с какими уровнями логирования нужно отображать
     *
     * @param string[] $levels
     */
    public function setLevels(array $levels): void
    {
        $this->levels = $levels;
    }

    /**
     * Устанавливает строку, которые должны включать записи для отображения
     *
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
