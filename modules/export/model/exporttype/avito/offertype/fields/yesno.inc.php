<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Avito\OfferType\Fields;

use \Export\Model\ExportType;
use \Export\Model\Orm\ExportProfile;
use \Catalog\Model\Orm\Product;

/**
 * Структура данных, описывающая поле в экспортируемом XML документе
 */
class YesNo extends ExportType\Field implements ExportType\ComplexFieldInterface
{
    /**
    * Добавляет необходимую структуру тегов в итоговый XML
    * 
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param integer $offer_index - индекс комплектации для отображения
    */
    function writeSomeTags(\XMLWriter $writer, ExportProfile $profile, Product $product, $offer_index = null)
    {
        $value = $this->getValue($this->name, $profile, $product);
        $writer->writeElement($this->name, ($value) ? 'Да' : 'Нет'); // Не обёрнуто в t(), потому что Авито принимает только Да/Нет
    }
}