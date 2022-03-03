<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExtCsv\Model\CsvPreset;

use \RS\Csv\Preset as OldPreset;

class PhotoBlock extends  \RS\Csv\Preset\AbstractPreset
{
    protected
        $root = PATH,
        $link_id_field,
        $link_preset_id,
        $range,         //Массив диапозона для обработки полей (Сколько одинаковых полей "Фото" включает)
        $type_item;
        
    /**
    * Конструктор класса
    * 
    * @param array $options - массив с опциями пресета
    * @return PhotoBlock
    */
    function __construct($options)
    {
       parent::__construct($options); 
       $this->range = range(0,9); //Диапозон выборки колонок с фото от 1 до 5
    }
        
    /**
    * Загружает из базы данные, необходимые для экспорта текущего набора колонок
    * 
    * @return void
    */        
    function loadData()
    {
        $ids = [];
        foreach($this->schema->rows as $row) {
            $ids[] = $row[$this->link_id_field];
        }
        $this->row = [];
        
        if ($ids) {
            $this->row = \RS\Orm\Request::make()
                ->from(new \Photo\Model\Orm\Image())
                ->whereIn('linkid', $ids)
                ->where([
                    'type' => $this->type_item
                ])
                ->orderby('sortn')
                ->objects(null, 'linkid', true);
        }
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
        $colums_num = $this->range;
        $columns    = [];
        for($i=0;$i<count($colums_num);$i++){
            $columns[$this->id.'-photo'.($i+1)] = [
                'key' => 'photo'.($i+1),
                'title' => t('Фото'.($i+1))
            ];
        }
        
        return $columns;
    }
    
    /**
    * Возвращает ассоциативный массив с несколькими строками данных, где ключ - это id колонки, 
    * а значение - это содержимое ячейки
    * 
    * @param integer $n - индекс в наборе строк $this->rows
    * @return array
    */
    function getColumnsData($n)
    {
        $id = $this->schema->rows[$n][$this->link_id_field];
        
        $values_array = [];
        for($i=0;$i<count($this->range);$i++){ //Проходимся по колонкам с фото
            $image          = isset($this->row[$id][$i]) ? $this->row[$id][$i] : [];
            if ($image){ //Если фото есть
               $values_array[$this->id.'-photo'.($i+1)] = trim($image->getOriginalUrl());//Запишем значения для колонок 
            }
            
        }
        return $values_array;
    }
    
    /**
    * Импортирует одну строку данных
    * 
    * @return void
    */    
    function importColumnsData()
    {
        /**
        * @var \Catalog\Model\Orm\Product $object
        */
        $object = $this->schema->getPreset($this->link_preset_id)->loadObject(); //Текущий найденный товар
        $id = $this->schema->getPreset($this->link_preset_id)->row[$this->link_id_field];

        for($i=0;$i<count($this->range);$i++){ //Проходимся по колонкам с фото
            if (isset($this->row['photo'.($i+1)])) {
                
                $api = new \Photo\Model\PhotoApi(); 
                $photo  = trim($this->row['photo'.($i+1)]); //Значение ячейки
                $is_url = strpos($photo, '://') !== false;
                $photo_path = $is_url ? $photo : $this->root.$photo;
                
                //Подгружим текущий объект, и посмотрим есть ли у него фото. Если есть, то ниже проверим на дубли по оригинальному имени
                if ($object && !isset($object['load_images'])){
                   $object['load_images'] = $api->getLinkedImages($object['id'], $this->type_item); 
                }

                if ($photo && ($is_url || file_exists($photo_path))) {
                    if ($object && !empty($object['load_images'])){ //Если объект уже существует, проверим, а было ли это фото ранее загружено
                        $found = false; //Флаг найденного совпадаения
                        foreach ($object['load_images'] as $image){
                            if ($image['filename']==basename($photo)){
                                $found = $image['filename'];
                                break;
                            }
                        }
                        if (!$found){ //Если совпадение не найдено, проверим следующее фото
                            $api->addFromUrl($photo_path, $this->type_item, $object['id'], true, null, false, true);
                        }
                    }else{
                        $api->addFromUrl($photo_path, $this->type_item, $id, true, null, false, true);
                    }
                }
            }
        }
    }
}

