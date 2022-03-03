<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\PrintForm;

use Shop\Model\Orm\Address;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Region;

/**
 * Обычная печатная форма заказа
 */
abstract class AbstractTorgForm extends AbstractPrintForm
{

    protected
        $amount_on_first_page = 5,
        $amount_on_middle = 10,
        $amount_on_last_page = 3;

    /**
     * Возвращает HTML готовой печатной формы
     *
     * @return string
     */
    function getHtml()
    {
        $cart = $this->order->getCart();
        $subtotal = 0;
        $total_taxes = 0;
        $total_amount = 0;
        $result = $cart->getPriceItemsData();
        //Расчитываем налоги для каждого товара
        foreach($cart->getProductItems() as $key => $product) {
            $product_subtotal = $product['cartitem']['price'] - $product['cartitem']['discount']; //стоимость товара без налогов

            $address = $this->order->getAddress();
            $delivery = $this->order->getDelivery();

            if (!isset($address->city)){  //если доставка самовывоз, и свой адрес пользователь не вводил
                if ($delivery->getTypeObject()->isMyselfDelivery()) {  // для расчета ставки налога использовать регион(Город) из доставки
                    $city_id = $delivery->getTypeObject()->getOption('myself_addr');
                    $city_region = new Region($city_id);
                    if ($city_region['id']) {
                        $address = Address::createFromRegion($city_region);
                    }
                }
            }

            $taxes = \Shop\Model\TaxApi::getProductTaxes($product['product'], $this->order->getUser(), $address);
            // Событие для модификации списка применямых налогов
            $event_result = \RS\Event\Manager::fire('cart.getcartdata.producttaxlist', [
                'taxes' => $taxes,
                'product' => $product,
                'cart' => $cart,
            ]);
            list($taxes) = $event_result->extract();
            $total_amount += $result['items'][$key]['amount'];
            $all_taxes = 0;
            $nds_found = false;
            foreach($taxes as $tax) {
                $tax_rate = $tax->getRate($address);
                $tax_part = ($tax['included']) ? ($tax_rate / (100 + $tax_rate)) : ($tax_rate / 100) ;
                $tax_value = round($product_subtotal * $tax_part, 2);
                if(!$nds_found && $tax->is_nds){
                    if ($tax['included']) {
                        $product_subtotal -= $tax_value;
                    }
                    $result['taxes'][$tax['id']]['cost'] += $tax_value;
                    if (!$tax['included']) {
                        $result['total'] += $tax_value;
                    }
                    $nds_found = true;
                    $result['items'][$key]['taxes']['rate'] = $tax_rate;
                    $result['items'][$key]['taxes']['value'] = $tax_value;
                    $result['items'][$key]['subtotal'] = (float)$product_subtotal;
                    $result['items'][$key]['taxes']['cost'] = $product_subtotal + $tax_value;
                    $total_taxes += $tax_value;
                    break;
                }
                $all_taxes += $tax_value;
            }
            if(!$nds_found){
                $result['items'][$key]['taxes']['rate'] = '0';
                $result['items'][$key]['taxes']['value'] = '0';
                $result['items'][$key]['subtotal'] = (float)$product_subtotal;
                $result['items'][$key]['taxes']['cost'] = $product_subtotal + $result['items'][$key]['taxes']['value'];
            }
            $result['items'][$key]['taxes']['all'] = $all_taxes;

            $subtotal += $product_subtotal;
        }

        $result['delivery'] = $this->getDeliveryData($this->order);

        $products_array = $this->getProductsArray($this->order);

        $cart = $this->order->getCart();
        $products = $cart->getProductItems();

        $view = new \RS\View\Engine();
        $view->assign([
            'products' => $products,
            'products_array' => $products_array,
            'order' => $this->order,
            'subtotal' => $subtotal,
            'all_taxes' => $total_taxes,
            'taxes' => $result,
            'total_amount' => $total_amount,
        ]);
        $view->assign(\RS\Module\Item::getResourceFolders($this));
        return $view->fetch($this->getTemplate());
    }

    /**
     *  Подготавливает товары для документа
     *
     * @param Order $order
     * @return array
     */
    function getProductsArray($order)
    {
        $products_array = [];
        $cart = $order->getCart();
        $products = $cart->getOrderData(false);
        $products = $products['items'];

        if (count($products) <= $this->amount_on_first_page){
            $products_array[] = $products;
        } else {
            $products_array[] = array_slice($products, 0, $this->amount_on_first_page);
            $products_array = array_merge($products_array, array_chunk(array_slice($products, $this->amount_on_first_page), $this->amount_on_middle, true));
            $last_arr = count($products_array) - 1;
            if (count($products_array[$last_arr]) > $this->amount_on_last_page){
                $last_element = $products_array[$last_arr];
                unset($products_array[$last_arr]);
                $last_page = array_slice($last_element, 0, $this->amount_on_last_page);
                $page_before_last = array_slice($last_element, $this->amount_on_last_page);
                $products_array[] = $page_before_last;
                $products_array[] = $last_page;
            }
        }
        return $products_array;
    }

    /**
     *  Возвращает стоимость доставки и налог
     *
     * @param $order
     * @return array | bool
     */
    function getDeliveryData(\Shop\Model\Orm\Order $order)
    {
        $address = $order->getAddress();
        $delivery = $order->getDelivery();
        $delivery_taxes = \Shop\Model\TaxApi::getDeliveryTaxes($delivery, $order->getUser(), $address);
        $delivery_cost = $order->getDeliveryCost();

        $data['title'] = "Доставка: ".$delivery->getTypeObject()->getTitle();
        foreach($delivery_taxes as $tax) {
            $tax_rate = $tax->getRate($address);
            $tax_part = ($tax['included']) ? ($tax_rate / (100 + $tax_rate)) : ($tax_rate / 100) ;
            $tax_value = round($delivery_cost * $tax_part, 2);
            if($tax->is_nds){
                if ($tax['included']) {
                    $data['subtotal'] = $delivery_cost - $tax_value;
                    $data['cost'] = $delivery_cost;
                }else{
                    $data['subtotal'] = $delivery_cost;
                    $data['cost'] = $delivery_cost + $tax_value;
                }
                $data['tax'] = $tax_value;
                $data['tax_rate'] = $tax_rate;
                return $data;
            }
        }
        if($delivery){
            $data['cost'] = $delivery_cost;
            return $data;
        }
        return false;
    }
}
