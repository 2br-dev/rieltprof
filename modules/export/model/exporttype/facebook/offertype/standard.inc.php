<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\Facebook\OfferType;

use Catalog\Model\Orm\Product;
use Export\Model\ExportType\AbstractOfferType;
use Export\Model\ExportType\Field;
use Export\Model\Orm\ExportProfile;

class Standard extends AbstractOfferType
{
    /**
     * @var \RS\Config\Loader $shop_config
     */
    protected
        $shop_config;


    /**
     * Записывает товар в xml
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param Product $product
     * @param mixed $offer_index
     * @throws \RS\Exception
     */
    function writeOffer(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        $http_request = \RS\Http\Request::commonInstance();
        $request_host = $http_request->getProtocol() . '://' . $http_request->getDomainStr();
        $this->shop_config = \RS\Config\Loader::byModule('shop');

        $current_offer = false; // Текущая комплектация

        $writer->startElement("item");
        $this->fireOfferEvent('beforewriteoffer', $profile, $writer, $product, $offer_index);

        if ( $offer_index !== false ){
            $writer->writeAttribute('g:item_group_id', $product->id);
            $current_offer = $product['offers']['items'][$offer_index];//Текущее предложение
        }

        // Дополнительные параметры адреса страницы
        $url_params = false;
        if ( !empty($profile['url_params']) ){
            $url_params = htmlspecialchars_decode($profile['url_params']);
        }

        $title = strip_tags(trim($product->title.' '.(($offer_index !== false && !$profile->no_export_offers) ? $product->getOfferTitle($offer_index) : '')));
        $writer->writeElement("g:title", $title);
        $writer->writeElement("g:link", $request_host . $product->getUrl() . ($url_params ? "?".$url_params : "") . ($offer_index ? '#'.$offer_index : ''));

        // Описание
        if ( $profile->full_description && (strlen($product->description) < 5001)) {
            $description = $product->description;
        } else {
            $description = $product->short_description ?: $product->description;
        }
        $writer->writeElement("g:description", strip_tags($description));

        // Уникальный артикул
        $barcode = $product->getBarCode($offer_index);
        $profiles = $profile->getTypeObject();

        $gid = $barcode;
        if ($gid && $profile['barcode_offer_uniq'] && $current_offer  && !$profiles->no_export_offers) {
            $gid .= "-".$current_offer['id'];
        } else if (!$gid) {
            $gid = $current_offer ? $current_offer['id'] : $product['id'];
        }
        $writer->writeElement("g:id", $gid);

        // Категории
        $this->writeOfferCategory($profile, $writer, $product);

        // Картинка и картинки
        $this->writeOfferImages($profile, $writer, $product, $current_offer);

        // Цена по умолчанию
        $prices = $product->getOfferCost($offer_index, $product['xcost']);
        if ( !empty($profile['export_cost_id']) ) {
            $price = $prices[$profile['export_cost_id']];
        }else{
            $price = $prices[\Catalog\Model\Costapi::getDefaultCostId()];
        }
        $old_price = 0;
        if ( $old_cost_id = \Catalog\Model\CostApi::getOldCostId() ) {
            $old_price = $prices[$old_cost_id];
        }

        // Доступность
        $this->writeOfferAvaliability($writer, $product, $price, $offer_index);

        // Цена
        if ($old_price > 0){ //Если есть старая цена продажи
            $writer->writeElement("g:price", $old_price." ".\Catalog\Model\CurrencyApi::getDefaultCurrency()->title);
            $writer->writeElement("g:sale_price", $price." ".\Catalog\Model\CurrencyApi::getDefaultCurrency()->title);
        }else{
            $writer->writeElement("g:price", $price." ".\Catalog\Model\CurrencyApi::getDefaultCurrency()->title);
        }

        // Бренд
        $brand = $product->getBrand();
        if ( isset($brand['id']) && $brand['id'] ){
            $writer->writeElement("g:brand", $brand['title']);
        }

        // Штрихкод
        $sku = $product->getSKU($offer_index);
        if ( !empty($sku) ) {
            $writer->writeElement("g:gtin", $sku);
        }

        // Запись доп. полей
        $this->writeEspecialOfferTags($profile, $writer, $product, $offer_index);

        // Вес товара
        if ( $product->getWeight($offer_index) ) {
            $weight = ($product->getWeight($offer_index))/1000;
            $writer->writeElement("g:shipping_weight", $weight.' kg');
        }

        //Дополнительные сведения если, это комплектация
        if ($current_offer){
            $this->writeOfferAdditionalParams($profile, $writer, $current_offer);
        }

        $this->fireOfferEvent('writeoffer', $profile, $writer, $product, $offer_index);
        $writer->endElement();
    }

    /**
     * Дополнительные поля для испорта
     * @param $fields
     * @return \Export\Model\ExportType\Filed[]
     */
    protected function addSelfEspecialTags($fields)
    {
        $field = new Field();
        $field->name        = 'g:condition';
        $field->title       = t('*Состояние товара (condition)');
        $field->hint        = t('new, refurbished, used<br>Обязательное<br>Тип: Строка');
        $field->required    = true;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:google_product_category';
        $field->title       = t('Категория к товарам из классификатора Google(Идентификатор)');
        $field->hint        = t('Категории из классификатора на сайте Google Merchants<br>(https://support.google.com/merchants/answer/1705911)<br>Тип: Строка');
        $field->required    = false;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:age_group';
        $field->title       = t('Возрастная группа');
        $field->hint        = t('newborn, infant, toddler, kids, adult<br>Тип: Строка');
        $field->required    = false;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:color';
        $field->title       = t('Цвет');
        $field->required    = false;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:material';
        $field->title       = t('Материал');
        $field->required    = false;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:size';
        $field->title       = t('Размер');
        $field->required    = false;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:custom_label_0';
        $field->title       = t('Произвольная характеристика 1');
        $field->required    = false;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:custom_label_1';
        $field->title       = t('Произвольная характеристика 2');
        $field->required    = false;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:custom_label_2';
        $field->title       = t('Произвольная характеристика 3');
        $field->required    = false;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:custom_label_3';
        $field->title       = t('Произвольная характеристика 4');
        $field->required    = false;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:custom_label_4';
        $field->title       = t('Произвольная характеристика 5');
        $field->required    = false;
        $fields[$field->name] = $field;

        return $fields;
    }

    /**
     * Комплектации
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param Product $product
     */
    private function writeOfferCategory(ExportProfile $profile, \XMLWriter $writer, Product $product)
    {
        $dirapi = \Catalog\Model\Dirapi::getInstance();
        if ($product['xdir']) {
            $xdirs = array_diff($product['xdir'], $product['xspec']);
            $xdirs = array_slice($xdirs, 0, 10);
            foreach ($xdirs as $xdir) {
                $dirapi->clearFilter();
                $dirapi->setFilter('public', 1);
                $path = $dirapi->getPathToFirst($xdir);
                $arr = [];
                foreach ($path as $dir) {
                    $arr[] = $dir['name'];
                }
                $writer->writeElement("g:product_type", substr(implode(" > ", $arr), 0, 750));
            }
        }
    }

    /**
     * Добавляет при наличии дополнительных данных сведения по комплекатациям
     *
     * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
     * @param \XMLWriter $writer - объект библиотеки для записи XML
     * @param mixed $current_offer - текущая комплектация
     */
    private function writeOfferAdditionalParams(\Export\Model\Orm\ExportProfile $profile, \XMLWriter $writer, $current_offer)
    {
        if (isset($current_offer['propsdata_arr'])) {
            //Параметры для поиска данных в комплектациях
            $tag_names = ['size', 'color', 'gender', 'age_group', 'pattern', 'size_type', 'size_system'];

            $tags = [];
            foreach ($tag_names as $tag_name) {
                if (!empty($profile[$tag_name])) {
                    $tags[$profile[$tag_name]] = $tag_name;
                }
            }

            foreach ($current_offer['propsdata_arr'] as $prop_title => $value) {
                if (isset($tags[$prop_title])) {
                    $writer->writeElement("g:" . $tags[$prop_title], $value);
                }
            }
        }
    }

    /**
     * Записывает доступность товара
     * @param \XMLWriter $writer
     * @param Product $product
     * @param $price
     * @param $offer_index
     */
    private function writeOfferAvaliability(\XMLWriter $writer, Product $product, $price, $offer_index)
    {

        if ($this->shop_config && $product->shouldReserve()){ //Если есть только предзаказ
            $writer->writeElement("g:availability", "preorder");
        } elseif ($this->shop_config['check_quantity']) { //Если нельзя продавать товары, которых нет в наличии
            $available = $product->getNum($offer_index) > 0 && $price > 0;
            $writer->writeElement("g:availability", $available ? "in stock" : "out of stock");
        } else {
            $writer->writeElement("g:availability",  "in stock");
        }
    }

    /**
     * Записывает изображения
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param Product $product
     * @param $current_offer
     */
    private function writeOfferImages(ExportProfile $profile, \XMLWriter $writer, Product $product, $current_offer)
    {
        $images = $product->getImages();
        $offer_images = [];
        if ( $current_offer ){ //Если есть комплектации, посмотим привязани ли фото к конкретной комплектации
            $offer_images = $current_offer['photos_arr'];
            if ( !empty($offer_images) ) {
                foreach ($images as $k=>$image){
                    if ( in_array($image['id'], $offer_images )){
                        $image_url = ( $profile['export_photo_originals'] ) ? $image->getOriginalUrl() : $image->getUrl(800, 800, 'axy');
                        $tag_name = ( $first ?? true ) ? "g:image_link" : "g:additional_image_link"; // первое фото имеет отличный тег
                        $writer->writeElement($tag_name, \RS\Http\Request::commonInstance()->getDomain(true) . $image_url);
                        $first = false;
                    }
                }
            }
        }
        //Если просто товар или фото комплектаций не привязано
        if ( !$current_offer || ($current_offer && empty($offer_images)) ){
            foreach ($images as $k=>$image){
                $image_url = ( $profile['export_photo_originals'] ) ? $image->getOriginalUrl() : $image->getUrl(800, 800, 'axy');
                $tag_name = ( $k == 0 ) ? "g:image_link" : "g:additional_image_link"; // первое фото имеет отличный тег
                $writer->writeElement($tag_name, \RS\Http\Request::commonInstance()->getDomain(true) . $image_url);
            }
        }
    }

    function getShortName()
    {
        return 'simple';
    }

    function getTitle()
    {
        return t('Стандартный');
    }
}