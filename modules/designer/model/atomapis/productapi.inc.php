<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\AtomApis;

use Catalog\Model\Api;
use Catalog\Model\DirApi;
use RS\Config\Loader as ConfigLoader;
use RS\Module\AbstractModel\BaseModel;

/**
 * Класс API для компонента товар
 */
class ProductApi extends BaseModel
{
    /**
     * Возвращает фото адрес картинки
     *
     * @param array $info - массив данных для возврата
     * @param \Catalog\Model\Orm\Product|\Photo\Model\Orm\Image $photo - объект фото или товара
     * @param array $photo_params - массив с данными для фото
     */
    function getProtoUrl(&$info, $photo, $photo_params)
    {
        if ($photo instanceof \Catalog\Model\Orm\Product){
            $photo = $photo->getMainImage();
        }
        $info['photos']['big'][]    = $photo->getUrl($photo_params['big_width'], $photo_params['big_height'], $photo_params['type']);
        $info['photos']['medium'][] = $photo->getUrl($photo_params['width'], $photo_params['height'], $photo_params['type']);
        $info['photos']['thumbs'][] = $photo->getUrl($photo_params['thumb_width'], $photo_params['thumb_height'], $photo_params['type']);
    }

    /**
     * Дополняет секцию со сведениями о товаре наличием кнопок
     *
     * @param array $info - информация о товаре
     * @param \Catalog\Model\Orm\Product $product - товар
     * @param integer $offer_sortn - номер комплектации
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    function getProductButtons(&$info, \Catalog\Model\Orm\Product $product, $offer_sortn = 0)
    {
        $shop_config     = \RS\Config\Loader::byModule('shop');
        $catalog_config  = \RS\Config\Loader::byModule('catalog');
        $info['buttons'] = [];

        if ($product->isOffersUse()){
            $offer = $product['offers']['items'][(int)$offer_sortn];
            $info['has_offers'] = true;
        }else{
            $offer = new \Catalog\Model\Orm\Offer();
        }

        $router = \RS\Router\Manager::obj();
        if ($product->isAvailable()){
            if ($shop_config && !$product['disallow_manually_add_to_cart']){
                if ($product['reservation'] != 'forced' && (!$shop_config['check_quantity'] || $product->getNum($offer_sortn) > 0)){ //Если не только кнопка заказать
                    $info['buttons']['buy'] = $router->getUrl('shop-front-cartpage', ["add" => $product['id'], "offer" => $offer_sortn]);
                }elseif ($product->shouldReserve()){
                    $info['buttons']['reservation'] = $router->getUrl('shop-front-reservation', ["product_id" => $product['id'], 'offer_id' => $offer['id']]);
                }
            }

            if ((!$shop_config || (!$product->shouldReserve() && (!$shop_config['check_quantity'] || $product->getNum($offer_sortn) > 0))) && $catalog_config['buyinoneclick']){
                $info['buttons']['oneclick'] = $router->getUrl('catalog-front-oneclick', ["product_id" => $product['id'], 'offer_id' => $offer['id']]);
            }
        }else{
            if ($shop_config && !$product['disallow_manually_add_to_cart'] && $product->shouldReserve()){
                $info['buttons']['reservation'] = $router->getUrl('shop-front-reservation', ["product_id" => $product['id']]);
            }
        }
    }

    /**
     * Дополняет секцию со сведениями о товаре наличием кнопок
     *
     * @param array $info - информация о товаре
     * @param \Catalog\Model\Orm\Product $product - товар
     * @param array $photo_params - массив сведениями о фото товара
     * @param integer $offer_sortn - номер комплектации
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    function getProductPhotos(&$info, \Catalog\Model\Orm\Product $product, $photo_params, $offer_sortn)
    {
        $photos = $product->getImages();
        $need_all_photos = true; //Нужны все фото?
        if ($product->isOffersUse() && $product['offers']['items'][$offer_sortn]) { //Если есть комплектации
            /**
             * @var \Catalog\Model\Orm\Offer $offer
             */
            $offer         = $product['offers']['items'][$offer_sortn];
            $offer_photos  = $offer['photos_arr'];
            if (!empty($offer_photos)){
                $need_all_photos = false;
                foreach ($photos as $photo_id => $photo){
                    if (in_array($photo_id, $offer_photos)){ //Отображаем все фото комплектации
                        $this->getProtoUrl($info, $photo, $photo_params);
                    }
                }
            }
        }
        if ($need_all_photos){ //Отображаем все фото товара
            foreach ($photos as $photo_id=>$photo){
                $this->getProtoUrl($info, $photo, $photo_params);
            }
        }
        if (empty($photos)){ //Если фото вообще не добавлено
            $this->getProtoUrl($info, $product, $photo_params);
        }
    }

    /**
     * Возвращает массив данных по товару и комплектации
     *
     * @param integer $product_id - id товара
     * @param integer $offer_sortn - комплектация
     * @param array $photo_params - параметры фото
     *
     * @return array
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    function getProductInfo($product_id, $offer_sortn, $photo_params)
    {
        $product = new \Catalog\Model\Orm\Product($product_id);

        $info = \ExternalApi\Model\Utils::extractOrm($product);
        $info['has_offers'] = false;
        if ($product->isOffersUse() && $product['offers']['items'][$offer_sortn]) { //Если есть комплектации
            /**
             * @var \Catalog\Model\Orm\Offer $offer
             */
            $offer         = $product['offers']['items'][$offer_sortn];
            $info['offer'] = \ExternalApi\Model\Utils::extractOrm($offer);
            $info['has_offers'] = true;
        }

        if ($product['brand_id']){ //Если есть бренд
            $info['brand'] = $product->getBrand()->title;
        }

        $info['barcode'] = $product->getBarCode($offer_sortn);
        $info['num']     = $product->getNum($offer_sortn);
        $info['amount']['min']  = $product->getAmountStep();
        $info['amount']['step'] = $product->getAmountStep();

        $info['cost']     = $product->getCost(null, $offer_sortn);
        if ($product->getOldCost($offer_sortn, false) > 0){
            $info['old_cost'] = $product->getOldCost($offer_sortn);
        }
        $info['currency'] = $product->getCurrency();

        //Получим сведения по доступным кнопкам
        $this->getProductButtons($info, $product, $offer_sortn);
        $this->getProductPhotos($info, $product, $photo_params, $offer_sortn);

        return $info;
    }
}