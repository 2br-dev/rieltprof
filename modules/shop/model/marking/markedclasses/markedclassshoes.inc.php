<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Marking\MarkedClasses;

use Shop\Model\Marking\MarkingApi;

class MarkedClassShoes extends AbstractMarkedClass
{
    /**
     * Возвращает имя класса маркированых товаров
     *
     * @return string
     */
    public static function getName():string
    {
        return 'shoes';
    }

    /**
     * Возвращает публичное имя класса маркированых товаров
     *
     * @return string
     */
    public static function getTitle():string
    {
        return t('Обувь');
    }

    /**
     * Разбивает УИТ на составные части
     *
     * @param string $code - УИТ в текстовом виде
     * @return string[]
     */
    public static function parseCode(string $code):array
    {
        preg_match('/01(\d{14})21(\w{13})/', $code, $matches);

        $result = [
            MarkingApi::USE_ID_GTIN => $matches[1] ?? null,
            MarkingApi::USE_ID_SERIAL => $matches[2] ?? null,
        ];

        return $result;
    }
}
