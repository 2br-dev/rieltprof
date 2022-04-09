<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\AliExpress\OfferType;

use Catalog\Model\Orm\Product;
use Export\Model\ExportType\Yandex\OfferType\Simple;
use Export\Model\Orm\ExportProfile;
use RS\Exception as RSException;

class Yml extends Simple
{
    /**
     * Возвращает название типа описания
     *
     * @return string
     */
    function getTitle()
    {
        return 'YML';
    }

    /**
     * Возвращает идентификатор данного типа описания. (только англ. буквы)
     *
     * @return string
     */
    public function getShortName()
    {
        return 'yml';
    }

    /**
     * Запись "Особенных" полей, для данного типа описания
     * Перегружается в потомке. По умолчанию выводит все поля в соответсвии с fieldmap
     *
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param Product $product
     * @param mixed $offer_index
     * @throws RSException
     */
    function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        parent::writeEspecialOfferTags($profile, $writer, $product, $offer_index);

        $writer->writeElement('quantity', $product->getNum($offer_index));
    }

    /**
     * Возвращает ID для тега offer
     *
     * @param Product $product
     * @param Integer $offer_id
     * @return string
     */
    protected function getOfferId($product, $offer_id)
    {
        if (!$offer_id) {
            return $product->getMainOffer()->id;
        }

        return $offer_id;
    }
}
