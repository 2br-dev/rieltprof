<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\HtmlFilterType;

use RS\Exception as RSException;
use RS\Html\Filter\Type as FilterType;
use RS\Http\Request as HttpRequest;
use Shop\Model\Orm\Transaction;

/**
 * Класс поиска по товару в заказе
 */
class TransactionEntity extends FilterType\AbstractType
{
    public $tpl = '%shop%/form/filtertype/transaction_entity.tpl';

    /**
     * Возвращает секцию для where
     *
     * @return string
     */
    public function getWhere()
    {
        $where = '';
        $value = $this->getValue();
        if (!empty($value['type']) && in_array($value['type'], array_keys($this->handbookEntityType()))) {
            switch ($value['type']) {
                case 'order':
                    if (empty($value['id'])) {
                        $where = "order_id > 0";
                    } else {
                        $where = "order_id = {$value['id']}";
                    }
                    break;
                case 'order_pay':
                    $where = 'entity is NULL';
                    if (empty($value['id'])) {
                        $where .= " and order_id > 0";
                    } else {
                        $where .= " and order_id = {$value['id']}";
                    }
                    break;
                case 'personal_account':
                    $where = 'personal_account = 1';
                    if (!empty($value['id'])) {
                        $where .= " and user_id = {$value['id']}";
                    }
                    break;
                default:
                    $where = "entity = \"{$value['type']}\"";
                    if (!empty($value['id'])) {
                        $where .= " and entity_id = {$value['id']}";
                    }
                    break;
            }
        }
        return $where;
    }

    /**
     * Возвращает массив с данными, об установленых фильтрах для визуального отображения частиц
     *
     * @param array $current_filter_values - значения установленных фильтров
     * @param array $exclude_keys массив ключей, которые необходимо исключить из ссылки на сброс параметра
     * @return array of array ['title' => string, 'value' => string, 'href_clean']
     * @throws RSException
     */
    public function getParts($current_filter_values, $exclude_keys = [])
    {
        $value = $this->getValue();
        if (!empty($value['type'])) {
            $without_this = $current_filter_values;
            unset($without_this[$this->getKey()]);

            $part = [];
            if (empty($value['id']) || $value['type'] == 'personal_account') {
                $part['title'] = t('Тип операции');
                $part['value'] = $this->handbookEntityType()[$value['type']];
            } else {
                $part['title'] = $this->handbookEntityType()[$value['type']];
                $part['value'] = 'id - ' . $value['id'];
            }
            $part['href_clean'] = HttpRequest::commonInstance()->replaceKey([$this->wrap_var => $without_this]);
            return [$part];
        }
        return [];
    }

    /**
     * Справочник типов операций
     *
     * @return string[]
     */
    public function handbookEntityType()
    {
        static $types;
        if ($types === null) {
            $types = [
                '' => t('- Любой -'),
                'order' => t('Все по заказу'),
                'order_pay' => t('Оплата заказа'),
                Transaction::ENTITY_SHIPMENT => t('Отгрузка заказа'),
                Transaction::ENTITY_PRODUCTS_RETURN => t('Возврат заказа'),
                'personal_account' => t('Лицевой счёт'),
            ];
        }
        return $types;
    }
}
