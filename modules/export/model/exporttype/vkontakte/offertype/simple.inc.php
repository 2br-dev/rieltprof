<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Vkontakte\OfferType;
use Export\Model\ExportType\AbstractOfferType;
use \Export\Model\Orm\ExportProfile as ExportProfile;
use \Catalog\Model\Orm\Product as Product;

/**
 * Простой тип экспорта описания
 */
class Simple extends AbstractOfferType
{

    /**
     * Возвращает название типа описания
     *
     * @return string
     */
    function getTitle()
    {
        return t('Стандартный');
    }

    /**
     * Возвращает идентификатор данного типа описания. (только англ. буквы)
     *
     * @return string
     */
    function getShortName()
    {
        return 'standard';
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
    {}
}
