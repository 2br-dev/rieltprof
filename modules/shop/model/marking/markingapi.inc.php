<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\Marking;

use RS\Exception as RSException;
use RS\Module\AbstractModel\BaseModel;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Marking\MarkedClasses;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\OrderItemUIT;

class MarkingApi extends BaseModel
{
    const USE_ID_GTIN = '01';
    const USE_ID_SERIAL = '21';

    protected function __construct()
    {
    }

    /**
     * Возвращает экземпляр класса
     *
     * @return MarkingApi
     */
    public static function instance()
    {
        static $instance;
        if ($instance === null) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Проверяет возможность отгрузки заказа
     *
     * @param Order $order - объект заказа
     * @return array
     * @throws RSException
     */
    public function getFullShipmentPossibilityErrors(Order $order)
    {
        $errors = [];
        foreach ($order->getCart()->getProductItems() as $item) {
            /** @var OrderItem $cart_item */
            $cart_item = $item['cartitem'];
            $product = $cart_item->getEntity();
            if ($product['marked_class']) {
                $uits = $cart_item->getUITs();
                if (count($uits) != $cart_item['amount']) {
                    $errors[] = $cart_item['title'] . ' count';
                }
            }
        }
        return $errors;
    }

    /**
     * Загружает из БД УИТ товарной позиции
     *
     * @param OrderItem $order_item - товарная позиция
     * @return OrderItemUIT[]
     */
    public function loadOrderItemUITs(OrderItem $order_item)
    {
        /** @var OrderItemUIT[] $result */
        $result = (new OrmRequest())
            ->from(new OrderItemUIT())
            ->where([
                'order_id' => $order_item['order_id'],
                'order_item_uniq' => $order_item['uniq'],
            ])
            ->objects();

        return $result;
    }

    /**
     * Записывает в БД УИТ товарной позиции
     *
     * @param OrderItem $order_item - товарная позиция
     */
    public function saveOrderItemUITs(OrderItem $order_item)
    {
        $this->deleteOrderItemUITs($order_item);

        foreach ($order_item->getUITs() as $uit) {
            if (empty($uit['id'])) {
                $uit->insert();
            } else {
                $uit->update();
            }
        }
    }

    /**
     * Удаляет из БД УИТ товарной позиции
     *
     * @param OrderItem $order_item - товарная позиция
     */
    public function deleteOrderItemUITs(OrderItem $order_item)
    {
        (new OrmRequest())
            ->delete()
            ->from(OrderItemUIT::_getTable())
            ->where([
                'order_id' => $order_item['order_id'],
                'order_item_uniq' => $order_item['uniq'],
            ])
            ->exec();
    }

    /**
     * Возвращает список возможных классов маркируемых товаров в виде списка
     *
     * @return string[]
     */
    public static function MarkedClassesSelectList()
    {
        $list = ['' => t('- Товар не подлежит маркировке -')];
        foreach (self::getMarkedClasses() as $name => $class) {
            $list[$name] = $class->getTitle();
        }
        return $list;
    }

    /**
     * Возвращает список возможных классов маркируемых товаров
     *
     * @return MarkedClasses\AbstractMarkedClass[]
     */
    public static function getMarkedClasses()
    {
        static $classes;
        if ($classes === null) {
            $classes = [];
            $classes_list = [
                new MarkedClasses\MarkedClassCommon('medicine', t('Лекарства'), '0003'),
                new MarkedClasses\MarkedClassCommon('fur', t('Меховые изделия'). '0004'),
                new MarkedClasses\MarkedClassCommon('tobacco', t('Табак'), '0005'),
                new MarkedClasses\MarkedClassCommon('alcohol', t('Алкоголь'), '0006'),
                new MarkedClasses\MarkedClassCommon('linens', t('Белье постельное, столовое, туалетное и кухонное'), '1392'),
                new MarkedClasses\MarkedClassCommon('leather', t('Предметы одежды, включая рабочую одежду, изготовленные из натуральной или композиционной кожи'), '1411'),
                new MarkedClasses\MarkedClassCommon('coat', t('Пальто, полупальто, накидки, плащи, куртки (включая лыжные), ветровки, штормовки и аналогичные изделия мужские или для мальчиков (женские или для девочек)'), '1413'),
                new MarkedClasses\MarkedClassCommon('blouse', t('Блузки, блузы и блузоны трикотажные машинного или ручного вязания, женские или для девочек'), '1414'),
                new MarkedClasses\MarkedClassCommon('shoes', t('Обувные товары'), '1520'),
                new MarkedClasses\MarkedClassCommon('perfumes', t('Духи и туалетная вода'), '2042'),
                new MarkedClasses\MarkedClassCommon('tyres', t('Шины и покрышки пневматические резиновые новые'), '2211'),
                new MarkedClasses\MarkedClassCommon('photo', t('Фотокамеры (кроме кинокамер), фотовспышки и лампы-вспышки'), '2670'),
                new MarkedClasses\MarkedClassCommon('other', t('Прочие товары'), '444d'),
            ];
            foreach ($classes_list as $item) {
                $classes[$item->getName()] = $item;
            }
        }
        return $classes;
    }

    /**
     * Возвращает содержание данных по идентификатору применения
     *
     * @param string $id - идентификатор применения
     * @return string|null
     */
    public static function handbookUseIdTitleStr(string $id): ?string
    {
        return self::handbookUseIdTitle()[$id] ?? null;
    }

    /**
     * Справочник содержаний данных
     *
     * @return string[]
     */
    public static function handbookUseIdTitle()
    {
        static $titles;
        if ($titles === null) {
            $titles = [
                self::USE_ID_GTIN => t('Глобальный номер предмета торговли (GTIN)'),
                self::USE_ID_SERIAL => t('Серийный номер'),
            ];
        }
        return $titles;
    }
}
