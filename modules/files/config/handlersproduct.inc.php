<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Config;
use \RS\Orm\Type;

/**
* В классе реализованы обработчики событий для товара
*/
class HandlersProduct extends \RS\Event\HandlerAbstract
{
    function init()
    {
        //Добавляем функциональность файлов к товарам
        $this
            ->bind('orm.init.catalog-product')
            ->bind('orm.afterwrite.catalog-product')
            ->bind('orm.delete.catalog-product')
            ->bind('orm.multidelete.catalog-product');
    }
    
    /**
    * Добавляет вкладку Файлы к товару
    */
    public static function ormInitCatalogProduct($product)
    {
        $product->getPropertyIterator()->append([
            t('Файлы'),
            '__files__' => new Type\UserTemplate('%files%/catalog/product_files.tpl')
        ]);
    }
    
    /**
    * Обрабатывает привязку файлов при создании товара
    */
    public static function ormAfterwriteCatalogProduct($params)
    {
        $product = $params['orm'];
        
        if ($product['_tmpid']<0) {
            \RS\Orm\Request::make()
                    ->update(new \Files\Model\Orm\File())
                    ->set(['link_id' => $product['id']])
                    ->where([
                        'link_type_class' => 'files-catalogproduct',
                        'link_id' => $product['_tmpid']
                    ])->exec();
        }
    }
    
    /**
    * Обрабатывает удаление товара
    */
    public static function ormDeleteCatalogProduct($params)
    {
        $product = $params['orm'];
        
        $file_api = new \Files\Model\FileApi();
        $file_api->setFilter('link_id', $product['id']);
        $file_api->setFilter('link_type_class', 'files-catalogproduct');
        $files = $file_api->getList();
        foreach ($files as $file) {
            $file->delete();
        }
    }
    
    /**
    * Обрабатывает массовое удаление товаров
    */
    public static function ormMultideleteCatalogProduct($params)
    {
        $ids = $params['ids'];
        if ($ids) {
            $file_api = new \Files\Model\FileApi();
            $file_api->setFilter('link_id', $ids, 'in');
            $file_api->setFilter('link_type_class', 'files-catalogproduct');
            $page = 1;
            while($files = $file_api->getList($page, 50)) {
                foreach ($files as $file) {
                    $file->delete();
                }
                $page++;
            }
        }
    }
}
