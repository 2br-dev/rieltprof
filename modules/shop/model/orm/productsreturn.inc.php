<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Helper\Pdf\PDFGenerator;
use RS\Helper\Tools as HelperTools;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use \RS\Orm\Type;
use Shop\Model\Cart;
use Shop\Model\CashRegisterType\AbstractType as AbstractCashRegisterType;
use Shop\Model\Exception as ShopException;
use Shop\Model\ProductsReturnApi;
use Shop\Model\TransactionApi;

/**
 * ORM объект документа на возврат на заказа
 * @package Shop\Model\Orm
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $return_num Номер возврата
 * @property integer $order_id Id заказа
 * @property integer $user_id ID пользователя
 * @property string $status Статус возврата
 * @property string $name Имя пользователя
 * @property string $surname Фамилия пользователя
 * @property string $midname Отчество пользователя
 * @property string $passport_series Серия паспорта
 * @property string $passport_number Номер паспорта
 * @property string $passport_issued_by Кем выдан паспорт
 * @property string $phone Номер телефона
 * @property string $dateof Дата оформления возврата
 * @property string $date_exec Дата выполнения возврата
 * @property array $return_items Список товаров на возврат
 * @property string $return_reason Причина возврата
 * @property string $bank_name Название банка
 * @property string $bik БИК
 * @property string $bank_account Рассчетный счет
 * @property string $correspondent_account Корреспондентский счет
 * @property float $cost_total Сумма возврата
 * @property string $currency Id валюты
 * @property float $currency_ratio Курс на момент оформления заказа
 * @property string $currency_stitle Символ курса валюты
 * @property integer $create_receipt_on_save Отправить чек при сохранении
 * --\--
 */
class ProductsReturn extends OrmObject
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETE = 'complete';
    const STATUS_REFUSE = 'refused';

    protected static $table = 'order_products_return';

    function _init() //инициализация полей класса. конструктор метаданных
    {
        return parent::_init()
            ->append([
                t('Основные данные'),
                    'site_id' => new Type\CurrentSite(),
                    'return_num' => new Type\Varchar([
                        'maxLength' => '20',
                        'description' => t('Номер возврата'),
                        'visible' => false,
                        'unique' => true
                    ]),
                    'order_id' => new Type\Integer([
                        'maxLength' => '20',
                        'description' => t('Id заказа'),
                        'index' => true,
                        'hidden' => true,
                    ]),
                    'user_id' => new Type\User([
                        'allowEmpty' => false,
                        'maxLength' => '11',
                        'attr' => [[
                            'data-autocomplete-body' => '1'
                        ]],
                        'description' => t('ID пользователя'),
                        'hidden' => true,
                    ]),
                    'status' => new Type\Enum(
                        [
                            self::STATUS_NEW,
                            self::STATUS_IN_PROGRESS,
                            self::STATUS_COMPLETE,
                            self::STATUS_REFUSE,
                        ],
                        [
                            'allowEmpty'    => false,
                            'description'   => t('Статус возврата'),
                            'listFromArray' => [[
                                self::STATUS_NEW         => t('Новый'),
                                self::STATUS_IN_PROGRESS => t('В процессе'),
                                self::STATUS_COMPLETE    => t('Завершено'),
                                self::STATUS_REFUSE      => t('Отклонено')
                            ]]
                        ]),
                    'name' => new Type\Varchar([
                        'description' => t('Имя пользователя'),
                        'checker' => ['chkEmpty', 'Имя - обязательное поле']
                    ]),
                    'surname' => new Type\Varchar([
                        'description' => t('Фамилия пользователя'),
                        'checker' => ['chkEmpty', 'фамилия - обязательное поле']
                    ]),
                    'midname' => new Type\Varchar([
                        'description' => t('Отчество пользователя'),
                        'checker' => ['chkEmpty', 'Отчество - обязательное поле']
                    ]),
                    'passport_series' => new Type\Varchar([
                        'maxLength' => '50',
                        'description' => t('Серия паспорта'),
                        'checker' => ['chkEmpty', 'Серия паспорта - обязательное поле']
                    ]),
                    'passport_number' => new Type\Varchar([
                        'maxLength' => '50',
                        'description' => t('Номер паспорта'),
                        'checker' => ['chkEmpty', 'Номер паспорта - обязательное поле']
                    ]),
                    'passport_issued_by' => new Type\Varchar([
                        'maxLength' => '100',
                        'description' => t('Кем выдан паспорт'),
                        'checker' => ['chkEmpty', 'Кем выдан паспорт - обязательное поле']
                    ]),
                    'phone' => new Type\Varchar([
                        'maxLength' => '50',
                        'description' => t('Номер телефона'),
                        'checker' => ['chkEmpty', 'Номер телефона - обязательное поле']
                    ]),
                    'dateof' => new Type\Datetime([
                        'description' => t('Дата оформления возврата'),
                        'index' => true,
                        'checker' => ['chkEmpty', 'Дата оформления возврата - обязательное поле']
                    ]),
                    'date_exec' => new Type\Datetime([
                        'description' => t('Дата выполнения возврата'),
                    ]),
                    'return_items' => new Type\ArrayList([
                        'maxLength' => '200',
                        'description' => t('Список товаров на возврат'),
                        'visible' => false,
                    ]),
                    'return_reason' => new Type\Varchar([
                        'maxLength' => '200',
                        'description' => t('Причина возврата'),
                        'checker' => ['chkEmpty', 'Причина возврата - обязательное поле']
                    ]),
                    'bank_name' => new Type\Varchar([
                        'description' => t('Название банка'),
                        'maxLength' => '100',
                    ]),
                    'bik' => new Type\Varchar([
                        'description' => t('БИК'),
                        'maxLength' => '50',
                    ]),
                    'bank_account' => new Type\Varchar([
                        'description' => t('Рассчетный счет'),
                        'maxLength' => '100',
                    ]),
                    'correspondent_account' => new Type\Varchar([
                        'description' => t('Корреспондентский счет'),
                        'maxLength' => '100',
                    ]),
                    'cost_total' => new Type\Decimal([
                        'description' => t('Сумма возврата'),
                        'visible' => false,
                    ]),
                    'currency' => new Type\Varchar([
                        'description' => t('Id валюты'),
                        'maxLength' => '20',
                        'hidden' => true,
                    ]),
                    'currency_ratio' => new Type\Decimal([
                        'description' => t('Курс на момент оформления заказа'),
                        'maxLength' => '20',
                        'hidden' => true,
                    ]),
                    'currency_stitle' => new Type\Varchar([
                        'description' => t('Символ курса валюты'),
                        'maxLength' => '20',
                        'hidden' => true,
                    ]),
                t('Товары на возврат'),
                    'chooseproducts' => new Type\UserTemplate('%shop%/form/productsreturn/returnproductselect.tpl', [
                        'return_api' => new ProductsReturnApi()
                    ]),
                    'create_receipt_on_save' => new Type\Integer([
                        'description' => t('Отправить чек при сохранении'),
                        'runtime' => true,
                    ]),
            ]);
    }

    /**
     * Действия перед записью объекта
     *
     * @param string $flag - insert или update
     * @return bool|null
     */
    function beforeWrite($flag)
    {
        $before_orm = new self($this['id']); //Предыдущая версия ORM
        $order_items = $this->getOrderData(false); //Товары заказа
        $items = $this['return_items']; //Товары на возврат

        if (empty($items)) {
            $this->addError(t('Укажите товары для возврата'));
            return false;
        }

        //если есть ошибки, не даем записать возврат
        if ($error = $this->checkItemsInOrder($order_items, $items)) {
            $this->addError($error);
            return false;
        }

        //Посчитаем цену возврата
        $total_cost_return = 0;
        foreach ($items as $uniq => $item) {
            if (isset($order_items['items'][$uniq])) {
                $total_cost_return += $order_items['items'][$uniq]['single_cost_with_discount'] * $item['amount'];
            }
            if (isset($order_items['other'][$uniq])) {
                $total_cost_return += $order_items['other'][$uniq]['total'];
            }
        }

        $this['cost_total'] = $total_cost_return;

        if (empty($this['return_num'])) {
            $this['return_num'] = HelperTools::generatePassword(6, '0123456789');
        }

        if ($this['status'] == self::STATUS_COMPLETE && ($before_orm['status'] != self::STATUS_COMPLETE)) {
            $this['date_exec'] = date('Y-m-d H:i:s');
        }

        return null;
    }

    /**
     * Действия после записи объекта
     *
     * @param string $flag - insert или update
     * @throws ShopException
     * @throws RSException
     */
    function afterWrite($flag)
    {
        //зазписываем return_item'ы в бд если возврат сформирован без ошибок
        $this->deleteReturnItems(); //Преварительно удалим, если такие товары были
        $items = $this['return_items'];
        $order = $this->getOrder();
        $order_items = $this->getOrderData(false);
        foreach ($items as $uniq => $item) {
            $product_return = new ProductsReturnOrderItem();
            $product_return->getFromArray($item);
            $product_return['return_id'] = $this['id'];
            $product_return['site_id'] = $order['site_id'];
            $product_return['uniq'] = $uniq;
            $product_return['amount'] = $item['amount'];

            if (isset($order_items['items'][$uniq])) {
                $product_return['entity_id'] = $order_items['items'][$uniq]['cartitem']['entity_id'];
                $product_return['offer'] = $order_items['items'][$uniq]['cartitem']['offer'];
                $product_return['title'] = $order_items['items'][$uniq]['cartitem']['title'];
                $product_return['model'] = $order_items['items'][$uniq]['cartitem']['model'];
                $product_return['barcode'] = $order_items['items'][$uniq]['cartitem']['barcode'];
                $product_return['cost'] = $order_items['items'][$uniq]['single_cost_with_discount'];
            }
            if (isset($order_items['other'][$uniq])) {
                $product_return['entity_id'] = $order_items['other'][$uniq]['cartitem']['entity_id'];
                $product_return['title'] = $order_items['other'][$uniq]['cartitem']['title'];
                $product_return['cost'] = $order_items['other'][$uniq]['total'];
            }

            $product_return->save();
        }

        $transaction = new Transaction();
        $transaction['dateof'] = date('Y-m-d H:i:s');
        $transaction['order_id'] = $order['id'];
        $transaction['user_id'] = $order->getUser()['id'];
        $transaction['personal_account'] = false;
        $transaction['cost'] = $this['cost_total'];
        $transaction['reason'] = t('Возврат заказа №%0', [$order['order_num']]);
        $transaction['status'] = Transaction::STATUS_SUCCESS;
        $transaction['entity'] = Transaction::ENTITY_PRODUCTS_RETURN;
        $transaction['entity_id'] = $this['id'];
        if ($transaction->insert()) {
            $transaction['sign'] = TransactionApi::getTransactionSign($transaction);
            $transaction->update();

            if ($this['create_receipt_on_save']) {
                $transaction_api = new TransactionApi();
                $transaction_api->createReceipt($transaction, AbstractCashRegisterType::OPERATION_SELL_REFUND);
            }
        }
    }

    /**
     * Заполняет поле return_items, исходя из отмеченных ранее товаров
     */
    function fillReturnItems()
    {
        if ($this['id']) {
            $data = $this->getReturnItems();
            $return_items = [];
            foreach ($data as $uniq => $item) {
                $return_items[$uniq]['uniq'] = $uniq;
                $return_items[$uniq]['amount'] = $item['amount'];
            }

            $this['return_items'] = $return_items;
        }
    }

    /**
     * Удаление возврата товара
     *
     * @return bool
     */
    function delete()
    {
        $this->deleteReturnItems(); //удаляем return_item'ы при удалении возврата
        return parent::delete();
    }

    /**
     * Возвращает ФИО того кто хочет вернуть товары
     *
     * @return string
     */
    function getFio()
    {
        return $this['surname'] . " " . $this['name'] . " " . $this['midname'];
    }

    /**
     * Заполняет поля для создания возврата
     *
     */
    function preFillFields()
    {
        $order = $this->getOrder();
        $user = $order->getUser();
        $this['user_id'] = $user['id'];
        $this['name'] = $user['name'];
        $this['surname'] = $user['surname'];
        $this['midname'] = $user['midname'];
        $this['phone'] = $user['phone'] ? $user['phone'] : $order['user_phone'];
        $this['dateof'] = date("Y-m-d H:i:s");

        $this['currency'] = $order['currency'];
        $this['currency_ratio'] = $order['currency_ratio'];
        $this['currency_stitle'] = $order['currency_stitle'];

    }

    /**
     * Проверяет наличие ошибок в заказе
     *
     * @param array $order_items - массив товаров в конкретном заказе
     * @param array $return_items - массив товаров, которые нужно вернуть для данного возврата
     * @return string|null
     */
    function checkItemsInOrder($order_items, $return_items)
    {
        //получаем все возвращенные товары заказа
        $api = new ProductsReturnApi();
        $all_returned_items_of_order = $api->getReturnItemsByOrder($this['order_id']);
        $items_returned_amount = [];
        foreach ($all_returned_items_of_order as $returned_item) {
            // не учитываем товаров редактируемого возврата
            if ($returned_item['return_id'] != $this['id']) {
                $items_returned_amount[$returned_item['uniq']] = ($items_returned_amount[$returned_item['uniq']] ?? 0) + $returned_item['amount'];
            }
        }

        foreach ($return_items as $uniq => $return_item) {
            if (isset($order_items['items'][$uniq])) {
                if ($return_item['amount'] > $order_items['items'][$uniq][Cart::CART_ITEM_KEY]['amount'] - ($items_returned_amount[$uniq] ?? 0)) {
                    return t('Количество товаров для возврата превышает разрешенное, возможно Вы уже вернули часть заказа.');
                }
            }
        }

        if ($error = $this->checkItemsInOrderDelivery($order_items, $return_items)) {
            return $error;
        }

        return null;
    }

    /**
     * Проверяет наличие ошибок, связанных с возвратом доставки
     *
     * @param array $order_items - массив товаров в конкретном заказе
     * @param array $return_items - массив товаров, которые нужно вернуть для данного возврата
     * @return string|null
     */
    function checkItemsInOrderDelivery($order_items, $return_items)
    {
        $delivery_uniq = false;
        foreach ($return_items as $uniq => $return_item) {
            if (isset($order_items['other'][$uniq]) && $order_items['other'][$uniq][Cart::CART_ITEM_KEY]['type'] == OrderItem::TYPE_DELIVERY) {
                $delivery_uniq = $uniq;
                break;
            }
        }
        if ($delivery_uniq) {
            if (!empty($items_returned_amount[$delivery_uniq])) {
                return t('Доставка уже была возвращена.');
            }
            foreach ($order_items['items'] as $uniq => $item) {
                if ($item[Cart::CART_ITEM_KEY]['amount'] > ($items_returned_amount[$uniq] ?? 0) + ($return_items[$uniq]['amount'] ?? 0)) {
                    return t('Вы можете вернуть доставку только если возвращаете все товары, входящие в заказ.');
                }
            }
        }
    }

    /**
     * Удаляет товары которые предназначены на возврат
     *
     * @param null|integer $return_id
     */
    function deleteReturnItems($return_id = null)
    {
        if (!isset($return_id)) {
            $return_id = $this['id'];
        }
        OrmRequest::make()
            ->delete()
            ->from(new ProductsReturnOrderItem())
            ->where([
                'return_id' => $return_id
            ])->exec();
    }

    /**
     * Возвращает массив товаров для возврата в рамках для данного возврата
     *
     * @return ProductsReturnOrderItem[]
     */
    function getReturnItems()
    {
        /** @var ProductsReturnOrderItem[] $result */
        $result = OrmRequest::make()
            ->from(new ProductsReturnOrderItem())
            ->where([
                'return_id' => $this['id']
            ])
            ->objects(null, 'uniq');

        return $result;
    }

    /**
     * Возвращает элементы из заказа
     *
     * @param bool $format - форматировать вывод
     * @return array
     */
    function getOrderData($format = true)
    {
        $order = new Order($this['order_id']);
        return $order->getCart()->getOrderData($format);
    }

    /**
     * Возвращает заказ которому принадлежит возврат
     *
     * @return Order
     */
    function getOrder()
    {
        return new Order($this['order_id']);
    }

    /**
     * Возвращает шаблон заявления на возврат товара в формате PDF
     *
     * @return string
     */
    function getPdfForm()
    {
        $template = ConfigLoader::byModule($this)->return_print_form_tpl;
        $pdf_generator = new PDFGenerator();
        return $pdf_generator->renderTemplate($template, [
            'return' => $this,
            'return_text_totalcost' => HelperTools::priceToString($this['cost_total']),
            'site_config' => ConfigLoader::getSiteConfig()
        ]);
    }
}
