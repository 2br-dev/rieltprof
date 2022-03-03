<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\Yandex\OfferType;

use Catalog\Model\Orm\Product as Product;
use Export\Model\Orm\ExportProfile as ExportProfile;
use RS\Orm\Exception as OrmException;

class Simple extends CommonOfferType
{
    /**
     * Возвращает название типа описания
     *
     * @return string
     */
    function getTitle()
    {
        return t('Упрощенное');
    }

    /**
     * Возвращает идентификатор данного типа описания. (только англ. буквы)
     *
     * @return string
     */
    public function getShortName()
    {
        return 'simple';
    }

    /**
     * Запись "Особенных" полей, для данного типа описания
     * Перегружается в потомке. По умолчанию выводит все поля в соответсвии с fieldmap
     *
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param Product $product
     * @param mixed $offer_index
     * @throws OrmException
     */
    function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        $name = $product->title . ' ' . (($offer_index !== false && !$profile->no_export_offers && !$profile->no_export_offers_title) ? $product->getOfferTitle($offer_index) : '');
        $writer->writeElement('name', $this->use_htmlentity ? htmlspecialchars_decode($name) : $name);
        if ($vendor = $product->getBrand()->title) {
            $vendor_str = $this->use_htmlentity ? htmlspecialchars_decode($vendor) : $vendor;
            $writer->writeElement('vendor', $vendor_str);
            if ($profile->export_fb_model) {
                $writer->writeElement('manufacturer', $vendor_str);
            }
        }

        parent::writeEspecialOfferTags($profile, $writer, $product, $offer_index);
    }
}
