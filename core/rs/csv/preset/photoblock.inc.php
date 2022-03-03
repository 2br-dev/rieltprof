<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Preset;

class PhotoBlock extends AbstractPreset
{
    protected
        $root = PATH,
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
        return [
            $this->id.'-photos' => [
                'key' => 'photos',
                'title' => t('Фотографии')
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
        $id = $this->schema->rows[$n][$this->link_id_field];
        $images = isset($this->row[$id]) ? $this->row[$id] : [];
        $value = [];
        foreach($images as $image) {
            $value[] = $image->getOriginalUrl();
        }
        return [
            $this->id.'-photos' => implode(";\n", $value)
        ];
    }
    
    /**
    * Импортирует одну строку данных
    * 
    * @return void
    */    
    function importColumnsData()
    {
        if (isset($this->row['photos'])) {
            $id = $this->schema->getPreset($this->link_preset_id)->row[$this->link_id_field];
            $api = new \Photo\Model\PhotoApi(); 
            $photos = $this->row['photos'];
            //Подгружим текущий объект, и посмотрим есть ли у него фото. Если есть, то ниже проверим на дубли по оригинальному имени
            /**
            * @var \Catalog\Model\Orm\Product
            */
            $object = $this->schema->getPreset($this->link_preset_id)->loadObject(); //Текущий найденный товар
            if ($object){
               $images = $api->getLinkedImages($object['id'], $this->type_item);
               $id = $object['id'];
            }
            foreach(explode(";", $photos) as $photo) {
                $photo = trim($photo);
                $is_url = strpos($photo, '://') !== false;
                $photo_path = $is_url ? $photo : $this->root.$photo;
                
                if ($photo && ($is_url || file_exists($photo_path))) {
                    if ($object && !empty($images)){ //Если объект уже существует, проверим, а было ли это фото ранее загружено
                        $found = false; //Флаг найденного совпадаения
                        foreach ($images as $image){
                            if ($image['filename']==basename($photo)){
                                $found = $image['filename'];
                                break;
                            }
                        }
                        if ($found){ //Если совпадение найдено, проверим следующее фото
                            continue;
                        }
                    }
                    //Если объект новый, то не проверяем ранее разгруженное, а просто сразу ложим в базу, копируя
                    $api->addFromUrl($photo_path, $this->type_item, $id, true, null, false, true);
                    
                }
            }
        }
    }
}
