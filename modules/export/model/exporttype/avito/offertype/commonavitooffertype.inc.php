<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\Avito\OfferType;

use Catalog\Model\CostApi;
use Catalog\Model\Orm\Offer;
use Export\Model\ExportType\AbstractOfferType as AbstractOfferType;
use Export\Model\ExportType\Field as Field;
use Export\Model\Orm\ExportProfile as ExportProfile;
use Catalog\Model\Orm\Product as Product;
use RS\Exception as RSException;
use RS\Http\Request as HttpRequest;

abstract class CommonAvitoOfferType extends AbstractOfferType
{
    /**
    * Дополняет список "особенных" полей, общими для всех типов описания данного типа экспорта
    * 
    * @param $fields - массив "особенных" полей
    * @return Field[]
    */
    protected function addCommonEspecialTags($fields)
    {
        $field = new Field();
        $field->name        = 'ListingFee';
        $field->title       = t('Вариант платного размещения');
        $field->hint        = t('Значение из списка, см. документацию Avito');
        $fields[$field->name] = $field;
        
        $field = new Field();
        $field->name        = 'AdStatus';
        $field->title       = t('Платная услуга, которую нужно применить к объявлению');
        $field->hint        = t('Значение из списка, см. документацию Avito');
        $fields[$field->name] = $field;
        
        $field = new Field();
        $field->name        = 'AvitoId';
        $field->title       = t('Номер объявления на Avito');
        $fields[$field->name] = $field;
        
        $field = new Fields\YesNo();
        $field->name        = 'AllowEmail';
        $field->title       = t('Возможность написать сообщение по объявлению через сайт');
        $field->hint        = t('Характеристика Да/Нет');
        $field->type        = TYPE_BOOLEAN;
        $fields[$field->name] = $field;
        
        $field = new Field();
        $field->name        = 'ManagerName';
        $field->title       = t('Имя менеджера, контактного лица компании по данному объявлению');
        $field->hint        = t('Не более 40 символов');
        $fields[$field->name] = $field;
        
        $field = new Field();
        $field->name        = 'ContactPhone';
        $field->title       = t('Контактный телефон по данному объявлению');
        $field->hint        = t('Только один российский номер телефона.<br>Должен быть обязательно указан код города или мобильного оператора');
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'Address';
        $field->title       = t('Полный адрес объекта');
        $field->hint        = t('Поле "Address"<br><br>
                                Максимум 256 символов.<br>
                                Примечание: Улица и номер дома не будут показаны в объявлении<br><br>
                                Пример заполнения: Россия, Тамбовская область, Моршанск, Лесная улица, 7');
        $field->required    = true;
        $fields[$field->name] = $field;

        return $fields;
    }

    /**
     * Запись товарного предложения
     *
     * @param ExportProfile $profile - объект профиля экспорта
     * @param \XMLWriter $writer - объект библиотеки для записи XML
     * @param Product $product - объект товара
     * @param integer $offer_id - индекс комплектации для отображения
     * @throws RSException
     */
    public function writeOffer(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_id)
    {
        $writer->startElement("Ad");
            $this->fireOfferEvent('beforewriteoffer', $profile, $writer, $product, $offer_id);

            $writer->writeElement('Id', $product->id.'x'.$offer_id);
            $this->writeEspecialOfferTags($profile, $writer, $product, $offer_id);
            $prices = $product->getOfferCost($offer_id, $product['xcost']);
        if (!empty($profile['export_cost_id'])) {
            $price = ceil($prices[$profile['export_cost_id']]);
        } else {
            $price = ceil($prices[CostApi::getDefaultCostId()]);
        }
            $writer->writeElement('Price', $price);
            if ($product->hasImage()) {
                $this->writeOfferImages($profile, $writer, $product, $offer_id);
            }

            $this->fireOfferEvent('writeoffer', $profile, $writer, $product, $offer_id);
        $writer->endElement();
    }

    /**
    * Добавляет в XML сведения с фото для товара или комплектации
    * 
    * @param ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param Product $product - объект товара
    * @param Offer|false $current_offer - текущая комплектация, объект или false
    */
    protected function writeOfferImages(ExportProfile $profile, \XMLWriter $writer, Product $product, $current_offer)
    {
        $writer->startElement('Images');
        $images = $product->getImages();
        $offer_images = [];
        if ($current_offer) { //Если есть комплектации, посмотрим привязаны ли фото к конкретной комплектации
            $offer_images = $current_offer['photos_arr'];
            if (!empty($offer_images)) {
                foreach ($images as $k => $image) {
                    if (in_array($image['id'], $offer_images)) {
                        $image_url = ($profile['export_photo_originals']) ? $image->getOriginalUrl() : $image->getUrl(800, 800, 'axy');
                        $writer->startElement('Image');
                            $writer->writeAttribute('url', HttpRequest::commonInstance()->getDomain(true) . $image_url);
                        $writer->endElement();
                    }
                }
            }
        }
        //Если просто товар или фото комплектаций не привязано
        if (!$current_offer || ($current_offer && empty($offer_images))) {
            foreach ($images as $k => $image) {
                $image_url = ($profile['export_photo_originals']) ? $image->getOriginalUrl() : $image->getUrl(800, 800, 'axy');
                $writer->startElement('Image');
                    $writer->writeAttribute('url', HttpRequest::commonInstance()->getDomain(true) . $image_url);
                $writer->endElement();
            }
        }
        $writer->endElement();
    }
}
