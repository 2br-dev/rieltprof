<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\CsvPreset;

use Catalog\Model\Orm\Dir;
use RS\Csv\Preset\AbstractPreset;

/**
 * Пресет для экспорта данных (полный url товара)
 */
class DirUrl extends AbstractPreset
{
    protected $title;

    /**
     * Устанавливает название экспортной колонки
     *
     * @param mixed $title
     */
    function setTitle($title)
    {
        $this->title = $title;
    }

    function getColumns()
    {
        return [
            $this->id . '-dirurl' => [
                'key' => 'dirurl',
                'title' => $this->title
            ]
        ];
    }

    /**
     * Получает данные для колонки
     *
     * @param integer $n - номер колонки
     * @return array
     */
    function getColumnsData($n)
    {
        /** @var Dir $dir */
        $dir = $this->schema->rows[$n];

        return [$this->id . '-dirurl' => $dir->getUrl(true)];
    }

    /**
     * Пустой метод, т.к. в импорте не участвует поле, только в экспорте
     *
     */
    function importColumnsData()
    {
    }
}
