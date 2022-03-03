<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm;
use Catalog\Model\Api;
use RS\Config\UserFieldsManager;
use \RS\Orm\Type;
use Catalog\Model\Orm\Property\Item as PropertyItem;

/**
 * Категория товаров
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $name Название категории
 * @property string $alias Псевдоним
 * @property integer $parent Родитель
 * @property integer $public Публичный
 * @property integer $sortn Порядк. N
 * @property string $is_spec_dir Это спец. список?
 * @property integer $is_label Показывать как ярлык у товаров
 * @property integer $itemcount Количество элементов
 * @property integer $level Уровень вложенности
 * @property string $image Изображение
 * @property integer $weight Вес товара по умолчанию, грамм
 * @property string $xml_id Идентификатор в системе 1C
 * @property string $_alias 
 * @property string $_class 
 * @property integer $closed 
 * @property integer $processed 
 * @property string $description Описание категории
 * @property string $meta_title Заголовок
 * @property string $meta_keywords Ключевые слова
 * @property string $meta_description Описание
 * @property string $product_meta_title Заголовок товаров
 * @property string $product_meta_keywords Ключевые слова товаров
 * @property string $product_meta_description Описание товаров
 * @property string $default_product_meta_title Заголовок товаров по умолчанию
 * @property string $default_product_meta_keywords Ключевые слова товаров по умолчанию
 * @property string $default_product_meta_description Описание товаров по умолчанию
 * @property array $in_list_properties_arr Характеристики списка (удерживая CTRL можно выбрать несколько)
 * @property string $in_list_properties Характеристики списка
 * @property integer $is_virtual Включить подбор товаров
 * @property array $virtual_data_arr Параметры выборки товаров
 * @property string $virtual_data Параметры выборки товаров
 * @property string $export_name Название категории при экспорте
 * @property string $recommended Рекомендуемые товары
 * @property array $recommended_arr 
 * @property string $concomitant Сопутствующие товары
 * @property array $concomitant_arr 
 * --\--
 */
class Dir extends \RS\Orm\OrmObject
{
    const ROOT_DIR = 'all';

    protected static
        $table = 'product_dir',
        $deleted_folder_id, //id папки, в которую скидываются все товары из удаленных папок
        $product_xdir;
    
    function _init()
    {                
        parent::_init()->append([
            t('Основные'),
                    'site_id' => new Type\CurrentSite(),
                    'name' => new Type\Varchar([
                        'maxLength' => '255',
                        'description' => t('Название категории'),
                        'Checker' => ['chkEmpty', t('Название категории не может быть пустым')],
                        'attr' => [[
                            'data-autotranslit' => 'alias'
                        ]],
                        'rootVisible' => false
                    ]),
                    'alias' => new Type\Varchar([
                        'maxLength' => '150',
                        'Checker' => ['chkalias', null],
                        'checker' => [function($dir, $value, $error_text){
                            if (!empty($value) && $value === self::ROOT_DIR) {
                                return $error_text;
                            }
                            return true;
                        }, t('«all» зарезервирован. Используйте другой псевдоним')],
                        'description' => t('Псевдоним'),
                        'rootVisible' => false,
                        'meVisible' => false
                    ]),
                    'parent' => new Type\Integer([
                        'maxLength' => '11',
                        'description' => t('Родитель'),
                        'tree' => [['\Catalog\Model\DirApi', 'staticTreeList'], 0, [0 => t('- Корень каталога -')]],
                        'specVisible' => false,
                        'rootVisible' => false
                    ]),
                    'public' => new Type\Integer([
                        'maxLength' => '1',
                        'description' => t('Публичный'),
                        'CheckboxView' => [1,0],
                        'rootVisible' => false
                    ]),
                    'sortn' => new Type\Integer([
                        'maxLength' => '11',
                        'description' => t('Порядк. N'),
                        'visible' => false,
                        'rootVisible' => false
                    ]),
                    'is_spec_dir' => new Type\Varchar([
                        'maxLength' => '1',
                        'description' => t('Это спец. список?'),
                        'visible' => false,
                        'rootVisible' => false,
                        'hint' => t('Например: новинки, популярные товары, горячие и т.д.'),
                    ]),
                    'is_label' => new Type\Integer([
                        'description' => t('Показывать как ярлык у товаров'),
                        'hint' => t('Опция актуальна только для тех тем оформления, где поддерживается эта опция'),
                        'maxLength' => 1,
                        'default' => 0,
                        'allowEmpty' => false,
                        'visible' => false,
                        'specVisible' => true,
                        'checkboxView' => [1, 0]
                    ]),
                    'itemcount' => new Type\Integer([
                        'description' => t('Количество элементов'),
                        'visible' => false,
                        'rootVisible' => false
                    ]),
                    'level' => new Type\Integer([
                        'description' => t('Уровень вложенности'),
                        'index' => true,
                        'visible' => false,
                        'rootVisible' => false
                    ]),
                    'image' => new Type\Image([
                        'max_file_size' => 10000000,
                        'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'],
                        'description' => t('Изображение'),
                        'specVisible' => true,
                        'rootVisible' => false
                    ]),
                    'weight' => new Type\Integer([
                        'description' => t('Вес товара по умолчанию, грамм'),
                        'rootVisible' => false
                    ]),
                    'xml_id' =>  new Type\Varchar([
                        'maxLength' => '255',
                        'description' => t('Идентификатор в системе 1C'),
                        'meVisible' => false,
                    ]),
                    '_alias' => new Type\Varchar([
                        'maxLength' => '50',
                        'runtime' => true,
                        'visible' => false,
                        'rootVisible' => false
                    ]),
                    '_class' => new Type\Varchar([
                        'maxLength' => '50',
                        'runtime' => true,
                        'visible' => false,
                        'rootVisible' => false
                    ]),
                    'closed' => new Type\Integer([
                        'maxLength' => '1',
                        'runtime' => true,
                        'visible' => false,
                        'rootVisible' => false,
                    ]),
                    'prop' => new Type\MixedType([
                        'visible' => false,
                        'rootVisible' => false
                    ]),
                    'properties' => new Type\MixedType([
                        'visible' => false,
                    ]),
                    'processed' => new Type\Integer([
                        'maxLength' => '2',
                        'visible' => false,
                    ]),

            t('Характеристики'),
                    '__property__' => new Type\UserTemplate('%catalog%/form/dir/property.tpl',null, [
                        'getPropertyItemAllowTypeData' => function() {
                            return PropertyItem::getAllowTypeData();
                        },
                    ]),
                    
            t('Описание'),
                    'description' => new Type\Richtext([
                        'description' => t('Описание категории'),
                        'rootVisible' => false
                    ]),
            t('Мета-тэги'),
                    'meta_title' => new Type\Varchar([
                        'maxLength' => '1000',
                        'description' => t('Заголовок'),
                        'rootVisible' => false
                    ]),
                    'meta_keywords' => new Type\Varchar([
                        'maxLength' => '1000',
                        'description' => t('Ключевые слова'),
                        'rootVisible' => false
                    ]),
                    'meta_description' => new Type\Varchar([
                        'maxLength' => '1000',
                        'viewAsTextarea' => true,
                        'description' => t('Описание'),
                        'rootVisible' => false
                    ]),
            t('Параметры товаров'),
                    'product_meta_title' => new Type\Varchar([
                        'maxLength' => '1000',
                        'description' => t('Заголовок товаров'),
                        'rootVisible' => false
                    ]),
                    'product_meta_keywords' => new Type\Varchar([
                        'maxLength' => '1000',
                        'description' => t('Ключевые слова товаров'),
                        'rootVisible' => false
                    ]),
                    'product_meta_description' => new Type\Varchar([
                        'maxLength' => '1000',
                        'viewAsTextarea' => true,
                        'description' => t('Описание товаров'),
                        'rootVisible' => false
                    ]),
                    'default_product_meta_title' => new Type\Varchar([
                        'maxLength' => '1000',
                        'description' => t('Заголовок товаров по умолчанию'),
                        'visible' => false,
                        'rootVisible' => true,
                        'runtime' => true,
                    ]),
                    'default_product_meta_keywords' => new Type\Varchar([
                        'maxLength' => '1000',
                        'description' => t('Ключевые слова товаров по умолчанию'),
                        'visible' => false,
                        'rootVisible' => true,
                        'runtime' => true,
                    ]),
                    'default_product_meta_description' => new Type\Varchar([
                        'maxLength' => '1000',
                        'viewAsTextarea' => true,
                        'description' => t('Описание товаров по умолчанию'),
                        'visible' => false,
                        'rootVisible' => true,
                        'runtime' => true,
                    ]),
                    'in_list_properties_arr' => new Type\ArrayList([
                        'description' => t('Характеристики списка (удерживая CTRL можно выбрать несколько)'),
                        'hint' => t('Данные характеристики могут отображаться в списке товаров, если это предусматривает тема оформления'),
                        'list' => [['\Catalog\Model\PropertyApi', 'staticSelectList']],
                        'attr' => [[
                            'multiple' => true,
                            'size' => 10
                        ]],
                        'rootVisible' => false
                    ]),
                    'in_list_properties' => new Type\Text([
                        'description' => t('Характеристики списка'),
                        'visible' => false
                    ]),
            t('Подбор товаров'),
                    '__virtual__' => new Type\UserTemplate('%catalog%/form/dir/virtual.tpl', null, [
                        'rootVisible' => false,
                        'specVisible' => false,
                        'getBrands' => function() {
                            return \Catalog\Model\BrandApi::staticSelectList();
                        },
                        'getProperties' => function() {
                            return \Catalog\Model\PropertyApi::staticSelectList();
                        }
                    ]),
                    'is_virtual' => new Type\Integer([
                        'description' => t('Включить подбор товаров'),
                        'checkboxView' => [1,0],
                        'visible' => false,
                    ]),
                    'virtual_data_arr' => new Type\ArrayList([
                        'description' => t('Параметры выборки товаров'),
                        'visible' => false,
                    ]),
                    'virtual_data' => new Type\Text([
                        'description' => t('Параметры выборки товаров'),
                        'visible' => false,
                    ]),
            t('Экспорт данных'),
                'export_name' => new Type\Varchar([
                    'description' => t('Название категории при экспорте'),
                    'hint' => t('Если не указано - используется "название категории". Используется для экспорта данных во внешние системы, например в Яндекс.Маркет'),
                    'rootVisible' => false
                ]),
            t('Рекомендуемые товары'),
                'recommended' => new Type\Varchar([
                    'maxLength' => 4000,
                    'description' => t('Рекомендуемые товары'),
                    'visible' => false,
                ]),
                'recommended_arr' => new Type\ArrayList([
                    'visible' => false
                ]),
                '_recomended_' => new Type\UserTemplate(
                    '%catalog%/form/product/recomended.tpl',
                    '%catalog%/form/product/merecomended.tpl', [
                    'rootVisible' => false,
                    'meVisible' => true  //Видимость при мультиредактировании
                ]),
            t('Сопутствующие товары'),
                'concomitant' => new Type\Varchar([
                    'maxLength' => 4000,
                    'description' => t('Сопутствующие товары'),
                    'visible' => false,
                ]),
                'concomitant_arr' => new Type\ArrayList([
                    'visible' => false
                ]),
                '_concomitant_' => new Type\UserTemplate(
                    '%catalog%/form/product/concomitant.tpl',
                    '%catalog%/form/product/meconcomitant.tpl', [
                    'rootVisible' => false,
                    'meVisible' => true  //Видимость при мультиредактировании
                ]),


        ]);
        
        $this->addIndex(['site_id', 'parent']);
        $this->addIndex(['site_id', 'name', 'parent']);
        $this->addIndex(['site_id', 'xml_id'], self::INDEX_UNIQUE);
        $this->addIndex(['site_id', 'alias'], self::INDEX_UNIQUE);
    }
    
    /**
    * Возвращает отладочные действия, которые можно произвести с объектом
    * 
    * @return \RS\Debug\Action\AbstractAction[]
    */
    function getDebugActions()
    {
        return [
            new \RS\Debug\Action\Edit(\RS\Router\Manager::obj()->getAdminPattern('treeEdit', [':id' => '{id}'], 'catalog-ctrl')),
            new \RS\Debug\Action\Delete(\RS\Router\Manager::obj()->getAdminPattern('treeDel', [':chk[]' => '{id}'], 'catalog-ctrl'))
        ];
    }

    /**
     * Инициализация свойств по умолчанию
     */
    function _initDefaults()
    {
        $this['is_spec_dir'] = 'N'; //Если свойство не задано, то его значение будет N = Нет
    }
    
    /**
    * При создании записи sortn - ставим максимальный, т.е. добавляем фото в конец.
    */
    function beforeWrite($save_flag)
    {
        if ($save_flag == self::INSERT_FLAG)
        {
            if (!$this->isModified('sortn')) {
                $this['sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(sortn) maxid')
                    ->from($this)
                    ->exec()->getOneField('maxid', 0) + 1;
            }
        }
        
        if (empty($this['itemcount'])) $this['itemcount'] = 0;
        
        $api = new \Catalog\Model\Dirapi();
        $parents_arrs = $api-> getPathToFirst($this['parent']);
        if($this['id'] && isset($parents_arrs[$this['id']])){
            return $this->addError(t('Неверно указан родительский элемент'), 'parent');
        }

        if ($this->isModified('recommended_arr')){ //Если изменялись рекомендуемые
            $this['recommended'] = serialize($this['recommended_arr']);
        }
        if ($this->isModified('concomitant_arr')){ //Если изменялись сопутствующие
            $this['concomitant'] = serialize($this['concomitant_arr']);
        }
        
        if ($this['xml_id'] === '') {
            unset($this['xml_id']);
        }
        
        if (empty($this['alias'])) {
            $this['alias'] = null;
        }
        
        if ($this['is_virtual']) {
            $this['virtual_data'] = serialize($this['virtual_data_arr']);
            \RS\Cache\Manager::obj()->invalidateByTags(\Catalog\Model\VirtualDir::INVALIDATE_TAG);
        }

        if ($this->isModified('in_list_properties_arr')) {
            $this['in_list_properties'] = serialize($this['in_list_properties_arr']);
        }
        
        if ($this->isModified('default_product_meta_title') || $this->isModified('default_product_meta_keywords') || $this->isModified('default_product_meta_description')) {
            $config = \RS\Config\Loader::byModule($this);
            $config['default_product_meta_title'] = $this['default_product_meta_title'];
            $config['default_product_meta_keywords'] = $this['default_product_meta_keywords'];
            $config['default_product_meta_description'] = $this['default_product_meta_description'];
            $config->update();
        }

        return true;
    }    
    
    /**
    * Функция срабатывает после сохранения.
    * 
    * @param string $flag - update или insert
    */
    function afterWrite($flag)
    {
        if ($this->isModified('parent') && empty($this['no_update_levels'])) {
            \Catalog\Model\Dirapi::updateLevels();
        }
        if ($flag == self::UPDATE_FLAG) {
           \RS\Cache\Manager::obj()->invalidateByTags(CACHE_TAG_UPDATE_CATEGORY); 
        }
    }
    
    /**
    * Возвращает клонированный объект доставки
    * @return \Catalog\Model\Orm\Dir
    */
    function cloneSelf()
    {
        $this->fillProperty();
        /**
        * @var \Catalog\Model\Orm\Dir $clone
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
        unset($clone['xml_id']);
        unset($clone['sortn']);
        $clone['itemcount'] = 0;
        return $clone;
    }
    
    /**
    * Действия после загрузки самого объекта
    * @return void
    */
    function afterObjectLoad()
    {
        if (!empty($this['recommended'])) {
            $this['recommended_arr'] = @unserialize($this['recommended']);
        }
        if (!empty($this['concomitant'])) {
            $this['concomitant_arr'] = @unserialize($this['concomitant']);
        }
        $this['_alias'] = empty($this['alias']) ? (string)$this['id'] : $this['alias'];
        $this['_class'] = $this['is_spec_dir'] == 'Y' ? 'specdir' : '';
        $this['_class'] .= $this['is_virtual'] ? ' virtual' : '';
        
        if ($this['is_virtual']) {
            $this['virtual_data_arr'] = @unserialize($this['virtual_data']) ?: [];
        }

        $this['in_list_properties_arr'] = @unserialize($this['in_list_properties']);
    }
    
    /**
    * Возвращает список характеристик из поля prop в виде списка объектов
    */
    function getPropObjects()
    {
        return $this['properties'];
    }

    /**
     * Возвращает HTML код для блока "рекомендуемые товары"
     * @return \Catalog\Model\ProductDialog
     */
    function getProductsDialog()
    {
        return new \Catalog\Model\ProductDialog('recommended_arr', true, @(array) $this['recommended_arr']);
    }

    /**
     * Возвращает HTML код для блока "сопутствующие товары"
     * @return \Catalog\Model\ProductDialog
     */
    function getProductsDialogConcomitant()
    {
        $product_dialog = new \Catalog\Model\ProductDialog('concomitant_arr', true, @(array) $this['concomitant_arr']);
        $product_dialog->setTemplate('%catalog%/dialog/view_selected_concomitant.tpl');
        return $product_dialog;
    }

    /**
     * Возвращает товары, рекомендуемые вместе с текущим
     *
     * @param bool $return_hidden - Если true, то метод вернет даже не публичные товары. Если false, то только публичные
     * @return Product[]
     */
    function getRecommended($return_hidden = false)
    {
        $list = [];
        if (!empty($this['recommended_arr']['product'])) {

            $api = new Api();
            $api->setFilter('id', (array)$this['recommended_arr']['product'], 'in');
            if (!$return_hidden) {
                $api->setFilter('public', 1);
            }
            $list = $api->getAssocList('id');

        }
        return $list;
    }

    /**
     * Возвращает есть ли у категории рекомендуемые
     *
     * @return boolean
     */
    function isHaveRecommended()
    {
        return !empty($this['recommended_arr']['product']);
    }

    /**
     * Возвращает товары, сопутствующие для текущего
     *
     * @return Product[]
     */
    function getConcomitant()
    {
        $list = [];
        if (!empty($this['concomitant_arr']['product'])) {

            $api = new Api();
            $api->setFilter('id', (array)$this['concomitant_arr']['product'], 'in');
            $list = $api->getAssocList('id');

            foreach($list as $id => $product) {
                $list[$id]->onlyone = $this['concomitant_arr']['onlyone'][$id] ?? null;
            }
        }
        return $list;
    }

    /**
     * Возвращает есть ли у категории сопутствующие
     *
     * @return boolean
     */
    function isHaveConcomitant()
    {
        return (isset($this['concomitant_arr']['product']) && !empty($this['concomitant_arr']['product']));
    }

    /**
     * Заполняет характеристики категории
     *
     * @return array
     */
    function fillProperty()
    {
        if ($this['properties'] === null) {
            $property_api = new \Catalog\Model\PropertyApi();
            $this['properties'] = $property_api->getGroupProperty($this['id']);
        }
        return $this['properties'];
    }

    /**
     * Возвращает псевдоним переведнный в кодировку для адров
     *
     * @return string
     */
    function alias()
    {
        return urlencode($this['_alias']);
    }
    
    /**
    * Удаление категории товара.
    */
    function delete()
    {        
        $ids = \RS\Orm\Request::make()
            ->select('product_id')
            ->from(new Xdir())
            ->where(['dir_id' => $this['id']])
            ->exec()
            ->fetchSelected(null, 'product_id');

        if (!empty($ids)) {
            $api = new \Catalog\Model\Api();
            $api->multiDelete($ids, $this['id']); //Удаляем товары
        }        
		
    	return parent::delete(); //Удаляем текущий объект из базы.
	}
    
    
    /**
    * Перемещает товары из удаляемой папки в папку для удаленных товаров.
    */
    protected function _moveProducts()
    {
        if (!isset(self::$deleted_folder_id)) {
            $api = new \Catalog\Model\Dirapi();
            $dir = $api->getByAlias('deleted');
            if ($dir) {
                self::$deleted_folder_id = $dir['id'];
            } else {//Создаем эту папку при необходимости
                $newdir = $api->getElement();
                $newdir['id'] = '1';
                $newdir['name'] = t('Товары из удаленных папок');
                $newdir['parent'] = 0;
                $newdir['public'] = 0;
                $newdir['alias'] = 'deleted';
                $newdir->insert();
                self::$deleted_folder_id = $newdir['id'];
            }
        }
        
        //Переносим товары в спец. папку. Помним, что товар уже может присутствовать там
        \RS\Orm\Request::make()
            ->update(new Xdir(), true)
            ->set(['dir_id' => self::$deleted_folder_id])
            ->where(['dir_id' => $this['id']])
            ->exec();

        //Если в предыдущем запросе возникла ошибка Duplicate, то чистим связи с этой директорией.            
        \RS\Orm\Request::make()
            ->delete()
            ->from(new Xdir())
            ->where(['dir_id' => $this['id']])
            ->exec();
    }
    
    /**
    * Возвращает объект фото-заглушку
    */
    function getImageStub()
    {
        return new \Photo\Model\Stub();
    }
    
    /**
    * Возвращает главную фотографию
    * @return \Photo\Model\Orm\Image
    */
    function getMainImage($width = null, $height = null, $type = 'xy')
    {
        $img = $this['image'] ? $this->__image : $this->getImageStub();
        
        return ($width === null) ? $img : $img->getUrl($width, $height, $type);
    }

    /**
     * Возвращает путь к странице со списком товаров
     *
     * @param bool $absolute - возвращать абсолютный путь?
     * @return string
     */
    function getUrl($absolute = false)
    {
        return \RS\Router\Manager::obj()->getUrl('catalog-front-listproducts', ['category' => $this['_alias']], $absolute);
    }


    
    /**
    * Возвращает родительскую категорию
    * 
    * @return self
    */
    function getParentDir()
    {
        return new self($this['parent']);
    }
    
    /**
    * Возвращает объект, который работает с виртуальными категориями 
    * 
    * @return \Catalog\Model\VirtualDir
    */
    function getVirtualDir()
    {
        return new \Catalog\Model\VirtualDir($this);
    }

    /**
     * Делает категорию корневой
     */
    function declareRoot()
    {
        $this['_alias'] = self::ROOT_DIR;
        $this['public'] = 1;
    }

    /**
     * Возвращает объект, отвечающий за работу с пользовательскими полями.
     *
     * @return \RS\Config\UserFieldsManager
     */
    public function getClickFieldsManager()
    {
        return new UserFieldsManager($this['clickfields'], null, 'clickfields');
    }
}