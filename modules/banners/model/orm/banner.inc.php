<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Banners\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название баннера
 * @property string $file Баннер
 * @property integer $use_original_file Использовать оригинал файла для вставки
 * @property string $link Ссылка
 * @property integer $targetblank Открывать ссылку в новом окне
 * @property string $info Дополнительная информация
 * @property integer $public Публичный
 * @property array $xzone Связанные зоны (удерживая CTRL можно выбирать несколько зон)
 * @property integer $weight Вес от 1 до 100
 * @property string $use_schedule Использовать показ по расписанию?
 * @property string $date_start Дата начала показа
 * @property string $date_end Дата окончания показа
 * --\--
 */
class Banner extends OrmObject
{
    protected static
        $table = 'banner';
    
    public static
        $src_folder = '/storage/banners/original',
        $dst_folder = '/storage/banners/resized';    
    
    function _init()
    {        
        parent::_init()->append([
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'description' => t('Название баннера')
                ]),
                'file' => new Type\File([
                    'description' => t('Баннер'),
                    'storage' => [\Setup::$ROOT, \Setup::$FOLDER . static::$src_folder],
                    'template' => '%banners%/form/banner/file.tpl'
                ]),
                'use_original_file' => new Type\Integer([
                    'description' => t('Использовать оригинал файла для вставки'),
                    'checkboxView' => [1, 0]
                ]),
                'link' => new Type\Varchar([
                    'description' => t('Ссылка')
                ]),
                'targetblank' => new Type\Integer([
                    'description' => t('Открывать ссылку в новом окне'),
                    'checkboxView' => [1, 0]
                ]),
                'info' => new Type\Text([
                    'description' => t('Дополнительная информация')
                ]),
                'public' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Публичный'),
                    'checkboxView' => [1, 0]
                ]),
                'xzone' => new Type\ArrayList([
                    'description' => t('Связанные зоны (удерживая CTRL можно выбирать несколько зон)'),
                    'list' => [['\Banners\Model\ZoneApi', 'staticAdminSelectList']],
                    'attr' => [[
                        'size' => 10,
                        'multiple' => 'multiple'
                    ]]
                ]),
                'weight' => new Type\Integer([
                    'description' => t('Вес от 1 до 100'),
                    'default' => 100,
                    'hint' => t('Чем больше вес, тем больше вероятность того, что баннер будет показан в случае конкуренции')
                ]),
            t('Расписание'),
                'use_schedule' => new Type\Varchar([
                    'description' => t('Использовать показ по расписанию?'),
                    'checkboxview' => [1, 0],
                    'default' => 0,
                    'checker' => [['\Banners\Model\Orm\Banner', 'staticUseScheduleCheck']],
                    'template' => '%banners%/form/banner/use_schedule.tpl'
                ]),
                'date_start' => new Type\Datetime([
                    'description' => t('Дата начала показа'),
                    'visible' => false,
                ]),
                'date_end' => new Type\Datetime([
                    'description' => t('Дата окончания показа'),
                    'visible' => false,
                ])
        ]);
    }

    /**
     * Проверяем правильно ли установлено рассписание
     *
     * @param Banner $orm - сам объект баннера
     * @return boolean
     */
    public static function staticUseScheduleCheck(Banner $orm)
    {
        if (!$orm['use_schedule']) {
            return true;
        }
        return (!$orm['date_start'] || !$orm['date_end']) ? t('Укажите правильно даты начала и окончания показа по расписанию') : true;
    }

    /**
    * Возвращает отладочные действия, которые можно произвести с объектом
    * 
    * @return \RS\Debug\Action\AbstractAction[]
    */    
    function getDebugActions()
    {
        return [
            new \RS\Debug\Action\Edit(\RS\Router\Manager::obj()->getAdminPattern('edit', [':id' => '{id}'], 'banners-ctrl')),
            new \RS\Debug\Action\Delete(\RS\Router\Manager::obj()->getAdminPattern('del', [':chk[]' => '{id}'], 'banners-ctrl'))
        ];
    }
    
    /**
    * Функция срабатывает после записи объекта
    * 
    * @param string $flag - флаг обозначающий какое действие выполняется. insert или update
    */
    function afterWrite($flag)
    {
        if ($this->isModified('xzone')) {
            OrmRequest::make()
                ->delete()
                ->from(new Xzone())
                ->where([
                    'banner_id' => $this['id']
                ])->exec();
            
            if ($this['xzone']) {
                foreach($this['xzone'] as $zone_id) {
                    if ($zone_id>0) {
                        $link = new Xzone();
                        $link['banner_id'] = $this['id'];
                        $link['zone_id'] = $zone_id;
                        $link->insert();
                    }
                }
            }
        }
    }

    /**
     * Возвращает клонированный объект баннера
     *
     * @return Banner
     */
    function cloneSelf()
    {
        $this->fillZones();
        $clone = parent::cloneSelf();

        //Клонируем фото, если нужно
        if ($clone['file']) {
            /** @var Type\Image $image_field */
            $image_field = $clone['__file'];
            $clone['file'] = $image_field->addFromUrl($image_field->getFullPath());
        }
        return $clone;
    }

    function fillZones()
    {
        if ($this['xzone'] === null) {
            $this['xzone'] = OrmRequest::make()
                ->from(new Xzone())
                ->where([
                    'banner_id' => $this['id']
                ])->exec()->fetchSelected(null, 'zone_id');
        }
    }
    
    function delete()
    {
        if ($result = parent::delete()) {
            OrmRequest::make()
                ->from(new Xzone())
                ->where([
                    'banner_id' => $this['id']
                ])->exec();
        }
        return $result;
    }
    
    /**
    * Возвращает путь к оригиналу файла 
    * 
    * @param bool $absolute Если true, то возвращает абсолютный путь, иначе возвращает относительный
    * @return string
    */
    function getOriginalUrl($absolute = false)
    {
        /**
        * @var \RS\Orm\Type\File
        */
        $link = $this['__file']->getLink();
        return $absolute ? \RS\Site\Manager::getSite()->getAbsoluteUrl($link) : $link;
    }
    
    /**
    * Возвращает путь к изображению с заданными размерами
    * 
    * @param integer $width - ширина изображения
    * @param integer $height - высота изображения
    * @param string $scale - тип масштабирования (xy|cxy|axy)
    * @param bool $absolute - если задано true, то будет возвращен абсолютный путь
    * @return string
    */
    function getImageUrl($width, $height, $scale = 'xy', $absolute = false)
    {
        //Пользуемся общей системой отображения картинок этой CMS.
        $img = new \RS\Img\Core(\Setup::$ROOT, \Setup::$FOLDER.static::$src_folder, \Setup::$FOLDER.static::$dst_folder);
        return $img->getImageUrl($this['__file']->getRealPath(), $width, $height, $scale, $absolute);
    }
    
    /**
    * Возвращает true, если файл баннера является файлом форматов jpg, gif, png
    * 
    * @return bool
    */
    function isImageFile()
    {
        $filename = $this['__file']->getRealPath();
        list($name, $ext) = \RS\File\Tools::parseFileName($filename, true);
        return in_array(strtolower($ext), ['jpg', 'gif', 'png']);
    }
    
    /**
    * Возвращает ссылку на сформированное изображение или на оригинал файла, 
    * в зависимости от опции баннера
    * 
    * @return string
    */
    function getBannerUrl($width = null, $height = null, $scale = 'xy', $absolute = false)
    {
        if ($this['use_original_file'] || !$width || !$height) {
            return $this->getOriginalUrl($absolute);
        } else {
            return $this->getImageUrl($width, $height, $scale, $absolute);
        }
    }

    /**
     * Возвращает одну строку из описания к баннеру
     *
     * @param integer $n - номер строки
     * @return string
     */
    function getInfoLine($n)
    {
        $lines = explode("\n", $this['info']);
        return isset($lines[$n]) ? trim($lines[$n]) : '';
    }
}
