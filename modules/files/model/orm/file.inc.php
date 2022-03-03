<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model\Orm;
use RS\File\Tools;
use \RS\Orm\Type,
    \Files\Model\FileApi;

/**
 * ORM объект - Прикрепленный файл
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $servername Имя файла на сервере
 * @property string $name Название файла
 * @property string $description Описание
 * @property string $size Размер файла
 * @property string $mime Mime тип файла
 * @property string $access Уровень доступа
 * @property integer $sortn Порядковый номер
 * @property string $link_type_class Класс типа связываемых объектов
 * @property integer $link_id ID связанного объекта
 * @property string $xml_id Идентификатор в сторонней системе
 * @property string $uniq Уникальный идентификатор
 * @property string $uniq_name Уникальное название файла (url-имя)
 * --\--
 */
class File extends \RS\Orm\OrmObject
{
    protected static
        $table = 'files';
    
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'servername' => new Type\Varchar([
                'description' => t('Имя файла на сервере'),
                'maxLength' => 50,
                'visible' => false
            ]),
            'name' => new Type\Varchar([
                'description' => t('Название файла'),
                'checker' => ['chkEmpty', t('Название файла не может быть пустым')]
            ]),
            'description' => new Type\Text([
                'description' => t('Описание')
            ]),
            'size' => new Type\Varchar([
                'description' => t('Размер файла'),
                'visible' => false
            ]),
            'mime' => new Type\Varchar([
                'description' => t('Mime тип файла'),
                'visible' => false
            ]),
            'access' => new Type\Varchar([
                'description' => t('Уровень доступа'),
                'visible' => false,
                'index' => true
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Порядковый номер'),
                'visible' => false
            ]),
            'link_type_class' => new Type\Varchar([
                'description' => t('Класс типа связываемых объектов'),
                'visible' => false,
                'maxLength' => 100
            ]),
            'link_id' => new Type\Integer([
                'description' => t('ID связанного объекта'),
                'visible' => false
            ]),
            'xml_id' => new Type\Varchar([
                'description' => t('Идентификатор в сторонней системе'),
                'visible' => false,
                'unique' => true
            ]),
            'uniq' => new Type\Varchar([
                'description' => t('Уникальный идентификатор'),
                'visible' => false,
                'maxLength' => 32,
                'unique' => true
            ]),
            'uniq_name' => new Type\Varchar([
                'description' => t('Уникальное название файла (url-имя)'),
                'visible' => false,
                'unique' => true
            ])
        ]);
        
        $this->addIndex(['servername', 'link_type_class', 'link_id'], self::INDEX_UNIQUE);
    }
    
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['uniq'] = self::generateUniq();            
            $this['sortn'] = \RS\Orm\Request::make()
                ->select('MAX(sortn)+1 as next_sort')
                ->from($this)
                ->where([
                    'link_id' => $this['link_id'],
                    'link_type_class' => $this['link_type_class']
                ])
                ->exec()->getOneField('next_sort', 0);
        }
        
        $this['uniq_name'] = $this->generateUniqName();
    }
    
    /**
    * Генерирует уникальный 32 байтный идентификатор для файла
    * 
    * @return string
    */
    public static function generateUniq()
    {
        return md5(uniqid(rand(), true));
    }
    
    /**
    * Возвращает алиас для файла, чье имя гарантировано не занято
    * 
    * @param string $filename
    * @return string
    */
    private function generateUniqName()
    {
        list($name, $ext) = \RS\File\Tools::parseFileName($this['name']);
        if ($name == '') $name = 'noname';
        $i = 0;
        do {
            $postfix = $i>0 ? '-'.$i : '';
            $uniq_name = \RS\Helper\Transliteration::str2url($name).$postfix.$ext;
            
            $count = \RS\Orm\Request::make()
                ->from($this)
                ->where(['uniq_name' => $uniq_name])
                ->count();
            $i++;
        } while($count>0);
        
        return $uniq_name;
    }
    
    /**
    * Возвращает URL для скачивания файла
    * 
    * @return string
    */
    function getUrl($absolute = false)
    {
        return \RS\Router\Manager::obj()->getUrl('files-front-download', [
            'uniq_name' => $this['uniq_name']
        ], $absolute);
    }
    
    /**
    * Возвращает URL для скачивания файла администратором
    * 
    * @param bool $absolute
    * @return string
    */
    function getAdminDownloadUrl($absolute = false)
    {
        return \RS\Router\Manager::obj()->getAdminUrl(false, ['uniq' => $this['uniq']], 'files-download', $absolute);
    }
    
    /**
    * Возвращает путь к файлу на сервере
    * 
    * @return string
    */
    function getServerPath()
    {
        return \Files\Model\FileApi::getStoragePath().'/'.$this['servername'];
    }
    
    /**
    * Возвращает объект типа связи
    * 
    * @return \Files\Model\FilesType\AbstractType
    */
    function getLinkType()
    {
        static 
            $cache_type = [];
        
        $type = $this['link_type_class'];
        if (!isset($cache_type[$type])) {
            $cache_type[$type] = FileApi::getTypeClassInstance($type);
        }
        return $cache_type[$type];
    }

    /**
     * Возвращает размер файла в отформатированном виде
     *
     * @return string
     */
    function getSizeStr()
    {
        return Tools::fileSizeToStr($this['size']);
    }
    
    /**
    * Удаляет файл
    * 
    * @return bool
    */
    function delete()
    {        
        if ($result = parent::delete()) {
            $remain = \RS\Orm\Request::make()
                        ->from($this)
                        ->where([
                            'servername' => $this['servername']]
                        )->count();
                        
            if ($remain == 0) { 
                //Удаляем физически файл, только если на него не осталось ссылок
                $filename = $this->getServerPath();
                if (file_exists($filename))
                    unlink($filename);
            }
        }
        return $result;
    }
}
