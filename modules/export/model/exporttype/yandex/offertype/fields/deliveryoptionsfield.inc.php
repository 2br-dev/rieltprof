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
class DeliveryOptionsField extends ExportType\Field implements ExportType\ComplexFieldInterface
{
    /**
    * Добавляет необходимую структуру тегов в итоговый XML
    * 
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param integer $offer_index - индекс комплектации для отображения
    */
    function writeSomeTags(\XMLWriter $writer, ExportProfile $profile, Product $product, $offer_index = null){
        $export_type_object = $profile->getTypeObject();
        
        // Запись произойдёт только если заданы обе характеристики, и только 1 раз 
        if($this->name == 'offer_delivery_cost' && !empty($export_type_object['fieldmap'][$this->name]['prop_id'])){
            $cost_value   = $this->getValue('offer_delivery_cost', $profile, $product);
            $days_value   = $this->getValue('offer_delivery_days', $profile, $product);
            $order_before = $this->getValue('offer_order_before', $profile, $product);

            if (is_numeric($cost_value)) {
                $writer->startElement('delivery-options');
                    $writer->startElement('option');
                        $writer->writeAttribute('cost', $cost_value);
                        $writer->writeAttribute('days', $days_value);
                        if ($order_before){
                            $writer->writeAttribute('order-before', $order_before);
                        }
                    $writer->endElement();
                $writer->endElement();
            }
        }
    }
}