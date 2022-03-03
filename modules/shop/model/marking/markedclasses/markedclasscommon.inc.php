<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Marking\MarkedClasses;

use Shop\Model\Marking\MarkingApi;
use Shop\Model\Marking\MarkingException;
use Shop\Model\Orm\OrderItemUIT;

class MarkedClassCommon extends AbstractMarkedClass
{
    protected $name;
    protected $title;
    protected $marking_type_id;

    public function __construct(string $name, string $title, string $marking_type_id = '444d')
    {
        $this->name = $name;
        $this->title = $title;
        $this->marking_type_id = $marking_type_id;
    }

    /**
     * Возвращает код товара в шестнадцатеричном представлении
     *
     * @param OrderItemUIT $uit
     * @return string
     */
    public function getNomenclatureCode(OrderItemUIT $uit): string
    {
        $tag_name_part = '8A04';
        $data_length_part = '1500';
        $marking_type_part = $this->getMarkingTypeId();
        $gtin_part = str_pad(dechex((int)$uit['gtin']), 12, '0', STR_PAD_LEFT);
        $serial_part = bin2hex($uit['serial']);

        $result = strtoupper($tag_name_part . $data_length_part . $marking_type_part . $gtin_part . $serial_part);
        $result = trim(chunk_split($result, 2, ' '));

        return $result;
    }

    /**
     * Разбивает УИТ на составные части
     *
     * @param string $code - УИТ в текстовом виде
     * @return string[]
     * @throws MarkingException
     */
    protected function parseCode(string $code): array
    {
        preg_match('/01(\d{14})21(\w{13})/', $code, $matches);

        if (empty($matches)) {
            throw new MarkingException(t('Некорректный код'), MarkingException::ERROR_SINGLE_CODE_PARSE);
        }

        $result = [
            MarkingApi::USE_ID_GTIN => $matches[1] ? ltrim($matches[1], '0') : null,
            MarkingApi::USE_ID_SERIAL => $matches[2] ?? null,
        ];

        return $result;
    }

    /**
     * Возвращает имя класса маркированых товаров
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает публичное имя класса маркированых товаров
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Возвращает код типа маркировки
     *
     * @return string
     */
    public function getMarkingTypeId(): string
    {
        return $this->marking_type_id;
    }
}
