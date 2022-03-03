<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Helper;

use RS\File\Tools as FileTools;

/**
* Класс позволяет сохранять отладочную информацию в файл
*/
class Log
{
    private static $instance = [];
    
    private $filename;
    private $enable = true;

    /**
     * Максимальный размер файла в байтах
     *
     * @var int
     */
    private $max_length = 0;

    /**
     * Нужно ли добавлять дату в начало строки
     *
     * @var bool
     */
    private $append_date = false;

    
    /**
    * Конструктор класса. Инициализоровать класс следует 
    * через статический конструктор: Log::file(...)->
    * 
    * @param string $filename полный путь к log-файлу на диске
    * @param bool $htaccess_protected Если true, то в директории с лог файлом будет создан .htaccess файл, запрещающий доступ ко всем файлам данной директории извне
    */
    protected function __construct($filename, $htaccess_protected = false)
    {
        $this->filename = $filename;
        $dir = dirname($this->filename);
        if (!is_dir($dir)) {
            FileTools::makePath($dir); //Создаем директорию, при необходимости
        }
        if ($htaccess_protected) {
            FileTools::makePrivateDir($dir);
        }
    }

    /**
     * Статический конструктор.
     *
     * @param string $filename полный путь к log-файлу на диске
     * @param bool $htaccess_protected Если true, то в директории с лог файлом будет создан .htaccess файл, запрещающий доступ ко всем файлам данной директории извне
     * @return Log
     */
    public static function file($filename, $htaccess_protected = false)
    {
        if (!isset(self::$instance[$filename])) {
            self::$instance[$filename] = new self($filename, $htaccess_protected);
        }
        return self::$instance[$filename];
    }

    /**
     * Дополняет лог файл сообщением $data
     *
     * @param string $data - текст для логирования
     * @return Log
     */
    public function append($data)
    {
        if (!$this->enable) {
            return $this;
        }

        if ($this->append_date) {
            $data = date('[Y-m-d H:i:s] ') . $data;
        }

        file_put_contents($this->filename, $data . "\n", FILE_APPEND);
        clearstatcache();
        $size = filesize($this->filename);

        if ($this->max_length > 0 && $size > $this->max_length) {
            $content = file_get_contents($this->filename);
            $begin = $size - (int)(0.75 * $this->max_length);
            $new_content = substr($content, $begin);
            file_put_contents($this->filename, $new_content);
        }

        return $this;
    }

    /**
     * Вставка символов новой строки в лог файл
     *
     * @param int $count
     */
    public function newLine($count = 1)
    {
        file_put_contents($this->filename, str_repeat("\n", $count), FILE_APPEND);
    }
    
    /**
    * Очищает лог файл
    * @return Log
    */
    public function clean()
    {
        file_put_contents($this->filename, '');
        return $this;
    }
    
    /**
    * Удаляет лог файл
    * @return Log
    */
    public function remove()
    {
        @unlink($this->filename);
        return $this;
    }

    /**
     * Устанавливает максимальный размер файла
     *
     * @param int $value размер файла в байтах
     * @return $this
     */
    public function setMaxLength($value)
    {
        $this->max_length = $value;
        return $this;
    }

    /**
     * Включает добавление даты в начало строки
     *
     * @return $this
     */
    public function enableDate()
    {
        $this->append_date = true;
        return $this;
    }

    /**
     * Включает/выключает запись логов. Если передать true, то метод append не будет записывать лог
     *
     * @param $bool
     * @return $this;
     */
    public function setEnable($bool)
    {
        $this->enable = $bool;
        return $this;
    }
}
