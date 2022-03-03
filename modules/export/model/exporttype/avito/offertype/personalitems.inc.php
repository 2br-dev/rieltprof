<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
/**
 * Тип объявления Avito "Личные вещи"
 */

namespace Export\Model\ExportType\Avito\OfferType;

use \Export\Model\Orm\ExportProfile;
use \Export\Model\ExportType\Field;
use \Catalog\Model\Orm\Product as Product;

class PersonalItems extends CommonAvitoOfferType
{
    /**
     * Возвращает название типа описания
     *
     * @return string
     */
    function getTitle()
    {
        return t('Личные вещи');
    }

    /**
     * Возвращает идентификатор данного типа описания. (только англ. буквы)
     *
     * @return string
     */
    function getShortName()
    {
        return 'personal_items';
    }

    /**
     * Получить список "особенных" полей для данного типа описания
     * Возвращает массив объектов класса Field.
     *
     * @param string $exporttype_name - короткое название типа экспорта
     * @return Filed[]
     */
    protected function addSelfEspecialTags($fields)
    {
        $field = new Field();
        $field->name        = 'Category';
        $field->title       = t('Категория товара');
        $field->hint        = t('Поле "Category" (Все категории)<br><br>
                                Одно из значений списка:<br>
                                - Одежда, обувь, аксессуары<br>
                                - Детская одежда и обувь<br>
                                - Товары для детей и игрушки<br>
                                - Часы и украшения<br>
                                - Красота и здоровье');
        $field->required    = true;
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'GoodsType';
        $field->title       = t('Вид товара');
        $field->hint        = t('Поле "GoodsType" (Все категории)<br><br>
                                 Одно из значений списка (отдельно для каждой категории):<br>
                                 - Для категории «Одежда, обувь, аксессуары»:<br>
                                 &nbsp;&nbsp;- Женская одежда<br>
                                 &nbsp;&nbsp;- Мужская одежда<br>
                                 &nbsp;&nbsp;- Аксессуары<br><br>
                                 Остальные категории см. в документации Avito
                                 ');
        $field->required    = true;
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'AdType';
        $field->title       = t('Вид объявления');
        $field->hint        = t('Поле "AdType" (Все категории)<br><br>
                                Одно из значений списка:<br>
                                - Товар приобретен на продажу<br>
                                - Товар от производителя');
        $field->required    = true;
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'Apparel';
        $field->title       = t('Предмет одежды');
        $field->hint        = t('Поле "Apparel" <br>(Для категорий "Одежда, обувь, аксессуары", "Детская одежда и обувь")<br><br>
                                 Значения см. в документации Avito');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'Size';
        $field->title       = t('Размер одежды или обуви');
        $field->hint        = t('Поле "Size" <br>(Для категорий "Одежда, обувь, аксессуары", "Детская одежда и обувь")<br><br>
                                 Значения см. в документации Avito');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'Condition';
        $field->title       = t('Состояние вещи');
        $field->hint        = t('Поле "Condition" <br>(Для категорий "Детская одежда и обувь", "Товары для детей и игрушки")<br><br>
                                 Одно из значений списка:<br>
                                 - Новый<br>
                                 - Б/у');
        $fields[$field->name]  = $field;

        return $fields;
    }

    /**
     * Запись товарного предложения
     *
     * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
     * @param \XMLWriter $writer - объект библиотеки для записи XML
     * @param \Catalog\Model\Orm\Product $product - объект товара
     * @param integer $offer_index - индекс комплектации для отображения
     */
    public function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        $title = mb_substr($product['title'], 0, 50);
        $product_description = html_entity_decode(strip_tags($product['short_description'] ?: $product['description']));
        $description = "{$product['title']}.\n {$product_description}";

        parent::writeEspecialOfferTags($profile, $writer, $product, $offer_index);
        $writer->writeElement('Title', $title);
        $writer->writeElement('Description', $description);
    }

}