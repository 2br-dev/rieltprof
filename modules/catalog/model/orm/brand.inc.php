<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Orm;
use \RS\Orm\Type;
 
/**
 * Класс ORM-объектов "Бренд".
 * Наследуется от объекта \RS\Orm\OrmObject, у которого объявлено свойство id
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название бренда
 * @property string $alias URL имя
 * @property integer $public Публичный
 * @property string $image Картинка
 * @property string $description Описание
 * @property string $xml_id Идентификатор в системе 1C
 * @property integer $sortn Сортировочный номер
 * @property string $meta_title Заголовок
 * @property string $meta_keywords Ключевые слова
 * @property string $meta_description Описание
 * --\--
 */
class Brand extends \RS\Orm\OrmObject
{
    protected static
        $table = 'brand'; //Имя таблицы в БД
         
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
                'description' => t('Название бренда'),
                'checker' => ['chkEmpty', t('Укажите название бренда')],
                'attr' => [[
                    'data-autotranslit' => 'alias'
                ]]
            ]),
            'alias' => new Type\Varchar([
                'description' => t('URL имя'),
                'checker' => ['chkalias', t('Могут использоваться только английские буквы, цифры, знак подчеркивания, запятая, точка и минус')],
                'meVisible' => false
            ]),
            'public' => new Type\Integer([
                'maxLength' => 1,
                'index' => true,
                'default' => 1,
                'description' => t('Публичный'),
                'checkboxView' => [1,0]
            ]),
            'image' => new Type\Image([ //Будет отображаться форма загрузки файла
                'max_file_size'    => 10000000, //Максимальный размер - 10 Мб
                'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'], //Допустимы форматы jpg, png, gif
                'description'      => t('Картинка'),
            ]),
            'description' => new Type\Richtext([
                'description' => t('Описание')
            ]),
            'xml_id' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Идентификатор в системе 1C'),
                'meVisible' => false
            ]),
            'sortn' => new Type\Integer([
                'maxLength' => 11,
                'index' => true,
                'description' => t('Сортировочный номер'),
                'visible' => false
            ]),
            t('Мета-теги'),
               'meta_title' => new Type\Varchar([
                    'maxLength' => '1000',
                    'description' => t('Заголовок'),
               ]),
               'meta_keywords' => new Type\Varchar([
                    'maxLength' => '1000',
                    'description' => t('Ключевые слова'),
               ]),
               'meta_description' => new Type\Varchar([
                    'maxLength' => '1000',
                    'viewAsTextarea' => true,
                    'description' => t('Описание'),
               ]),
        ]);
        $this->addIndex(['site_id', 'alias'], self::INDEX_UNIQUE);
    }
    
    /**
    * Возвращает отладочные действия, которые можно произвести с объектом
    * 
    * @return RS\Debug\Action[]
    */
    function getDebugActions()
    {
        return [
            new \RS\Debug\Action\Edit(\RS\Router\Manager::obj()->getAdminPattern('edit', [':id' => '{id}'], 'catalog-brandctrl')),
            new \RS\Debug\Action\Delete(\RS\Router\Manager::obj()->getAdminPattern('del', [':chk[]' => '{id}'], 'catalog-brandctrl'))
        ];
    }
    
    /**
    * Функция срабатывает перед записью в базу
    * 
    * @param mixed $flag
    * @return void
    */
    function beforeWrite($flag)
    {
        //При вставке
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = \RS\Orm\Request::make()
                ->select('MAX(sortn) as max')
                ->from($this)
                ->exec()->getOneField('max', 0) + 1;
        }
    }
    
    
    
    /**
    * Возвращает ссылку на бренд
    * @return string
    */
    function getUrl()
    {
        $alias = $this['alias'] ?: $this['id'];
        $router = \RS\Router\Manager::obj();
       return $router->getUrl('catalog-front-brand', [
           'id' => $alias
       ]);
    }
    
    /**
    * Возвращает объект фото-заглушку
    * @return \Photo\Model\Stub
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
    * Удаление
    * 
    */
    function delete()
    {
        if ($result = parent::delete()) {
            if ($this['id']) {
                \RS\Orm\Request::make()
                    ->update(new Product())
                    ->set([
                        'brand_id' => 0
                    ])->where([
                        'brand_id' => $this['id']
                    ])->exec();
            }
        }
        return $result;
    }
    
    /**
    * Возвращает клонированный объект бренда
    * @return Brand
    */
    function cloneSelf()
    {
        /**
        * @var \Shop\Model\Orm\Payment
        */
        $clone = parent::cloneSelf();

        //Клонируем фото, если нужно
        if ($clone['image']){
           /**
           * @var \RS\Orm\Type\Image
           */
           $clone['image'] = $clone->__image->addFromUrl($clone->__image->getFullPath());
        }
        unset($clone['alias']);
        return $clone;
    }
}