<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Application\Microdata;

/**
 * Интерфейс микроразметки Schema.org в формате JSON-LD
 */
interface InterfaceMicrodataSchemaOrgJsonLd
{
    /**
     * Возвращает данные для микроразметки Schema.org в формате JSON-LD
     *
     * @return array
     */
    function getSchemaOrgJsonLd(): array;
}
