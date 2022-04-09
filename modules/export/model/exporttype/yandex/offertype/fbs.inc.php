<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Yandex\OfferType;

use Catalog\Model\Orm\Product as Product;
use Export\Model\ExportType\Field;
use Export\Model\Orm\ExportProfile as ExportProfile;
use RS\Exception as RSException;

class Fbs extends Simple
{
    /**
     * Возвращает название типа описания
     *
     * @return string
     */
    function getTitle()
    {
        return t('Для Маркета FBS');
    }

    /**
     * Возвращает идентификатор данного типа описания. (только англ. буквы)
     *
     * @return string
     */
    public function getShortName()
    {
        return 'fbs';
    }

    /**
     * Дополняет список "особенных" полей, персональными для данного типа описания
     *
     * @param $fields - массив "особенных" полей
     * @return Filed[]
     */
    protected function addSelfEspecialTags($fields)
    {
        $field = new Field();
        $field->name        = 'period-of-validity-days';
        $field->title       = t('Срок годности (period-of-validity-days)');
        $field->hint       = t('Через какой период товар станет непригоден для использования (в годах, месяцах, днях, неделях или часах). Например, срок годности есть у таких категорий, как продукты питания и медицинские препараты. Должен быть указан в формате P1Y2M10D. Расшифровка примера — 1 год, 2 месяца и 10 дней. Или P15D — 15 дней; P2Y10D — 2 года, 10 дней.');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'comment-validity-days';
        $field->title       = t('Дополнительные условия хранения (comment-validity-days)');
        $field->hint       = t('Например «Хранить в сухом помещении»');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'service-life-days';
        $field->title       = t('Дополнительные условия хранения (service-life-days)');
        $field->hint       = t('Например «Хранить в сухом помещении»');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'comment-life-days';
        $field->title       = t('Дополнительные условия использования (comment-life-days)');
        $field->hint       = t('Например «Использовать при температуре не ниже -10 градусов»');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'warranty-days';
        $field->title       = t('Гарантийный срок (warranty-days)');
        $field->hint       = t('В течение этого периода возможны обслуживание и ремонт товара, возврат денег (в годах, месяцах или днях). Изготовитель или продавец несет ответственность за недостатки товара. Должен быть указан в формате P1Y2M10D. Расшифровка примера — 1 год, 2 месяца и 10 дней.');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'comment-warranty';
        $field->title       = t('Дополнительные условия гарантии (comment-warranty)');
        $field->hint       = t('Например «Гарантия на аккумулятор — 6 месяцев».');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'certificate';
        $field->title       = t('Номер документа. (certificate)');
        $field->hint       = t('Например, сертификат или декларация соответствия и т. п. Если это отказное письмо, укажите в формате: номер аттестата_номер отказного письма');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name       = 'transport-unit';
        $field->title      = t('Количество товаров в упаковке (кратность короба) transport-unit');
        $field->hint       = t('Значение используется, если вы поставляете товар упаковками, а продаете поштучно. Пример: вы продаете детское питание по 1 баночке, а коробка содержит 6 баночек');
        $field->modifier   = function($value) {
            return str_replace([' ', ' '], '', $value);
        };
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name       = 'min-delivery-pieces';
        $field->title      = t('Минимальная партия поставки (min-delivery-pieces)');
        $field->hint       = t('Значение используется, если вы поставляете товар упаковками, а продаете поштучно. Пример: вы продаете детское питание по 1 баночке, а коробка содержит 6 баночек');
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name       = 'quantum';
        $field->title      = t('Добавочная партия (квант поставки) (quantum)');
        $field->hint       = t('Число товаров, которое можно добавлять к минимальной партии. Указывайте количество товаров, а не коробок/упаковок.');
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name       = 'leadtime';
        $field->title      = t('Срок поставки (leadtime)');
        $field->hint       = t('За какое время вы поставите товар на склад (в днях). Введите значение элемента, чтобы получать рекомендации о пополнении товаров на складе.');
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name       = 'box-count';
        $field->title      = t('Количество мест, которое занимает товар. (box-count)');
        $field->hint       = t('Если товар занимает больше одного места, укажите количество мест. Например, кондиционер занимает два места — внешний и внутренний блоки в двух коробках.');
        $fields[$field->name] = $field;

        return $fields;
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
        $tn_veds = $product->getTnVedCodes();
        if ($tn_veds) {
            $writer->startElement('tn-ved-codes');
            foreach($tn_veds as $tn_ved) {
                $writer->writeElement('tn-ved-code', $tn_ved);
            }
            $writer->endElement();
        }

        parent::writeEspecialOfferTags($profile, $writer, $product, $offer_index);
    }

}