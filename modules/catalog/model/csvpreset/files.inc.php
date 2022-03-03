<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvPreset;

use CustomIlluminator\Config\File;
use Files\Model\FileApi;
use RS\Config\Loader;
use RS\Csv\Preset\AbstractPreset;
use RS\Csv\Preset\Base;
use RS\Orm\Request;

class Files extends AbstractPreset
{
    const
        FILE_UPLOAD_TYPE_BOTH = 0,
        FILE_UPLOAD_TYPE_NEW = 1,
        FILE_UPLOAD_TYPE_OLD = 2;

    protected
        $link_id_field,
        $link_preset_id,
        $type_item;

    /**
     * Загружает из базы данные, необходимые для экспорта текущего набора колонок
     *
     * @return void
     */
    function loadData()
    {
        $this->row = Request::make()
            ->select('name, link_id, id')
            ->from(\Files\Model\Orm\File::_getTable())
            ->where([
                'link_type_class' => $this->type_item,
            ])
            ->exec()->fetchSelected('link_id', 'id', true);
    }


    /**
     * Устанавливает номер пресета, к которому линкуется текущий пресет
     *
     * @param integer $n - номер пресета
     * @return void
     */
    function setLinkPresetId($n)
    {
        $this->link_preset_id = $n;
    }

    /**
     * Устанавливает название поля id основного объекта
     *
     * @param string $id_field
     * @return void
     */
    function setLinkIdField($id_field)
    {
        $this->link_id_field = $id_field;
    }

    /**
     * Устанавливает тип привязки изображений к основному объекту
     *
     * @param mixed $type
     */
    function setTypeItem($type)
    {
        $this->type_item = $type;
    }

    /**
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    function getColumns()
    {
        return [
            $this->id.'-files' => [
                'key' => 'files',
                'title' => t('Файлы')
            ]
        ];
    }

    /**
     * Возвращает ассоциативный массив с одной строкой данных, где ключ - это id колонки, а значение - это содержимое ячейки
     *
     * @param integer $n - индекс в наборе строк $this->rows
     * @return array
     */
    function getColumnsData($n)
    {
        /** @var Base $base_preset */
        $id = $this->schema->rows[$n][$this->link_id_field];
        $files = $this->row[$id] ?? [];

        $result = [];
        foreach ($files as $file_id) {
            $file = new \Files\Model\Orm\File($file_id);
            $result[] = $file->getUrl(true);
        }

        return [
            $this->id.'-files' => implode(";\n", $result)
        ];
    }

    /**
     * Импортирует одну строку данных
     * @return void
     */
    function importColumnsData()
    {
        if (isset($this->row['docs']) && !empty($this->row['docs'])) {
            /** @var Base $base_preset */
            $base_preset = $this->schema->getPreset($this->link_preset_id);
            $api = new FileApi();
            $docs = $this->row['docs'];

            /** @var \Catalog\Model\Orm\Product $object*/
            $object = $base_preset->loadObject(); //Текущий найденный товар
            if ($object['id'] ?? false){
                $config = Loader::byModule($this);
                $docs_array = explode(';', $docs);

                $file_types = new \Files\Model\FilesType\CatalogProduct();
                $file_types->default_access_type = $config['csv_file_upload_access'];

                foreach ($docs_array as $index => $doc_url) {
                    $doc_name = basename($doc_url);
                    $doc_url = trim($doc_url);
                    if (!empty($doc_url)) {
                        // если нужно проверять какой файл сохранять
                        if ($config['csv_file_upload_type'] != self::FILE_UPLOAD_TYPE_BOTH) {
                            $files = Request::make()
                                ->select('id')
                                ->from(\Files\Model\Orm\File::_getTable())
                                ->where([
                                    'link_type_class' => strtolower($file_types->getShortName()),
                                    'link_id' => $object['id'],
                                    'name' => $doc_name,
                                ])
                                ->exec()->fetchSelected(null, 'id');

                            if (!empty($files)) {
                                // если надо оставить старый, то пропускаем
                                if ($config['csv_file_upload_type'] == self::FILE_UPLOAD_TYPE_OLD) {
                                    continue;
                                }
                                // если оставить новый, то удалим старый
                                if ($config['csv_file_upload_type'] == self::FILE_UPLOAD_TYPE_NEW) {
                                    $api->deleteFiles($files);
                                }
                            }
                        }

                        $file = $api->uploadFromUrl($doc_url, $file_types, $object['id'], basename($doc_url));
                    }
                }
            }
        }
    }
}
