<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Microdata;

use Catalog\Model\Orm\WareHouse;
use RS\Application\Microdata\AbstractMicrodataEntity;
use RS\Application\Microdata\InterfaceMicrodataSchemaOrgJsonLd;

/**
 * Микроразметка склада
 */
class MicrodataWarehouse extends AbstractMicrodataEntity implements InterfaceMicrodataSchemaOrgJsonLd
{
    /** @var WareHouse */
    protected $warehouse;

    public function __construct(WareHouse $warehouse)
    {
        $this->warehouse = $warehouse;
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
            '@type' => 'Store',
            'name' => $this->warehouse['title'],
        ];

        if (!empty($this->warehouse['adress'])) {
            $result['address'] = $this->warehouse['adress'];
        }
        if (!empty($this->warehouse['phone'])) {
            $result['telephone'] = $this->warehouse['phone'];
        }
        if (!empty($this->warehouse['coor_x']) && !empty($this->warehouse['coor_y'])) {
            $result['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $this->warehouse['coor_x'],
                'longitude' => $this->warehouse['coor_y'],
            ];
        }
        if (!empty($this->warehouse['image'])) {
            $result['image'] = $this->warehouse['__image']->getUrl(800, 800, 'xy', true);
        }

        return $result;
    }
}
