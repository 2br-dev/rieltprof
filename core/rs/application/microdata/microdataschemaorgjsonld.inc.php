<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Application\Microdata;

/**
 * Тип микроразметки Schema.org в формате JSON-LD
 */
class MicrodataSchemaOrgJsonLd extends AbstractMicrodataType
{
    /**
     * Возвращает HTML микроразметки
     *
     * @param AbstractMicrodataEntity $microdata_entity
     * @return string
     */
    protected function getMicrodataHtml(AbstractMicrodataEntity $microdata_entity): string
    {
        if ($microdata_entity instanceof InterfaceMicrodataSchemaOrgJsonLd) {
            $data = $microdata_entity->getSchemaOrgJsonLd();
            $json = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            return '<script type="application/ld+json">' . $json . '</script>';
        }
        return '';
    }
}
