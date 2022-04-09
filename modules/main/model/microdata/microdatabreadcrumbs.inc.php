<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Model\Microdata;

use RS\Application\Microdata\AbstractMicrodataEntity;
use RS\Application\Microdata\InterfaceMicrodataSchemaOrgJsonLd;
use RS\Site\Manager as SiteManager;

/**
 * Микроразметка хлебных крошек
 */
class MicrodataBreadcrumbs extends AbstractMicrodataEntity implements InterfaceMicrodataSchemaOrgJsonLd
{
    protected $breadcrumbs_data;

    public function __construct(array $breadcrumbs_data)
    {
        $this->breadcrumbs_data = $breadcrumbs_data;
    }

    /**
     * Возвращает данные для микроразметки Schema.org в формате JSON-LD
     *
     * @return array
     */
    function getSchemaOrgJsonLd(): array
    {
        $result = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        $i = 1;
        foreach ($this->breadcrumbs_data as $item) {
            $result['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $i++,
                'name' => $item['title'],
                'item' => trim(SiteManager::getSite()->getRootUrl(true), '/') . $item['href'],
            ];
        }

        return $result;
    }
}
