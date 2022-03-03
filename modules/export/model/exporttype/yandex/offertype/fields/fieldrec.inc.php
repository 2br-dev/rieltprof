<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Yandex\OfferType\Fields;
use Catalog\Model\Orm\Product;
use \Export\Model\ExportType;
use Export\Model\Orm\ExportProfile;

/**
* Структура данных, описывающая поле в экспортируемом XML документе
*/
class FieldRec extends ExportType\Field implements ExportType\ComplexFieldInterface
{
    static
        $count_offers = null;
    
    /**
    * Добавляет необходимую структуру тегов в итоговый XML
    * 
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param integer $offer_index - индекс комплектации для отображения
    */
    function writeSomeTags(\XMLWriter $writer, ExportProfile $profile, Product $product, $offer_index = null) {
        if (self::$count_offers === null) {
            self::$count_offers = \RS\Orm\Request::make()
                ->select('product_id, count(*) as count')
                ->from(new \Catalog\Model\Orm\Offer())
                ->groupby('product_id')
                ->where([
                    'site_id' =>\RS\Site\Manager::getSiteId()
                ])
                ->exec()->fetchSelected('product_id', 'count');
        }
        
        if (!empty($product['recommended_arr']['product'])) {
            $recommended = array_slice($product['recommended_arr']['product'], 0, 30); // не более 20 рекомендованных товаров
            $rec_arr = [];
            foreach($recommended as $item) {
                if (isset(self::$count_offers[$item])) {
                    $rec_arr[] = (self::$count_offers[$item] > 1) ? $item.'x0' : $item.'x';
                }
            }
            $rec = implode(',', $rec_arr);
            
            $writer->writeElement('rec', $rec);
        }
    }
}
