<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Microdata;

use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use RS\Application\Microdata\AbstractMicrodataEntity;
use RS\Application\Microdata\InterfaceMicrodataSchemaOrgJsonLd;

/**
 * Микроразметка товара
 */
class MicrodataProduct extends AbstractMicrodataEntity implements InterfaceMicrodataSchemaOrgJsonLd
{
    /** @var Product */
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Возвращает данные для микроразметки Schema.org в формате JSON-LD
     *
     * @return array
     */
    function getSchemaOrgJsonLd(): array
    {
        $result = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $this->product['title'],
            'description' => $this->product['short_description'],
            'offers' => [],
        ];

        $brand = $this->product->getBrand();
        if ($brand['id']) {
            $result['brand'] = [
                '@type' => 'Brand',
                'name' => $brand['title'],
            ];
        }

        foreach ($this->product->getImages() as $image) {
            $result['image'][] = $image->getUrl(1500, 1500, 'xy', true);
        }

        if ($gtin = $this->product->getSKU()) {
            $result['gtin'] = $gtin;
        }

        foreach ($this->product->getOffers() as $offer) {
            /** @var Offer $offer */

            $offer_data = [
                '@type' => 'Offer',
                'price' => $this->product->getCost(null, $offer['id'], false),
                'priceCurrency' => $this->product->getCurrencyCode(),
                'itemCondition' => 'https://schema.org/NewCondition',
            ];

            if ($this->product->shouldReserve()) {
                $offer_data['availability'] = 'https://schema.org/PreOrder';
            } elseif ($this->product->getNum($offer['id']) > 0) {
                $offer_data['availability'] = 'https://schema.org/InStock';
            } else {
                $offer_data['availability'] = 'https://schema.org/OutOfStock';
            }

            $result['offers'] = $offer_data;
        }

        return $result;
    }
}
