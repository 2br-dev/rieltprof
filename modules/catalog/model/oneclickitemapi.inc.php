<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use Main\Model\NoticeSystem\HasMeterInterface;
use RS\Event\Manager as EventManager;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Cart;
use Users\Model\Orm\User;


/**
 * Класс содержит API функции для работы с объектом купить в 1 клик
 */
class OneClickItemApi extends \RS\Module\AbstractModel\EntityList
                        implements HasMeterInterface
{
    const
        METER_ONECLICK = 'rs-admin-menu-oneclick';

    function __construct()
    {
        parent::__construct(new \Catalog\Model\Orm\OneClickItem(), [
            'multisite' => true
        ]);
    }


    /**
     * Возвращает API по работе со счетчиками
     *
     * @return \Main\Model\NoticeSystem\MeterApiInterface
     */
    function getMeterApi($user_id = null)
    {
        return new \Main\Model\NoticeSystem\MeterApi($this->obj_instance,
            self::METER_ONECLICK,
            $this->getSiteContext(),
            $user_id);
    }
    
    /**
    * Подготавливает сериализованный массив из товаров
    * 
    * @param array $products - массив товаров и выбранными комплектациями
    * @return string
    */
    function prepareSerializeTextFromProducts($products)
    {
        $arr = [];
        foreach ($products as $product){
            $arr[] = [
                'id' => $product['id'],
                'title' => $product['title'],
                'barcode' => $product['barcode'],
                'offer_fields' => $product['offer_fields']
            ];
        }
        return serialize($arr);
    }

    /**
     * Ищет покупку в 1 клик по различным полям
     *
     * @param string $term поисковая строка
     * @param array $fields массив с полями, в которых необходимо произвести поиск
     * @param integer $limit максимальное количество результирующих строк
     * @return array
     */
    function search($term, $fields, $limit)
    {
        $this->resetQueryObject();
        $q = $this->queryObj();
        $q->select = 'A.*';

        $q->openWGroup();
        if (in_array('user', $fields)) {
            $q->leftjoin(new User(), 'U.id = A.user_id', 'U');
            $q->where("CONCAT(`U`.`surname`, ' ', `U`.`name`,' ', `U`.`midname`) like '%#term%'", [
                'term' => $term
            ]);
        }

        foreach($fields as $field) {
            if ($field == 'user') continue;
            $this->setFilter($field, $term, '%like%', 'OR');
        }

        $q->closeWGroup();

        return $this->getList(1, $limit);
    }

    /**
     * Подготавливает сведения о многомерных комплектациях для отображения
     *
     * @param array $offer_fields - массив доп. сведения
     * @param Orm\Product $product - объект товар
     * @param array $multioffers - массив с сведениями для установки
     * @return array
     */
    function preparedMultiOffers($offer_fields, $product, $multioffers)
    {
        //Многомерные комплектации
        if (!empty($multioffers)) {
            if ($product->isMultiOffersUse() || $product->isVirtualMultiOffersUse()) {
                $product_multioffers = $product->fillMultiOffers();
            }

            //Переберём комплектации и запишем значения в виде строки, на случай если удалён товар
            $multioffers_val = [];
            foreach ($multioffers as $prop_id => $value) {
                //Проверим, если данные в старом формате, обрежем как надо
                if (mb_stripos($value, ":") !== false) {
                    strtok($value, ":");
                    $multioffers[$prop_id] = trim(strtok(":"));
                }

                //Запишем значения текстом
                if (isset($product_multioffers['levels'][$prop_id])) {
                    $property_title = ($product_multioffers['levels'][$prop_id]['title']) ? $product_multioffers['levels'][$prop_id]['title'] : $product_multioffers['levels'][$prop_id]['prop_title'];
                    $multioffers_val[$prop_id] = $property_title . ": " . $multioffers[$prop_id];
                }
            }
            $offer_fields['multioffer'] = $multioffers;
            $offer_fields['multioffer_val'] = $multioffers_val;
        }
        return $offer_fields;
    }

    /**
     * Подготавливает сведения по одному товару
     *
     * @param Product $product - объект товара
     * @param integer|null $offer_id - id комплектации если есть
     * @param array $multioffers - массив многомерных комплектаций
     * @return array
     */
    function prepareProductOfferFields($product, $offer_id = null, $multioffers = [])
    {
        $offer_fields = [
            'offer' => null,
            'offer_id' => null,
            'multioffer' => [],
            'multioffer_val' => [],
            'amount' => 1 //Количество
        ];

        //Многомерные комплектации
        $offer_fields = $this->preparedMultiOffers($offer_fields, $product, $multioffers);
        if ($offer_id) {
            $offer = new Offer($offer_id);
            $offer_fields['offer_id'] = $offer_id;
            $offer_fields['offer'] = $offer['title'];
            $offer_fields['barcode'] = $offer['barcode'];
        }
        return $offer_fields;
    }


    /**
     * Возвращает подготовленные данные (товары) из корзины пользователя для отправки
     * @return array
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function getPreparedProductsFromCart()
    {
        $products = [];
        $cart = Cart::currentCart();
        $product_items = $cart->getProductItemsWithConcomitants(); //Получим товары из корзины

        foreach ($product_items as $item) {
            $product = $item['product'];  //Товар
            $cartitem = $item['cartitem']; //Объект в корзине

            //Предварительные данные
            $offer_fields = [
                'offer_id' => $cartitem['offer'],
                'multioffer' => [],
                'multioffer_val' => [],
                'amount' => $cartitem['amount'] //количество
            ];
            //Если есть комплектация
            if ($cartitem['offer'] !== null) {
                $offer = OrmRequest::make()
                    ->from(new Offer())
                    ->where([
                        'product_id' => $product['id'],
                        'id' => $cartitem['offer'],
                    ])->object();

                $offer_fields['offer'] = $offer['title'];
                $offer_fields['offer_id'] = $offer['id'];
                $offer_fields['barcode'] = $offer['barcode'];
            }

            //Соберём многомерные комплектации, если они есть
            $multioffers = @unserialize($cartitem['multioffers']);
            if (!empty($multioffers)) {
                foreach ($multioffers as $prop_id => $multioffer) {
                    $offer_fields['multioffer'][$prop_id] = $multioffer['value']; //
                    $offer_fields['multioffer_val'][$prop_id] = $multioffer['title'] . ": " . $multioffer['value']; //Текстовое представление
                }
            }

            $product['offer_fields'] = $offer_fields;

            $event_result = EventManager::fire('oneclick.addproduct.before', [
                'product' => $product,
                'cartitem' => $cartitem,
            ]);
            if ($event_result->getEvent()->isStopped()) {
                continue;
            }

            $products[] = $product;
        }

        return $products;
    }
      
}