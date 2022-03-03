<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Preset;

class Tags extends AbstractPreset
{
    protected
        $link_id_field,
        $link_preset_id,   
        $item,
        $delimiter = ',';
        
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
                ->from(new \Tags\Model\Orm\Link(),'L')
                ->whereIn('link_id', $ids)
                ->where([
                    'type' => $this->item
                ])
                ->join(new \Tags\Model\Orm\Word(),'L.word_id=W.id','W')
                ->objects(null, 'link_id', true);
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
    * @param string $item - строка с названием ORM объекта
    */
    function setItem($item)
    {
        $this->item = $item;
    }
    
    /**
    * Возвращает колонки, которые добавляются текущим набором 
    * 
    * @return array
    */
    function getColumns()
    {
        return [
            $this->id.'-tags' => [
                'key' => 'tags',
                'title' => t('Теги')
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
        $tag_words = isset($this->row[$id]) ? $this->row[$id] : [];
        $value = [];
        foreach($tag_words as $tag_words) {
            $value[] = $tag_words['word'];
        }
        
        return [
            $this->id.'-tags' => implode($value, $this->delimiter)
        ];
    }
    
    /**
    * Импортирует одну строку данных
    * 
    * @return void
    */    
    function importColumnsData()
    {
        
        if (!empty($this->row['tags'])) {
            
            $id   = $this->schema->getPreset($this->link_preset_id)->row[$this->link_id_field];
            $tags = $this->row['tags'];
            
            
            $api  = new \Tags\Model\Api();
            //Предварительно удаляем тэги
            $api->delByLinkAndType($id,$this->item);
          
            //Добавляет слова тегов в объект
            $api->addWords($tags,$this->item,$id);
            
        }
    }
}