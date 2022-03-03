<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType;

use Catalog\Model\Orm\Property\Item as PropertyItem;
use Export\Model\MyXMLWriter;
use Export\Model\Orm\ExportProfile as ExportProfile;
use Catalog\Model\Orm\Product as Product;
use Photo\Model\Orm\Image as PhotoImage;
use RS\Event\Manager as EventManager;
use RS\Event\Result as EventResult;
use RS\Exception as RSException;
use RS\Http\Request as HttpRequest;
use RS\Site\Manager as SiteManager;

abstract class AbstractOfferType
{
    protected $export_type_name;

    /**
     * Возвращает название типа описания
     *
     * @return string
     */
    abstract function getTitle();

    /**
     * Возвращает идентификатор данного типа описания. (только англ. буквы)
     *
     * @return string
     */
    abstract function getShortName();

    /**
     * Устанавливает идентификатор типа экспорта
     *
     * @param string $export_type_name - идентификатор типа экспорта
     * @return void
     */
    public function setExportTypeName($export_type_name)
    {
        $this->export_type_name = $export_type_name;
    }

    /**
     * Получить список "особенных" полей для данного типа описания
     * Возвращает массив объектов класса Field.
     *
     * @return Field[]
     */
    public function getEspecialTags()
    {
        $fields = [];
        // Начинаем с общих полей типа экспорта
        $fields = $this->addCommonEspecialTags($fields);
        // Добавляем поля, персональные для типа описания
        $fields = $this->addSelfEspecialTags($fields);
        // Добавим дополнительные поля через событие
        $fields = $this->addCustomEspecialTags($fields);

        return $fields;
    }

    /**
     * Дополняет список "особенных" полей, общими для всех типов описания данного типа экспорта
     *
     * @param $fields - массив "особенных" полей
     * @return Field[]
     */
    protected function addCommonEspecialTags($fields)
    {
        return $fields;
    }

    /**
     * Дополняет список "особенных" полей, персональными для данного типа описания
     *
     * @param $fields - массив "особенных" полей
     * @return Field[]
     */
    protected function addSelfEspecialTags($fields)
    {
        return $fields;
    }

    /**
     * Дополняет список "особенных" полей для данного типа описания, полученными через событие
     * Возвращает модифицированный массив объектов полей.
     *
     * @param array $fields - массив полей
     * @return Field[]
     */
    protected function addCustomEspecialTags($fields)
    {
        $class_name_pieces = explode('\\', get_called_class());
        $offer_type_name = strtolower(end($class_name_pieces));
        $event_name = 'export.' . $this->export_type_name . '.getespecialtags.' . $offer_type_name;
        $result = EventManager::fire($event_name, $fields);
        return $result->getResult();
    }

    /**
     * Запись товарного предложения
     *
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param mixed $product
     * @param mixed $offer_index
     */
    abstract public function writeOffer(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index);

    /**
     * Получение значения unit для экспорта
     * @param Product $product - товар
     * @param string $key - название характеристики у комплектации
     * @return mixed
     */
    function getPropUnitForExport($product, $key)
    {
        static $cache = [];

        if (!isset($cache[$key])) {
            if (isset ($product['multioffers']['levels'])) {
                $cache[$key] = false;
                foreach ($product['multioffers']['levels'] as $item) {
                    if ($item['title'] == $key) {
                        $prop = PropertyItem::loadByWhere(['id' => $item['prop_id']]);
                        $cache[$key] = $prop['unit_export'];
                        break;
                    }
                }
            } else {
                $prop = PropertyItem::loadByWhere([
                    'title' => $key,
                    'site_id' => SiteManager::getSiteId(),
                ]);
                $cache[$key] = $prop['unit_export'];
            }
        }

        return $cache[$key];
    }

    /**
     * Выгрузка всех изображений товара, если у оффера не указаны конкретные изображения
     *
     * @param Product $product - товар
     * @param ExportProfile $profile - профиль экспорта
     * @param MyXMLWriter $writer
     */
    function writeProductPictures(Product $product, $profile, $writer)
    {
        $http_request = HttpRequest::commonInstance();
        $request_host = $http_request->getProtocol() . '://' . $http_request->getDomainStr();
        /** @var PhotoImage[] $images */
        $images = array_chunk($product->getImages(), 10); // Yandex допускает не более 10 фото на одно предложение
        foreach (reset($images) as $image) {
            $image_url = ($profile['export_photo_originals']) ? $image->getOriginalUrl() : $image->getUrl(800, 800, 'axy');
            $writer->writeElement('picture', $request_host . $image_url);
        }
    }

    /**
     * Выгрузка изображений, согласно привязки к комплектации
     *
     * @param $product
     * @param $offer_index
     * @param $profile
     * @param \XMLWriter $writer
     */
    function writeOfferPictures($product, $offer_index, $profile, \XMLWriter $writer)
    {
        $item = $product['offers']['items'][$offer_index];
        $n = 0;
        foreach ($item['photos_arr'] as $imageid) {
            $image = new PhotoImage($imageid);
            if ($n < 10) {
                $image_url = ($profile['export_photo_originals']) ? $image->getOriginalUrl() : $image->getUrl(800, 800, 'axy');
                $writer->writeElement('picture', HttpRequest::commonInstance()->getDomain(true) . $image_url);
                $n++;
            }
        }
    }

    /**
     * Запись элемента в соответсвии с настройками сопоставления полей экспорта свойствам товара
     *
     * @param Field $field
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param Product $product
     * @param int $offer_index
     * @throws RSException
     */
    protected function writeElementFromFieldmap(Field $field, ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index = null)
    {
        if ($field instanceof ComplexFieldInterface) {
            $field->writeSomeTags($writer, $profile, $product, $offer_index);
        } else {
            $value = $this->getElementFromFieldmap($field, $profile, $writer, $product);
            if (!empty($value)) {
                $writer->writeElement($field->name, $value);
            }
        }
    }

    /**
     * Получить элемент в соответсвии с настройками сопоставления полей экспорта свойствам товара
     *
     * @param Field $field
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param Product $product
     * @return string
     * @throws RSException
     */
    protected function getElementFromFieldmap(Field $field, ExportProfile $profile, \XMLWriter $writer, Product $product)
    {
        // Получаем объект типа экспорта (в нем хранятся соотвествия полей - fieldmap)
        $export_type_object = $profile->getTypeObject();
        if (!empty($export_type_object['fieldmap'][$field->name]['prop_id'])) {
            // Идентификатор свойстава товара
            $property_id = (int)$export_type_object['fieldmap'][$field->name]['prop_id'];
            // Значение по умолчанию
            $default_value = $export_type_object['fieldmap'][$field->name]['value'];
            // Получаем значение свойства товара
            $value = $product->getPropertyValueById($property_id);
            // Если яндекс ожидает строку (true|false)
            if ($field->type == TYPE_BOOLEAN) {
                // Если значение свойства 1 или непустая строка - выводим 'true', в противном случае 'false'

                if ($field->boolAsInt) {
                    return $value === 'есть' ? '1' : (!isset($value) ? '1' : '0');
                }
                if ((!$value || $value == t('нет')) && (!$default_value || $default_value == t('нет'))) {
                    return "false";
                }
                return "true";
            } else {
                // Выводим значение свойства, либо значение по умолчанию
                return $value === null ? $default_value : $value;
            }
        }
        return null;
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
    protected function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        foreach ($this->getEspecialTags() as $field) {
            $this->writeElementFromFieldmap($field, $profile, $writer, $product, $offer_index);
        }
    }

    /**
     * Событие, которое вызывается при записи каждого товарного предложения
     *
     * @param string $event_name - уникальная часть итогового имени события
     * @param ExportProfile $profile - объект профиля экспорта
     * @param \XMLWriter $writer - объект библиотеки для записи XML
     * @param Product $product - объект товара
     * @param integer $offer_index - индекс комплектации для отображения
     * @return EventResult
     */
    protected function fireOfferEvent($event_name, ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        $event_name = "export.{$profile['class']}.$event_name";
        $export_params = [
            'profile' => $profile,
            'writer' => $writer,
            'product' => $product,
            'offer_index' => $offer_index
        ];

        return EventManager::fire($event_name, $export_params);
    }
}
