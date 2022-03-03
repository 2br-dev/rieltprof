<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Yandex\OfferType;

use \Export\Model\ExportType\AbstractOfferType as AbstractOfferType;
use \Export\Model\ExportType\Field as Field;
use \Export\Model\ExportType\Yandex\OfferType\Fields as YandexField;
use \Export\Model\Orm\ExportProfile as ExportProfile;
use \Catalog\Model\Orm\Product as Product;


abstract class CommonOfferType extends AbstractOfferType
{

    protected $use_htmlentity;

    function __construct()
    {
        $config = \RS\Config\Loader::byModule('catalog');
        $this->use_htmlentity = $config->use_htmlentity;
    }
    /**
    * Добавляет в выгрузки яндекса общие поля
    */
    protected function addCommonEspecialTags($ret)
    {
        $field = new Field();
        $field->name        = 'country_of_origin';
        $field->title       = t('Страна производства');
        $ret[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'sales_notes';
        $field->title       = t('Замечания продаж (sales_notes)');
        $ret[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'delivery';
        $field->title       = t('Возможность доставки (delivery)');
        $field->hint        = t('Характеристика Да/Нет');
        $field->type        = TYPE_BOOLEAN;
        $ret[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'pickup';
        $field->title       = t('Возможность получения товара<br>в пункте выдачи/почте России (pickup)');
        $field->hint        = t('Характеристика Да/Нет');
        $field->type        = TYPE_BOOLEAN;
        $ret[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'store';
        $field->title       = t('Возможность покупки в точке продаж (store)');
        $field->hint        = t('Характеристика Да/Нет');
        $field->type        = TYPE_BOOLEAN;
        $ret[$field->name]  = $field;

        $field = new YandexField\DeliveryOptionsField();
        $field->name        = 'offer_delivery_cost';
        $field->title       = t('Максимальная стоимость доставки товара по вашему региону (delivery-options)');
        $field->hint        = t('для выгрузки тега "delivery-options" необходимо указать оба поля');
        $ret[$field->name]  = $field;
        
        $field = new YandexField\DeliveryOptionsField();
        $field->name        = 'offer_delivery_days';
        $field->title       = t('Срок доставки товара по вашему региону (delivery-options)');
        $field->hint        = t('для выгрузки тега "delivery-options" необходимо указать оба поля');
        $ret[$field->name]  = $field;

        $field = new YandexField\DeliveryOptionsField();
        $field->name        = 'offer_order_before';
        $field->title       = t('Время, до которого нужно сделать заказ, чтобы получить его в этот срок (delivery-options)');
        $field->hint        = t('для выгрузки тега "delivery-options" необходимо указать предыдущие два поля выше');
        $ret[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'manufacturer_warranty';
        $field->title       = t('Гарантия производителя');
        $field->type        = TYPE_BOOLEAN;
        $ret[$field->name]  = $field;

        $field = new YandexField\FieldDimensions();
        $field->name        = 'dimensions_l';
        $field->title       = t('Длинна товара (число, в см)');
        $field->hint        = t('для выгрузки тега "dimensions" необходимо указать все 3 габарита');
        $ret[$field->name]  = $field;

        $field = new YandexField\FieldDimensions();
        $field->name        = 'dimensions_w';
        $field->title       = t('Ширина товара (число, в см)');
        $field->hint        = t('для выгрузки тега "dimensions" необходимо указать все 3 габарита');
        $ret[$field->name]  = $field;

        $field = new YandexField\FieldDimensions();
        $field->name        = 'dimensions_h';
        $field->title       = t('Высота товара (число, в см)');
        $field->hint        = t('для выгрузки тега "dimensions" необходимо указать все 3 габарита');
        $ret[$field->name]  = $field;

        $field = new YandexField\FieldAge();
        $field->name        = 'age';
        $field->title       = t('Возрастное ограничение');
        $field->hint        = t('Допустимые значения: 0, 6, 12, 16, 18.');
        $ret[$field->name]  = $field;

        $field = new YandexField\FieldWeight();
        $field->name        = 'weight';
        $field->title       = t('Вес товара');
        $field->hidden      = true;
        $ret[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'adult';
        $field->title       = t('Товар для взрослых');
        $field->hint        = t('Характеристика Да/Нет');
        $field->type        = TYPE_BOOLEAN;
        $ret[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'min-quantity';
        $field->title       = t('Минимальное количество для заказа');
        $ret[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'step-quantity';
        $field->title       = t('Шаг количества товара при заказе');
        $ret[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'cpa';
        $field->title       = t('Участие в "заказе на Маркете"');
        $field->hint        = t('Характеристика Да/Нет');
        $field->type        = TYPE_BOOLEAN;
        $field->boolAsInt   = true;
        $ret[$field->name]  = $field;

        $field = new YandexField\Condition();
        $field->name        = 'type_condition_ym';
        $field->title       = t('Состояние товара');
        $field->hint        = t('likenew или used');
        $ret[$field->name]  = $field;

        $field = new YandexField\Condition();
        $field->name        = 'reason_ym';
        $field->title       = t('Причина уценки');
        $field->hint        = t('Работает только при заполненном теге "Состояние товара"');
        $ret[$field->name]  = $field;
        
        return $ret;
    }
    
    /**
    * Запись товарного предложения
    * 
    * @param ExportProfile $profile
    * @param \XMLWriter $writer
    * @param mixed $product
    * @param mixed $offer_index
    */
    public function writeOffer(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        $event_result = $this->fireOfferEvent('beforewriteoffer', $profile, $writer, $product, $offer_index);
        if ($event_result->getEvent()->isStopped()) {
            return ;
        }

        $writer->startElement("offer");
            
            // Добавляем group_id, если у товара есть комплектации
            if ($product->isOffersUse()) {
                $writer->writeAttribute('id', $product->id.'x'.$offer_index);
                $writer->writeAttribute('group_id', $product->id);
            } else {
                $writer->writeAttribute('id', $product->id);
            }
            
            // Если указан тип описания
            if(isset($profile->data['offer_type'])){
                // Если это не Simple описание, то у тега offer добавляем аттрибут type
                if($profile->data['offer_type'] != 'simple'){
                    $writer->writeAttribute('type', $profile->data['offer_type']);  
                }
            }

            // Берем цену по-умолчанию
            $prices = $product->getOfferCost($offer_index, $product['xcost']);
            if (!empty($profile['export_cost_id'])){
                $price = $prices[ $profile['export_cost_id'] ];
            }else{
                $price = $prices[\Catalog\Model\Costapi::getDefaultCostId()];
            }

            if ($old_cost_id = \Catalog\Model\CostApi::getOldCostId()) {
                $old_price = $prices[ $old_cost_id ];
            }

            // Определяем доступность товара
            $available = $product->getNum($offer_index) > 0 && $price > 0;

            //Дополнительные параметры адресе страницы
            $url_params = false;
            if (!empty($profile['url_params'])){
                $url_params = htmlspecialchars_decode($profile['url_params']);
            }
            $writer->writeAttribute('available', $available ? 'true' : 'false');
            $request_domain = \RS\Http\Request::commonInstance()->getDomain(true);  
            $writer->writeElement('url', $request_domain . $product->getUrl() . ($url_params ? "?".$url_params : "") .( $offer_index ? '#'.$offer_index : '' ));  
            $writer->writeElement('price', $price);  
            if (!empty($old_price) && $old_price > $price) {
                $writer->writeElement('oldprice', $old_price);  
            }
            $writer->writeElement('currencyId', \Catalog\Model\CurrencyApi::getDefaultCurrency()->title);
            $sku = $product->getSKU($offer_index);
            if (!empty($sku)) {
                $writer->writeElement('barcode', $product->getSKU($offer_index));
            }
            $writer->writeElement('categoryId', $product->maindir);
            $exist = !empty($product['offers']['items'][$offer_index]['photos_arr']);      //проверка на наличие присвоенных к офферу изображений
            if ($exist == false) {
                $this->writeProductPictures($product, $profile, $writer);//заполняем всеми изображениями товара(максимум 10)
            } else {
                $this->writeOfferPictures($product, $offer_index, $profile, $writer);//заполняем  изображениями оффера (максимум 10)
            }

            if ($profile->export_fb_model) {
                $writer->writeElement('count', $product['offers']['items'][$offer_index]->num);
                $market_sku = $product['offers']['items'][$offer_index]['market_sku'] ?? $product['market_sku'];
                if ($market_sku) {
                    $writer->writeElement('market-sku', $market_sku);
                }

                $shop_sku = $product['offers']['items'][$offer_index]['barcode'] ?? $product['barcode'];
                if ($shop_sku) {
                    $writer->writeElement('shop-sku', $shop_sku);
                }
            }

            if ($profile['vendor_code_from_barcode'] && !empty($product['barcode'])) {
                $writer->writeElement('vendorCode', $product['barcode']);
            }

            if ($profile->use_full_description) {
                $writer->startElement('description');
                    //Пропускаем разрешенные Яндексом теги в описании
                    $writer->writeCdata(strip_tags($product->description, '<h3><ul><li><p><br>'));
                $writer->endElement();
            } else {
                $writer->writeElement('description', $this->use_htmlentity ? htmlspecialchars_decode($product->short_description) : $product->short_description);
            }

            // Запись "особенных" элементов для каждого конкретного типа описания
            $this->writeEspecialOfferTags($profile, $writer, $product, $offer_index);
            // Записываем свойства товара в теги <param>
            $prop_list = $product->getVisiblePropertyList(true, true);
            foreach($prop_list as $group){
                if(!isset($group['properties'])) continue;

                foreach ($group['properties'] as $prop) {
                    $value = $prop->textView();
                    if (trim($value) !== '') {
                        $writer->startElement('param');
                            $param_name = ($prop->name_for_export) ?: $prop->title;
                            $writer->writeAttribute('name', ($this->use_htmlentity) ? htmlspecialchars_decode($param_name) : $param_name);
                            // Если у свойства товара указана единица измерения
                            if ($prop->unit) {
                                $writer->writeAttribute('unit', $prop->unit);
                            }
                            $writer->text($value);
                        $writer->endElement();
                    }
                }
            }
            //Записываем свойства комплектации в теги <param>
            if (!$profile['no_export_offers_props'] && isset($product['offers']['items'][$offer_index])) {
                $offer = $product['offers']['items'][$offer_index];
                foreach ((array)$offer['propsdata_arr'] as $key => $value) {
                    $writer->startElement('param');
                        $name_for_export = $key;
                        $writer->writeAttribute('name', ($this->use_htmlentity) ? htmlspecialchars_decode($name_for_export) : $name_for_export);

                        if ($this->getPropUnitForExport($product, $key)) {
                            $writer->writeAttribute('unit', $this->getPropUnitForExport($product, $key));
                        }
                        $writer->text($value);
                    $writer->endElement();
                }
            }
            $this->fireOfferEvent('writeoffer', $profile, $writer, $product, $offer_index);
        $writer->endElement();
    }
}