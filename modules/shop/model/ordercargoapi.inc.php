<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;
use RS\Event\Manager as EventManager;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Orm\Cargo\OrderCargo;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItemUIT;

/**
 * API отвечает за грузовые места(коробки), связанные с заказами
 */
class OrderCargoApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Cargo\OrderCargo(), [
            'defaultOrder' => 'id'
        ]);
    }

    /**
     * Возвращает UIT'ы которые были созданы для данного заказа
     *
     * @param Order $order Заказ
     * @return OrderItemUIT
     */
    function getShippedUits($order)
    {
        /** @var OrderItemUIT $uits */
        $uits = (new OrmRequest())
            ->select('U.*')
            ->from(new OrderItemUIT(), 'U')
            ->where([
                'U.order_id' => $order['id'],
            ])
            ->objects(null, 'order_item_uniq', true);

        return $uits;
    }

    /**
     * Валидирует сведения о коробках в заказе
     *
     * @param $order_id
     * @param $cargos
     * @return bool
     */
    function validateCargos($order_id, $cargos)
    {
        $this->cleanErrors();

        $order = new Order($order_id);
        if (!$order['id']) {
            return $this->addError(t('Заказ ID:%0 не найден', $order_id));
        }

        $product_items = [];

        //Проверяем общее количесто распределяемых товаров
        $max_product_amounts = $this->getMaxProductAmounts($order, $product_items);

        $i = 0;
        foreach($cargos as $id => $cargo_data) {
            $i++;
            $cargo_title = $cargo_data['title'] ?: t('Грузоместо №%0', [$i]);

            $products_count_in_cargo = 0;
            if ($cargo_data['products']) {
                foreach ($cargo_data['products'] as $order_item_uniq => $item_data) {
                    $product_title = $product_items[$order_item_uniq]['cartitem']['title'] ?? $order_item_uniq;
                    foreach ($item_data as $uit_id => $data) {
                        $amount = $data['amount'];
                        $products_count_in_cargo += $amount;
                        if (!isset($max_product_amounts[$order_item_uniq][$uit_id])) {
                            $this->addError(t('В коробке %title присутствует товар, которого нет в заказе %uniq:%uit_id', [
                                'title' => $cargo_title,
                                'uniq' => $order_item_uniq,
                                'uit_id' => $uit_id
                            ]), $cargo_title);
                            continue;
                        }

                        $max_amount = $max_product_amounts[$order_item_uniq][$uit_id];
                        if ($amount > $max_amount) {
                            $this->addError(t('Количество товара `%product_title` в коробке `%cargo_title` больше допустимого. Максимально: %max_amount. У вас: %amount', [
                                'product_title' => $product_title,
                                'cargo_title' => $cargo_title,
                                'max_amount' => $max_amount,
                                'amount' => $amount
                            ]), $cargo_title);
                        }
                    }
                }
            }

            if (!$products_count_in_cargo) {
                $this->addError(t('В коробке %title должен размещаться хотя бы один товар', [
                    'title' => $cargo_title,
                ]), $cargo_title);
            }

            //Проверяем остальные поля упаковки
            $cargo_object = new Orm\Cargo\OrderCargo();
            $cargo_object->getFromArray($cargo_data);
            if (!$cargo_object->validate()) {
                foreach($cargo_object->getErrors() as $error) {
                    $this->addError($error, $cargo_title);
                }
            }
        }

        return !$this->hasError();
    }

    /**
     * Возвращает максимально возможное количество товаров в разрезе позиций заказа и маркировок.
     * Маркированный товар (uit > 0) должен быть по 1 единице в одной позиции
     *
     * @param Order $order Заказ
     * @param array $product_items Ссылка на массив с позициями заказа
     * @return array
     */
    protected function getMaxProductAmounts($order, &$product_items)
    {
        $max_product_amounts = [];
        //Находим максимальное количество товаров с учетом разреза маркированных товаров
        $product_items = $order->getCart()->getProductItems();
        $shipped_uits = $this->getShippedUits($order);

        foreach($product_items as $key => $item) {
            $cart_item = $item['cartitem'];
            $product = $cart_item->getEntity();
            $editable_amount = $cart_item['amount'];
            if ($product['marked_class'] && isset($shipped_uits[$key])) {
                foreach ($shipped_uits[$key] as $uit) {
                    $max_product_amounts[$key][ $uit['id'] ] = 1;
                }
                $editable_amount = $cart_item['amount'] - count($shipped_uits[$key]);
            }

            if ($editable_amount > 0) {
                $max_product_amounts[$key][ 0 ] = $editable_amount;
            }
        }

        return $max_product_amounts;
    }

    /**
     * Сохраняет сведения о коробках в заказе
     *
     * @param integer $order_id
     * @param array $cargos
     * @return bool
     */
    function saveCargos($order_id, $cargos)
    {
        if ($this->validateCargos($order_id, $cargos)) {
            $processed_ids = $this->insertOrUpdateCargos($order_id, $cargos);
            $this->removeUnprocessed($order_id, $processed_ids);

            EventManager::fire('cargo.save.after', [
                'order_id' => $order_id,
                'processed_cargo_ids' => $processed_ids
            ]);
            return true;
        }
        return false;
    }

    /**
     * Добавляет или обновляет груместа для заказа
     *
     * @param integer $order_id
     * @param array $cargos
     * @return array
     */
    protected function insertOrUpdateCargos($order_id, $cargos)
    {
        $processed_ids = [];
        foreach($cargos as $id => $cargo_data) {
            $cargo_data = ['order_id' => $order_id] + $cargo_data;
            if ($id <= 0) { //Создаем
                $cargo = new OrderCargo();
                $cargo->getFromArray($cargo_data);
                $cargo->insert();

            } else { //Обновляем
                $cargo = new OrderCargo($id);
                $cargo->getFromArray($cargo_data);
                $cargo->update();
            }
            $processed_ids[] = $cargo['id'];
        }
        return $processed_ids;
    }

    /**
     * Удаляет грузоместа, которые отсутствуют в списке $processed_ids
     *
     * @param integer $order_id
     * @param array $processed_ids
     */
    protected function removeUnprocessed($order_id, $processed_ids)
    {
        //Удаляем несуществующие грузовые места
        $q = Request::make()
            ->from(new OrderCargo())
            ->where([
                'order_id' => $order_id
            ]);
        if ($processed_ids) {
            $q->whereIn('id', $processed_ids, 'AND', true);
        }
        foreach($q->objects() as $cargo_for_delete) {
            $cargo_for_delete->delete();
        }
    }
}