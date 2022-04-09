<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\Google\OfferType;

use \Export\Model\ExportType\AbstractOfferType as AbstractOfferType;
use \Export\Model\ExportType\Field;
use \Export\Model\Orm\ExportProfile as ExportProfile;
use \Catalog\Model\Orm\Product as Product;

class Rss2 extends AbstractOfferType
{
    protected
        $shop_config = null;

    /**
    * Возвращает название типа описания
    *
    * @return string
    */
    function getTitle()
    {
        return t('RSS 2.0');
    }

    /**
    * Возвращает идентификатор данного типа описания. (только англ. буквы)
    *
    * @return string
    */
    function getShortName()
    {
        return 'rss_2_0';
    }

    /**
    * Дополняет список "особенных" полей, общими для всех типов описания данного типа экспорта
    *
    * @param $ret - массив "особенных" полей
    * @return Filed[]
    */
    protected function addSelfEspecialTags($fields)
    {
        $field = new Field();
        $field->name        = 'g:google_product_category';
        $field->title       = t('Категория к товарам из классификатора Google(Идентификатор)');
        $field->hint        = t('Категории из классификатора на сайте Google Merchants<br>(https://support.google.com/merchants/answer/1705911)');
        $field->required    = true;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:adult';
        $field->title       = t('Товар принадлежит категории для взрослых?');
        $field->hint        = t('Характеристика Да/Нет. Необязательное.');
        $field->type        = TYPE_BOOLEAN;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'g:condition';
        $field->title       = t('Состояние товара (condition)');
        $field->required    = true;
        $fields[$field->name] = $field;

        return $fields;
    }

    /**
    * Запись товарного предложения
    *
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param integer $offer_id - индекс комплектации для отображения
    */
    public function writeOffer(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_id)
    {
        $http_request = \RS\Http\Request::commonInstance();
        $request_host = $http_request->getProtocol() . '://' . $http_request->getDomainStr();
        if ($this->shop_config === null) {
            $this->shop_config = \RS\Config\Loader::byModule('shop');
        }

        if (!$product->hasImage()){ //Если нет фото
            return;
        }

        /**
        * @var \Catalog\Model\Orm\Offer
        */
        $current_offer = false; //Текущая комплектация

        $writer->startElement("item");
            $this->fireOfferEvent('beforewriteoffer', $profile, $writer, $product, $offer_id);

            if ($offer_id!==false){
                $writer->writeAttribute('g:item_group_id', $product->id);
            }

            //Дополнительные параметры адресе страницы
            $url_params = false;

            if (!empty($profile['url_params'])){
                $url_params = htmlspecialchars_decode($profile['url_params']);
            }

            $title = strip_tags(trim($product->title.' '.(($offer_id !== false && !$profile->no_export_offers) ? $product->getOfferTitle($offer_id) : '')));
            $writer->writeElement("g:title", $title);
            $writer->writeElement("g:link", $request_host . $product->getUrl() . ($url_params ? "?".$url_params : "") . ( $offer_id ? '#'.$offer_id : '' ));

            //Добавим описание
            $description = $product->short_description;
            if ($offer_id!==false){
                /**
                * @var \Catalog\Model\Orm\Offer
                */
                $current_offer = $product['offers']['items'][$offer_id];//Текущее предложение
                //Если есть доп параметры модели, то добавим их в конец
                if (isset($current_offer['propsdata_arr']) && !empty($current_offer['propsdata_arr'])){
                    $arr = [];
                    foreach ($current_offer['propsdata_arr'] as $key=>$value){
                        $arr[] = $key.": ".$value;
                    }
                    $description .= " ".implode(", ", $arr);
                }

            }
            $writer->writeElement("g:description", strip_tags($description));
            $barcode = $product->getBarCode($offer_id);
            //Уникаьлный артикул
             $profilegoogle =$profile->getTypeObject();
             $gid = (!$current_offer || ($profilegoogle->no_export_offers)) ? $barcode : $barcode."-".$current_offer['id'];
             $final_gid = $gid !== '' ? $gid : (($current_offer) ? $current_offer['id'] : $product['id']);
             $writer->writeElement("g:id", $final_gid);
            //Категории
            $this->writeOfferCategory($profile, $writer, $product);

            //Картинка и картинки
            $this->writeOfferImages($profile, $writer, $product, $current_offer);

            // Берем цену по-умолчанию
            $prices = $product->getOfferCost($offer_id, $product['xcost']);
             if (!empty($profile['export_cost_id'])) {
                 $price = $prices[$profile['export_cost_id']];
             } else {
                $price = $prices[\Catalog\Model\Costapi::getDefaultCostId()];
             }
            $old_price = 0;
            if ($old_cost_id = \Catalog\Model\CostApi::getOldCostId()) {
                $old_price = $prices[ $old_cost_id ];
            }

            //Доступность
            $this->writeOfferAvaliability($writer, $product, $price, $offer_id);

            //Цена
            if ($old_price > 0){ //Если есть старая цена продажи
                $writer->writeElement("g:price", $old_price." ".\Catalog\Model\CurrencyApi::getDefaultCurrency()->title);
                $writer->writeElement("g:sale_price", $price." ".\Catalog\Model\CurrencyApi::getDefaultCurrency()->title);
            }else{
                $writer->writeElement("g:price", $price." ".\Catalog\Model\CurrencyApi::getDefaultCurrency()->title);
            }

            //Бренд
            $brand = $product->getBrand();
            if (isset($brand['id']) && $brand['id']){
                $writer->writeElement("g:brand", $brand['title']);
            }
            //Штрихкод
            $sku = $product->getSKU($offer_id);
            if (!empty($sku)) {
                $writer->writeElement("g:gtin", $sku);
            }

            $this->writeEspecialOfferTags($profile, $writer, $product, $offer_id);

            //Вес товара
            if ($product->getWeight($offer_id) && !$profile['not_use_shipping_weight_tag']){
                $weight = ($product->getWeight($offer_id))/1000;
                $writer->writeElement("g:shipping_weight", $weight.' kg');
            }

            //Дополнительные сведения если, это комплектация
            if ($current_offer){
                $this->writeOfferAdditionalParams($profile, $writer, $current_offer);
            }

            $this->fireOfferEvent('writeoffer', $profile, $writer, $product, $offer_id);
        $writer->endElement();
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
        //Параметры для поиска данных в комплектациях
        $tag_names = ['size', 'color', 'gender', 'age_group', 'pattern', 'size_type', 'size_system'];

        $tags = [];
        foreach ($tag_names as $tag_name) {
            if (!empty($profile[$tag_name])) {
                $tags[$profile[$tag_name]] = $tag_name;
            }
        }

        if ($current_offer['propsdata_arr']) {
            foreach ($current_offer['propsdata_arr'] as $prop_title => $value) {
                if (isset($tags[$prop_title])) {
                    $writer->writeElement("g:".$tags[$prop_title], $value);
                }
            }
        }
    }

    /**
    * Добавляет сведения о доступности товара
    *
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param float $price - индекс комплектации для отображения
    * @param integer $offer_id - id комплектации для отображения
    */
    private function writeOfferAvaliability(\XMLWriter $writer, \Catalog\Model\Orm\Product $product, $price, $offer_id)
    {
        if ($this->shop_config && $product->shouldReserve()){ //Если есть только предзаказ
            $writer->writeElement("g:availability", "preorder");
        }else{
            // Определяем доступность товара
            $available = $product->getNum($offer_id) > 0 && $price > 0;
            $writer->writeElement("g:availability", $available ? "in stock" : "out of stock");
        }
    }

    /**
    * Добавляет в XML сведения о категории в которой должен хранится товар или комплектация
    *
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    */
    private function writeOfferCategory(\Export\Model\Orm\ExportProfile $profile, \XMLWriter $writer, \Catalog\Model\Orm\Product $product)
    {
        $dirapi = \Catalog\Model\Dirapi::getInstance();
        $xdirs = array_diff($product['xdir'], $product['xspec']);
        $xdirs = array_slice($xdirs, 0, 10);
        foreach ($xdirs as $xdir){
            $dirapi->clearFilter();
            $dirapi->setFilter('public', 1);
            $path   = $dirapi->getPathToFirst($xdir);

            $arr = [];
            foreach ($path as $dir){
               $arr[] = $dir['name'];
            }
            $writer->writeElement("g:product_type", substr(implode(" > ", $arr), 0, 750));
        }
    }

    /**
    * Добавляет в XML сведения с фото для товара или комплектации
    *
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param \Catalog\Model\Orm\Offer|false $current_offer - текущая комплектация, объект или false
    */
    private function writeOfferImages(\Export\Model\Orm\ExportProfile $profile, \XMLWriter $writer, \Catalog\Model\Orm\Product $product, $current_offer)
    {
        $images = $product->getImages();
        $offer_images = [];
        if ($current_offer){ //Если есть комплектации, посмотим привязани ли фото к конкретной комплектации
            $offer_images = $current_offer['photos_arr'];
            if (!empty($offer_images)){

                $first = false;
                $image_count = 0;
                foreach ($images as $k => $image) {
                    if (in_array($image['id'], $offer_images) && $image_count < 10) {
                        $image_count++;
                        $image_url = ($profile['export_photo_originals']) ? $image->getOriginalUrl(true) : $image->getUrl(800, 800, 'axy', true);
                        if (!$first) { //Первое фото
                            $writer->writeElement("g:image_link", $image_url);
                            $first = true;
                        } else {
                            $writer->writeElement("g:additional_image_link", $image_url);
                        }
                    }
                }
            }
        }
        //Если просто товар или фото комплектаций не привязано
        if (!$current_offer || ($current_offer && empty($offer_images))){
            $first = false;
            $image_chunk = array_chunk($images, 10);
            foreach (reset($image_chunk) as $k => $image) {
                $image_url = ($profile['export_photo_originals']) ? $image->getOriginalUrl(true) : $image->getUrl(800, 800, 'axy', true);
                if (!$first) { //Первое фото
                    $writer->writeElement("g:image_link", $image_url);
                    $first = true;
                } else {
                    $writer->writeElement("g:additional_image_link", $image_url);
                }
            }
        }
    }



    /**
    * Возвращает массив идентификаторов выбранных пользователем групп товаров
    *
    * @param \Export\Model\Orm\ExportProfile $profile
    * @return array
    */
    private function getSelectedProductGroupIds(\Export\Model\Orm\ExportProfile $profile)
    {
        //Возвращаем основные группы товаров, без спецкатегорий.
        $selected_product_ids = $this->getSelectedProductIds($profile);
        if(empty($selected_product_ids)) return [];
        $groups_ids = \RS\Orm\Request::make()
            ->select('maindir')
            ->from(new \Catalog\Model\Orm\Product())
            ->where(['public' => 1])
            ->whereIn('id', $selected_product_ids)
            ->exec()
            ->fetchSelected(null, 'maindir');
        return array_unique($groups_ids);
    }

    /**
    * Возвращает массив выбранных пользователем групп товаров
    *
    * @param \Export\Model\Orm\ExportProfile $profile
    * @return array
    */
    private function getSelectedProductGroups(\Export\Model\Orm\ExportProfile $profile)
    {
        $selected_product_group_ids = $this->getSelectedProductGroupIds($profile);
        if(empty($selected_product_group_ids)) return [];
        return \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Dir())
            ->whereIn('id', $selected_product_group_ids)
            ->objects();
    }
}
