<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photogalleries\Model\Orm;
use \RS\Orm\Type;
 
/**
 * Класс ORM-объектов "Страница альбома".
 * Наследуется от объекта \RS\Orm\OrmObject, у которого объявлено свойство id
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название альбома
 * @property string $alias URL имя
 * @property integer $public Публичный
 * @property integer $sortn Сорт. индекс
 * --\--
 */
class Album extends \RS\Orm\OrmObject
{
    const
        IMAGES_TYPE = 'photoalbum';
    
    protected static
        $table = 'photogalleries_album'; //Имя таблицы в БД
         
    /**
    * Инициализирует свойства ORM объекта
    *
    * @return void
    */
    function _init()
    {        
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),  
            'title' => new Type\Varchar([
                'maxLength'   => '250',
                'description' => t('Название альбома'),
                'checker' => ['chkEmpty', t('Укажите название альбома')],
                'attr' => [[
                    'data-autotranslit' => 'alias'
                ]]
            ]),
            'alias' => new Type\Varchar([
                'description' => t('URL имя'),
                'checker' => ['chkEmpty', t('Укажите идентификатор альбома')],
                'meVisible' => false
            ]),
            'public' => new Type\Integer([
                'maxLength' => 1,
                'index' => true,
                'default' => 1,
                'description' => t('Публичный'),
                'checkboxView' => [1,0]
            ]),
            'sortn' => new Type\Integer([
                'maxLength' => '11',
                'allowEmpty' => true,
                'description' => t('Сорт. индекс'),
                'visible' => false,
            ]),
            '_tmpid' => new Type\Hidden([
                        'appVisible' => false,
                        'meVisible' => false
            ]),
            'simage' => new Type\MixedType([
                'description' => t('Фото'),
                'visible' => false,
                'meVisible' => true,
                'template' => '%catalog%/form/product/simage.tpl'
            ]),
            t('Фото'),
                    '_photo_' => new Type\UserTemplate('%photogalleries%/form/album/photos.tpl'),
                    'meVisible' => false
        ]);
        
        //Включаем в форму hidden поле id.
        $this['__id']->setVisible(true);
        $this['__id']->setMeVisible(false);
        $this['__id']->setHidden(true);
        
        $this->addIndex(['site_id', 'alias'], self::INDEX_UNIQUE);
    }
    
    /**
    * Загружает фотографии для товара
    * 
    * @return \Photo\Model\Orm\Image[]
    */
    function fillImages()
    {
        if (!$this['images']) {
            if ($this['id']) {
                $photoapi = new \Photo\Model\PhotoApi();
                $images = $photoapi->getLinkedImages($this['id'], self::IMAGES_TYPE);
            } else {
                $images = [];
            }
            $this['images'] = $images;
        }
        return $this['images'];
    }
    
    /**
    * Возвращает ссылку на альбом
    * @return string
    */
    function getUrl()
    {
       return \RS\Router\Manager::obj()->getUrl('photogalleries-front-album', [
           'id' => $this['alias']
       ]);
    }
    
    /**
    * Возвращает объект фото-заглушку
    */
    function getImageStub()
    {
        return new \Photo\Model\Stub();
    }
    
    /**
    * Возвращает главную фотографию (первая в списке фотографий)
    * @return \Photo\Model\Orm\Image
    */
    function getMainImage($width = null, $height = null, $type = 'xy')
    {
        $img = $this['image'] ? $this->__image : $this->getImageStub();
        
        return ($width === null) ? $img : $img->getUrl($width, $height, $type);
    }
        
    /**
     * Вызывается перед сохранением объекта
     * 
     * @param string $flag - строковое представление текущей операции (insert или update)
     * @return void
     */
    function beforeWrite($flag)
    {
        if ($this['id'] < 0) {
            $this['_tmpid'] = $this['id'];
            unset($this['id']);
        }
        
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = \RS\Orm\Request::make()
                ->select('MAX(sortn) as max')
                ->from($this)
                ->exec()->getOneField('max', 0) + 1;
        }
    }
    
    
    /**
     * Вызывается после сохранения объекта
     * 
     * @param string $flag - строковое представление текущей операции (insert или update)
     * @return void
     */
    function afterWrite($flag)
    {
        //Переносим временные объекты, если таковые имелись
        if ($this['_tmpid']<0) {
            \RS\Orm\Request::make()
                    ->update(new \Photo\Model\Orm\Image())
                    ->set(['linkid' => $this['id']])
                    ->where([
                        'type' => self::IMAGES_TYPE,
                        'linkid' => $this['_tmpid']
                    ])->exec();
        }
    }
}