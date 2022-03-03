<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use Catalog\Model\Orm\Product;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Tax;
use Users\Model\Orm\User;

/**
 * API функции для работы с налогами
 */
class TaxApi extends EntityList
{
    const TAX_NDS_NONE = 'nds_none';
    const TAX_NDS_0 = 'nds_0';
    const TAX_NDS_10 = 'nds_10';
    const TAX_NDS_20 = 'nds_20';
    const TAX_NDS_110 = 'nds_110';
    const TAX_NDS_120 = 'nds_120';

    protected static $all_tax_ids;
    protected static $cache_dir_tax;

    function __construct()
    {
        parent::__construct(new Tax(), [
            'nameField' => 'title',
        ]);
    }

    /**
     * Возвращает список налогов, которые применяются к товару
     *
     * @param Product $product - загруженный товар
     * @param User $user
     * @param Address $address - адрес, который необходим для расчета налогов
     * @param int[] $tax_id_list
     * @return Tax[]
     */
    public static function getProductTaxes(Product $product, User $user, Address $address, array $tax_id_list = null)
    {
        if ($tax_id_list === null) {
            $tax_id_list = self::getProductTaxIds($product);
        }
        return self::getTaxesByIds($tax_id_list, $user, $address);
    }

    /**
     * Возвращает id налогов, которые могут быть применены к товару
     *
     * @param Product $product
     * @return array
     */
    public static function getProductTaxIds(Product $product)
    {
        $tax_ids = $product['tax_ids'];
        if ($tax_ids == 'category') {
            $dir_id = $product['maindir'];
            if (!isset(self::$cache_dir_tax[$dir_id])) {
                $main_dir = $product->getMainDir();
                self::$cache_dir_tax[$dir_id] = $main_dir['tax_ids'];
            }
            $tax_ids = self::$cache_dir_tax[$dir_id];
        }

        if ($tax_ids == 'all') {
            if (!isset(self::$all_tax_ids)) {
                self::$all_tax_ids = array_keys(self::staticSelectList());
            }
            $ids = self::$all_tax_ids;
        } else {
            $ids = explode(',', $tax_ids);
        }
        return $ids;
    }

    /**
     * Возвращает список налогов, которые применяются к доставке
     *
     * @param Delivery $delivery - доставка
     * @param User $user
     * @param Address $address - адрес, который необходим для расчета налогов
     * @param int[] $tax_id_list
     * @return Tax[]
     */
    public static function getDeliveryTaxes(Delivery $delivery, User $user, Address $address, array $tax_id_list = null)
    {
        if ($tax_id_list === null) {
            $tax_id_list = ($delivery['tax_ids']) ?: [];
        }
        return self::getTaxesByIds($tax_id_list, $user, $address);
    }

    /**
     * Возвращает список налогов до списку id
     *
     * @param array $tax_id_list - id налогов
     * @param User $user
     * @param Address $address - адрес, который необходим для расчета налогов
     * @return Tax[]
     */
    public static function getTaxesByIds(array $tax_id_list, User $user, Address $address)
    {
        static $cache_tax = [];

        if (empty($tax_id_list)) {
            return [];
        }

        $address_id = $address['country_id'] . ':' . $address['region_id'];
        $user_id = $user['id'];
        $tax_ids = implode(',', $tax_id_list);
        $cache_id = "$address_id#$user_id#$tax_ids";

        if (!isset($cache_tax[$cache_id])) {
            $cache_tax[$cache_id] = [];
            /** @var Tax[] $taxes */
            $taxes = OrmRequest::make()
                ->from(new Tax())
                ->whereIn('id', $tax_id_list)
                ->objects();

            foreach ($taxes as $tax) {
                if ($tax->canApply($user, $address)) {
                    $cache_tax[$cache_id][] = $tax;
                }
            }
        }

        return $cache_tax[$cache_id];
    }

    /**
     * Возвращает правильный идентификатор НДС
     *
     * @param Tax[] $taxes - массив налогов
     * @param Address $address - адрес используемый для расчёта
     * @return string
     */
    public static function getRightNds(array $taxes, Address $address)
    {
        $tax = new Tax();
        foreach ($taxes as $item){
            if ($item['is_nds']){
                $tax = $item;
                break;
            }
        }

        $tax_rate = $tax->getRate($address);
        if (!empty($tax_rate)) {
            switch((int) $tax_rate){
                case 10:
                    $tax_id = ($tax['included']) ? self::TAX_NDS_110 : self::TAX_NDS_10;
                    break;
                case 20:
                    $tax_id = ($tax['included']) ? self::TAX_NDS_120 : self::TAX_NDS_20;
                    break;
                case 0:
                    $tax_id = self::TAX_NDS_0;
                    break;
                default:
                    $tax_id = self::TAX_NDS_NONE;
            }
        } else {
            $tax_id = self::TAX_NDS_NONE;
        }

        return $tax_id;
    }
}
