<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvPreset;

/**
* Добавляет к экспорту колонку для поля с сериализованными данными для фото комплектаций.
*/
class OfferPhotos extends \RS\Csv\Preset\AbstractPreset
{
    protected
        $line_delimiter = ";",
        $link_preset_id,
        $link_foreign_field,
        $link_foreign_named_field = "photos_named_arr",
        $link_orm_type,
        $title;
        
    function loadData()
    {
        $this->rows = $this->schema->rows;
    }
    
    /**
    * Устанавливает название колонки
    * 
    * @param string $title
    * @return void
    */
    function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
    * Определяет foreign key другого объекта
    * 
    * @param string $field
    * @return void
    */
    function setLinkForeignField($field)
    {
        $this->link_foreign_field = $field;
    }
    
    /**
    * Устанавливает номер пресета, к которому линкуется текущий пресет
    * 
    * @param integer $n - номер пресета
    * @return void
    */
    function setLinkPresetId($id)
    {
        $this->link_preset_id = $id;
    }
    
    
    /**
    * Возвращает данные для вывода в CSV
    * 
    * @return array
    */
    function getColumns()
    {
        return [
            $this->id.'-photos_arr' => [
                'key' => 'photos_arr',
                'title' => $this->title
            ]
        ];
    }
    
    /**
    * Возвращает данные для вывода в CSV
    * 
    * @return array
    */
    function getColumnsData($n)
    {
        $this->row = [];
        foreach($this->getColumns() as $id => $column) {
            $photos_ids = $this->schema->rows[$n][$this->link_foreign_field];
            $values_arr = [];
            if (count((array)$photos_ids)){ //Если есть назначенные фото
               $photos = \RS\Orm\Request::make()
                    ->from(new \Photo\Model\Orm\Image())
                    ->where([
                        'site_id' => \RS\Site\Manager::getSiteId(),
                    ])
                    ->whereIn('id',$photos_ids)
                    ->objects(null,'id');
               foreach($photos as $photo){
                  $values_arr[] = $photo['filename'];
               }     
            }
            
            
            $this->row[$id] = implode($this->line_delimiter, $values_arr);
        }
        return $this->row;
    }
    
    
    /**
    * Импортирует данные одной строки текущего пресета в базу
    * 
    * @return void
    */
    function importColumnsData()
    {
        if ($this->row['photos_arr']) {
            $photos = explode($this->line_delimiter, $this->row[$this->link_foreign_field]);
            
            $linked_preset = $this->schema->getPreset($this->link_preset_id);
            $linked_preset->row[$this->link_foreign_named_field] = $photos;
        }
    }
}