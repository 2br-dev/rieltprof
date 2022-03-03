<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model;

use RS\File\Tools;

/**
 * Класс позволяет создавать буфер из данных, а затем читать его порциями.
 * По достижении конца буфера, он удаляется. Класс сохраняет свое состояние и начинает каждый следующий fetch с предыдущей позиции
 */
class Fetcher
{
    private $list_filepath;
    private $pos_filepath;
    private $position = 0;
    private $fp;


    /**
     * Конструктор
     *
     * @param string $filepath путь ко временному файлу без расширения
     */
    function __construct($filepath)
    {
        $this->list_filepath = $filepath.'.tmp';
        $this->pos_filepath = $filepath.'_pos.tmp';
        Tools::makePath($this->list_filepath, true);

    }

    /**
     * Инициаизирует список данных
     *
     * @param array $list
     */
    public function initialize(array $list)
    {
        if ($this->fp) {
            fclose($this->fp);
            $this->fp = null;
        }
        file_put_contents($this->list_filepath, implode("\n", $list));
        file_put_contents($this->pos_filepath, 0);
        $this->position = 0;
    }

    /**
     * Возвращает следующую запись в списке
     *
     * @return string | null
     */
    public function fetch()
    {
        if (!$this->issetBuffer()) return null;

        if (!$this->fp) {
            $this->position = file_get_contents($this->pos_filepath);

            $this->fp = fopen($this->list_filepath, 'r');
            $this->seek($this->position);
        }

        while((!feof($this->fp) && $value = fgets($this->fp))) {
            return trim($value);
        }

        $this->finish();
        return null;
    }

    /**
     * Перемещает указатель текущей позиции в файле
     *
     * @param $position
     */
    public function seek($position)
    {
        if ($this->fp) {
            fseek($this->fp, $position);
        }
    }

    /**
     * Возвращает $count количество элементов из буфера
     *
     * @param integer $count - Количество записей, которое нужно вернуть
     * @return array
     */
    public function fetchList($count)
    {
        $result = [];
        $i = 0;
        while($value = $this->fetch()) {
            $result[] = $value;

            $i++;
            if ($i == $count) {
                $this->savePosition();
                return $result;
            }
        }
        return $result;
    }

    /**
     * Очищает буфер
     *
     * @return void
     */
    public function finish()
    {
        fclose($this->fp);
        $this->fp = null;

        @unlink($this->list_filepath);
        @unlink($this->pos_filepath);
    }

    /**
     * Возвращает true, если в настоящее время существует буфер для
     *
     * @return bool
     */
    public function issetBuffer()
    {
        return file_exists($this->list_filepath);
    }

    /**
     * Сохраняет текущую позицию в буфере чтения
     *
     * @return void
     */
    public function savePosition()
    {
        $this->position = ftell($this->fp);
        file_put_contents($this->pos_filepath, $this->position);
    }
}