<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Block;

use RS\Config\Loader as ConfigLoader;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Controller\StandartBlock;
use RS\Exception as RSException;
use RS\Orm\AbstractObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use Shop\Model\Cart as Cart;
use Shop\Model\Orm\CartItem;

/**
 * Блок-контроллер Изменение количества товара в корзине.
 * Имеет расширенную логику контроля изменения количества товара.
 * Учитывает опции
 * - Разрешить покупать товар, если его остаток меньше "минимального количества для заказа
 * - Игнорировать "шаг изменения количества" если выкупается весь остаток
 *
 */
class ProductAmountInCart extends StandartBlock
{
    protected static $controller_title = 'Изменение количества товара в корзине';
    protected static $controller_description = 'Позволяет изменять количество товара в корзине';

    protected $action_var = 'cart_amount_action';
    protected $default_params = [
        'indexTemplate' => 'blocks/productamountincart/productamountincart.tpl',
        'use_captcha' => 0
    ];

    /**
     * Получение параметров блока
     *
     * @return AbstractObject
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'cart_item' => (new Type\MixedType())
                ->setDescription(t('Товарная позиция'))
                ->setVisible(false),
            'product' => (new Type\MixedType())
                ->setDescription(t('Товар (если не указана товарная позиция)'))
                ->setVisible(false),
            'style_class' => (new Type\Varchar())
                ->setDescription(t('CSS класс')),
            'forbid_exceed_stock' => (new Type\Integer())
                ->setDescription(t('Запретить превышать максимальное количество'))
                ->setCheckboxView(1, 0)
                ->setDefault(0),
            'is_cached' => (new Type\Integer())
                ->setDescription(t('HTML блока кэшируется'))
                ->setCheckboxView(1, 0)
                ->setDefault(0)
                ->setVisible(false),
        ]);
    }

    /**
     * Возвращает список параметров, которые не изменяются при редактировании через "режим отладки"
     *
     * @return string[]
     */
    public static function getSelfNotReplaceableParams(): array
    {
        return ['cart_item', 'product'];
    }

    /**
     * Обработка основного метода
     *
     * @return ResultStandard|string
     * @throws RSException
     */
    function actionIndex()
    {
        static $products_in_cart = null;

        /** @var CartItem $cart_item */
        $cart_item = $this->getParam('cart_item');
        $product = $this->getParam('product');

        if (!$cart_item && (!$product || !$product['id'])) {
            return '';
        }

        $catalog_config = ConfigLoader::byModule('catalog');
        $shop_config = ConfigLoader::byModule('shop');
        $cart = Cart::currentCart();

        if ($products_in_cart === null) {
            $products_in_cart = [];

            foreach ($cart->getProductItems() as $item) {
                /** @var CartItem $cart_item */
                //$cart_item = $item[Cart::CART_ITEM_KEY];
                /*$products_in_cart[$cart_item['entity_id']][] = [
                    'id' => $cart_item['entity_id'],
                    'offer' => $cart_item['offer'],
                    'multioffers' => $cart_item['multioffers'],
                    'additional_uniq' => $cart_item->getExtraParam(Cart::ITEM_EXTRA_KEY_ADDITIONAL_UNIQUE),
                    'amount' => $cart_item['amount'],
                ];*/
            }
        }

        if ($product) {
            $amount = 0;
            if (!empty($_SESSION[Cart::SESSION_CART_PRODUCTS][$product['id']])) {
                foreach ($_SESSION[Cart::SESSION_CART_PRODUCTS][$product['id']] as $offer_amount) {
                    $amount += $offer_amount;
                }
            }
            $forbid_change_amount = false;
            $unit_title = $product->getUnit()['stitle'];
            $input_name = '';
            $offer_id = $product->getMainOffer()['id'];
        } else {
            $product = $cart_item->getEntity();
            $amount = $cart_item['amount'];
            $forbid_change_amount = $cart_item->getExtraParam(CartItem::EXTRA_FLAG_FORBID_CHANGE_AMOUNT);
            $input_name = "products[{$cart_item['uniq']}][amount]";
            $offer_id = $cart_item['offer'];
            if ($catalog_config['use_offer_unit']) {
                $product->fillOffers();
                if (isset($product->getOffers()[$cart_item['offer']])) {
                    $offer = $product->getOffers()[$cart_item['offer']];
                    $unit_title = $offer->getUnit()['stitle'];
                } else {
                    $unit_title = $product->getUnit()['stitle'];
                }
            } else {
                $unit_title = $product->getUnit()['stitle'];
            }
        }

        $amount_step = $product->getAmountStep();
        $product_stock = $product->getNum($offer_id);
        $amount_add_to_cart = max($product->getMinOrderQuantity(), $amount_step);
        $is_cart_page = in_array(RouterManager::getCurrentRoute()->getId(), ['shop-front-cartpage', 'shop-block-cart', 'shop-front-checkout']);
        $cart_amount_options = [
            'productId' => $product['id'],
            'amountStep' => $amount_step,
            'minAmount' => $product->getMinOrderQuantity(),
            'forbidRemoveProducts' => $is_cart_page,
            'forbidChangeRequests' => $is_cart_page,
            'isCached' => $this->getParam('is_cached'),
        ];
        if ($shop_config['allow_buy_num_less_min_order'] && $product_stock < $product->getMinOrderQuantity()) {
            $break_point = ($shop_config['allow_buy_all_stock_ignoring_amount_step']) ? $product_stock : floor($product_stock / $amount_step) * $amount_step;
            $cart_amount_options['amountBreakPoint'] = $break_point;
            $amount_add_to_cart = $break_point;
        } elseif ($shop_config['allow_buy_all_stock_ignoring_amount_step'] && $product_stock > $product->getMinOrderQuantity()) {
            $cart_amount_options['amountBreakPoint'] = $product_stock;
            if ($product_stock < $amount_step) {
                $amount_add_to_cart = $product_stock;
            }
        }
        $cart_amount_options['amountAddToCart'] = $amount_add_to_cart;
        if ($this->getParam('forbid_exceed_stock')) {
            $cart_amount_options['maxAmount'] = $product->getNum();
        }

        $this->view->assign([
            'product' => $product,
            //'data' => json_encode($products_in_cart[$product['id']]),
            'style_class' => $this->getParam('style_class'),
            'amount' => $amount,
            'forbid_change_amount' => $forbid_change_amount,
            'unit_title' => $unit_title,
            'input_name' => $input_name,
            'cart_amount_options' => json_encode($cart_amount_options),
            'min_amount' => $product->getMinOrderQuantity(),
            'amount_add_to_cart' => $amount_add_to_cart,
            'is_cached' => $this->getParam('is_cached'),
        ]);

        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }

    public function actionChangeAmount()
    {
        $url = $this->url;
        $id = $url->request('id', TYPE_INTEGER);
        $amount = $url->request('amount', TYPE_FLOAT);

        $cart = Cart::currentCart();
        $products = $cart->getProductItems();
        $new_items = [];

        foreach ($products as $uniq => $item) {
            if ($item['cartitem']['entity_id'] == $id) {
                if ($amount == 0) {
                    $cart->removeItem($uniq);
                } else {
                    $products[$uniq]['cartitem']['amount'] = $amount;
                    $new_items[$uniq] = $products[$uniq]['cartitem'];
                }
            }
        }
        if ($amount != 0) {
            $cart->update($new_items);
        }

        return $this->result->setSuccess(true);
    }
}
