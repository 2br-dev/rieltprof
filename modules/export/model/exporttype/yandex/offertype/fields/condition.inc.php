<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\Yandex\OfferType\Fields;

use \Catalog\Model\Orm\Product;
use \Export\Model\ExportType;
use \Export\Model\Orm\ExportProfile;

/**
 * Структура данных, описывающая поле в экспортируемом XML документе
 */
class Condition extends ExportType\Field implements ExportType\ComplexFieldInterface
{
    /**
     * Добавляет необходимую структуру тегов в итоговый XML
     *
     * @param \XMLWriter $writer - объект библиотеки для записи XML
     * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
     * @param \Catalog\Model\Orm\Product $product - объект товара
     * @param integer $offer_index - индекс комплектации для отображения
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function writeSomeTags(\XMLWriter $writer, ExportProfile $profile, Product $product, $offer_index = null)
    {
        $export_type_object = $profile->getTypeObject();
        if ($this->name === 'reason_ym') return;
        if (isset($export_type_object['fieldmap'][$this->name]) && $export_type_object['fieldmap'][$this->name]['prop_id'] != 0) {
            if (!empty($export_type_object['fieldmap'][$this->name]['prop_id']) && $export_type_object['fieldmap'][$this->name]['prop_id'] != -1) {
                $prop_id = (int)$export_type_object['fieldmap'][$this->name]['prop_id'];
                $value = $product->getPropertyValueById($prop_id);
            } else {
                $value = $export_type_object['fieldmap'][$this->name]['value'];
            }
            if ($value) {
                if (isset($export_type_object['fieldmap']['reason_ym'])) {
                    if (!empty($export_type_object['fieldmap']['reason_ym']['prop_id']) && $export_type_object['fieldmap']['reason_ym']['prop_id'] != -1) {
                        $prop_id = (int)$export_type_object['fieldmap']['reason_ym']['prop_id'];
                        $reason = $product->getPropertyValueById($prop_id);
                    } else {
                        $reason = $export_type_object['fieldmap']['reason_ym']['value'];
                    }
                    $writer->startElement('condition');
                    $writer->writeAttribute('type', $value);
                    $writer->writeElement('reason', $reason);
                    $writer->endElement();
                }
            }
        }
    }
}
