<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;
use \Photo\Model\Orm\Image as Picture;
use Photo\Model\Stub;
use RS\Orm\Request;

class Image extends AbstractType
{
    public
        $property = [
            'preview' => [
                'width' => 200,
                'height' => 200,
                'scale' => 'xy'
            ]
    ];
        
    protected
        $can_modificate_query = true,
        $width,
        $height,
        $scale,
        $body_template = 'system/admin/html_elements/table/coltype/image.tpl';
    
    function __construct($field, $title = null, $width, $height, $scale = 'xy', $property = null)
    {
        parent::__construct($field, $title, $property);        
        $this->width = $width;
        $this->height = $height;
        $this->scale = $scale;
    }
    
    /**
    * Устанавливает параметры фото для предварительного просмотра
    * 
    * @param array $width_height_scale
    *
    * @return Image
    */
    function setPreview(array $width_height_scale)
    {
        $this->property['preview'] = $width_height_scale;
        return $this;
    }
    
    /**
    * Возвращает url изображения 
    * @return string
    */
    function getImageSrc()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            $value = reset($value);
        }

        if ($value instanceof Picture) {
            return $value->getUrl($this->width, $this->height, $this->scale);
        }
        elseif ($value && $this->row['__'.$this->field] instanceof \RS\Orm\Type\Image) {
            return $this->row['__'.$this->field]->getUrl($this->width, $this->height, $this->scale);
        } else {
            $stub = new Stub();
            return $stub->getUrl($this->width, $this->height);
        }
    }
    
    /**
    * Возвращает url увеличенного изображения. Если у записи нет фото, то возвращается пустая строка.
    * 
    * @return string
    */
    function getPreviewUrl()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            $value = reset($value);
        }

        if ($value instanceof Picture) {
            return $value->getUrl($this->property['preview']['width'], $this->property['preview']['height'], $this->property['preview']['scale']);
        } 
        elseif ($value && $this->row['__'.$this->field] instanceof \RS\Orm\Type\Image) {
            return $this->row['__'.$this->field]->getUrl($this->property['preview']['width'], $this->property['preview']['height'], $this->property['preview']['scale']);
        } 
        else {
            return '';
        }
    }

    /**
     * Модифицирует запрос для установки сортировки
     * @param Request $q
     * @return void
     */
    function modificateSortQuery(Request $q)
    {
        if (!$q->issetTable(new Picture)) {
            $q->leftjoin(new Picture, "Image.linkid=A.id AND type='catalog'", 'Image');
        }

        $q->groupby('A.id');

        if($this->property['CurrentSort']=='ASC')
            $q->orderby('Image.id IS NOT NULL');
        else
            $q ->orderby('Image.id IS  NULL');
    }
}