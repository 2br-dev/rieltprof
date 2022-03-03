<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\Yandex\OfferType\Fields;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Orm\Product;
use Export\Model\ExportType;
use Export\Model\Orm\ExportProfile;
use RS\Exception as RSException;

/**
 * Структура данных, описывающая поле в экспортируемом XML документе
 */
class FieldWeight extends ExportType\Field implements ExportType\ComplexFieldInterface
{
    /**
     * Добавляет необходимую структуру тегов в итоговый XML
     *
     * @param ExportProfile $profile - объект профиля экспорта
     * @param \XMLWriter $writer - объект библиотеки для записи XML
     * @param Product $product - объект товара
     * @param integer $offer_index - индекс комплектации для отображения
     * @throws RSException
     */
    function writeSomeTags(\XMLWriter $writer, ExportProfile $profile, Product $product, $offer_index = null)
    {
        $weight = $product->getWeight($offer_index, ProductApi::WEIGHT_UNIT_KG);
        if ($weight > 0) {
            $writer->startElement('weight');
            $writer->text($weight);
            $writer->endElement();
        }
    }
}
