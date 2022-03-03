<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Config;

use RS\Module\AbstractPatches;
use RS\Orm\Request;
use RS\Site\Manager as SiteManager;
use Shop\Model\DeliveryApi;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Region;
use Shop\Model\Orm\UserStatus;

/**
 * Патчи к модулю
 */
class Patches extends AbstractPatches
{
    /**
     * Возвращает массив имен патчей.
     */
    function init()
    {
        return [
            '20073',
            '20063',
            '20087',
            '200152',
            '200198',
            '200197',
            '3021',
            '4067',
            '40106',
            '40117',
            '40121',
            '40100',
            '40144',
            '40170',
            '40177',
        ];
    }

    public function afterUpdate40177()
    {
        $statuses = (new Request())
            ->from(new UserStatus())
            ->orderby('sortn asc')
            ->objects();

        foreach ($statuses as $status) {
            $status['sortn'] = $status['id'];
            $status->update();
        }

        foreach (SiteManager::getSiteList() as $site) {
            SiteManager::setCurrentSite($site);

            $delivery_api = new DeliveryApi();
            foreach ($delivery_api->getList() as $delivery) {
                /** @var $delivery Delivery */
                $delivery_type = $delivery->getTypeObject();
                if ($delivery_type->getShortName() == 'cdek') {
                    if (!$delivery_type->getOption('city_from')) {
                        if ($delivery_type->getOption('city_from_name')) {
                            $region = Region::loadByWhere([
                                'title' => $delivery_type->getOption('city_from_name'),
                                'is_city' => 1,
                            ]);
                        } elseif ($delivery_type->getOption('city_from_zipcode')) {
                            $region = Region::loadByWhere([
                                'zipcode' => $delivery_type->getOption('city_from_zipcode'),
                                'is_city' => 1,
                            ]);
                        } else {
                            $region = false;
                        }

                        if ($region) {
                            $delivery_type->setOption('city_from', $region['id']);
                            $delivery->update();
                        }
                    }
                }
            }
        }
    }

    public function afterUpdate40170()
    {
        foreach (SiteManager::getSiteList() as $site) {
            SiteManager::setCurrentSite($site);

            $status_payment_method_selected = UserStatus::loadByWhere([
                'site_id' => SiteManager::getSiteId(),
                'type' => 'payment_method_selected',
            ]);
            if (!$status_payment_method_selected['id']) {
                $status_payment_method_selected['title'] = t('Выбран метод оплаты');
                $status_payment_method_selected['bgcolor'] = '#4d76ad';
                $status_payment_method_selected['type'] = 'payment_method_selected';
                $status_payment_method_selected['is_system'] = 1;
                $status_payment_method_selected->insert();
            }
        }
    }

    /**
     * Обновляем значения в БД, так как null теперь означает,
     * что условие не задано, а не 0, как было раньше
     */
    function afterUpdate40144()
    {
        $null_fields = ['min_price', 'max_price', 'min_weight', 'max_weight'];

        foreach($null_fields as $field) {
            Request::make()
                ->update(Delivery::_getTable())
                ->set([$field => null])
                ->where([$field => 0])
                ->exec();
        }
    }

    /**
     * Обновляет КЛАДР id городов
     */
    function afterUpdate40121()
    {
        $filename = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/shop' . \Setup::$CONFIG_FOLDER . '/demo/regions.csv';

        $file = fopen($filename,'r');
        if ($file) {
            $site_api = new \Site\Model\Api();
            $site_list = $site_api->getList();

            $region_list = (new \RS\Orm\Request())
                ->select('id, site_id, title')
                ->from(\Shop\Model\Orm\Region::_getTable())
                ->where(['is_city' => 0])
                ->exec()->fetchAll();

            $regions = [];
            foreach ($region_list as $item) {
                $regions[$item['site_id']][$item['title']] = $item['id'];
            }

            $q = \RS\Orm\Request::make()
                ->update(new \Shop\Model\Orm\Region());

            fgetcsv($file, null, ';', '"');
            while ($row = fgetcsv($file, null, ';', '"')) {
                if (!empty($row[4])) {
                    $parent_parts = explode('/', $row[1]);
                    $region_title = end($parent_parts);

                    foreach ($site_list as $site) {
                        if (isset($regions[$site['id']][$region_title])) {
                            $region_id = $regions[$site['id']][$region_title];

                            $q->set = '';
                            $q->where = '';

                            $q->set(['kladr_id' => $row[4]])
                                ->where([
                                    'site_id' => $site['id'],
                                    'is_city' => 1,
                                    'title' => $row[0],
                                    'parent_id' => $region_id,
                                ])
                                ->exec();
                        }
                    }
                }
            }
        }
    }

    /**
     * Изменеия в связи с появлением голобалного "выбранного адреса"
     */
    function beforeUpdate40120()
    {
        $shop_config = \RS\Config\Loader::byModule('shop');
        $shop_config['use_selected_address_in_checkout'] = $shop_config['use_geolocation_address'];
        $shop_config->update();
    }

    /**
     * Подменяет в позициях заказов sortn комплектаций на id
     */
    function afterUpdate40100()
    {
        $offset = 0;
        $limit = 1000;
        $file = fopen(\Setup::$PATH . \Setup::$LOGS_DIR . '/old_orderitems_sortn.csv', 'w');
        fputcsv($file, ['order_id', 'uniq', 'sortn'], ';', '"');

        while (true) {
            $product_ids = (new \RS\Orm\Request())
                ->select('id')
                ->from(\Catalog\Model\Orm\Product::_getTable())
                ->orderby('id asc')
                ->limit($offset, $limit)
                ->exec()->fetchSelected(null, 'id');

            if (empty($product_ids)) {
                break;
            }

            $offset += $limit;

            $offers = (new \RS\Orm\Request())
                ->select('product_id, id')
                ->from(\Catalog\Model\Orm\Offer::_getTable())
                ->whereIn('product_id', $product_ids)
                ->orderby('sortn asc')
                ->exec()->fetchSelected('product_id', 'id', true);

            $order_items = (new \RS\Orm\Request())
                ->select('order_id, uniq, entity_id, offer')
                ->from(\Shop\Model\Orm\OrderItem::_getTable())
                ->whereIn('entity_id', $product_ids)
                ->where([
                    'type' => 'product',
                ])
                ->exec()->fetchAll();

            $update = [];
            foreach ($order_items as $item) {
                fputcsv($file, [$item['order_id'], $item['uniq'], $item['offer']], ';', '"');
                $offer_id = $offers[$item['entity_id']][$item['offer']] ?? $offers[$item['entity_id']][0] ?? 0;
                $update[] = [$item['order_id'], $item['uniq'], $offer_id];
            }

            if (!empty($update)) {
                $values = [];
                foreach ($update as $item) {
                    $values[] = '("' . implode('","', $item) . '")';
                }

                $sql = 'INSERT INTO ' . \Shop\Model\Orm\OrderItem::_getTable() . ' (order_id,uniq,offer) VALUES ' . implode(',', $values) . ' ON DUPLICATE KEY UPDATE offer=VALUES(offer);';
                \RS\Db\Adapter::sqlExec($sql);
            }
        }
        fclose($file);
    }

    /**
     * Переносит значение региона по умочанию в новое поле
     */
    function beforeUpdate40106()
    {
        $shop_config = \RS\Config\Loader::byModule('shop');
        if (!empty($shop_config['default_city'])) {
            $shop_config['default_region_id'] = $shop_config['default_city'];
        } elseif (!empty($shop_config['default_region'])) {
            $shop_config['default_region_id'] = $shop_config['default_region'];
        } elseif (!empty($shop_config['default_country'])) {
            $shop_config['default_region_id'] = $shop_config['default_country'];
        } else {
            $shop_config['default_region_id'] = 0;
        }
        $shop_config->update();
    }

    /**
     * Очищает все корзины для установки первичного ключа
     */
    function beforeUpdate4067()
    {
        $cart_item = new \Shop\Model\Orm\CartItem();
        $sql = 'TRUNCATE TABLE ' . $cart_item->_getTable();
        \RS\Db\Adapter::sqlExec($sql);
    }

    /**
     * Добавляем для всех мультисайтов причины отмены заказов
     */
    function afterUpdate3021()
    {
        $sites = \RS\Site\Manager::getSiteList(false);
        if (!empty($sites)){
            $module = new \RS\Module\Item('shop');
            $installer = $module->getInstallInstance();
            foreach($sites as $site) {
                $installer->importCsv(new \Shop\Model\CsvSchema\SubStatus(), 'substatus', $site['id']);
            }
        }
    }
    
    /**
    * Патч для релиза 2.0.0.73 и ниже
    * Проставляет сортировки в доставках и оплатах
    */
    function afterUpdate20073()
    {
        //Обновление доставок
        $q = \RS\Orm\Request::make()
            ->update(new \Shop\Model\Orm\Delivery())
            ->set("`sortn` = `id`")
            ->where('sortn = 0')
            ->exec();
        
        //Обновление оплат    
        $q = \RS\Orm\Request::make()
            ->update(new \Shop\Model\Orm\Payment())
            ->set("`sortn` = `id`")
            ->where('sortn = 0')
            ->exec();
    } 
    
    /**
    * Плагие проставляет всем заказам уникальные номера заказа
    * 
    */
    function afterUpdate20063()
    {
        //Подгрузим все заказы
        \RS\Orm\Request::make()
            ->update(new \Shop\Model\Orm\Order())
            ->set('order_num = id')
            ->where('order_num IS NULL')
            ->exec();
    }    
    
    /**
    * Патч проставляет site_id идентификатор для адреса доставки
    */
    function afterUpdate20087()
    {
        \RS\Orm\Request::make()
            ->update(new \Shop\Model\Orm\Address())->asAlias('A')
            ->update(new \Shop\Model\Orm\Order())->asAlias('O')
            ->set('A.site_id = O.site_id')
            ->where('A.id = O.use_addr')
            ->exec();
        
        //Оставшиеся адреса привяжем к текущему сайту
        \RS\Orm\Request::make()
            ->update(new \Shop\Model\Orm\Address())
            ->set([
                'site_id' => \RS\Site\Manager::getSiteId()
            ])
            ->where('site_id IS NULL')
            ->exec();
    }
    
    /**
    * Устанавливаем дату обновления равную дате создания всем старым заказам
    */
    function afterUpdate200152()
    {
        \RS\Orm\Request::make()
            ->update(new \Shop\Model\Orm\Order())
            ->set('dateofupdate = dateof')
            ->where('dateofupdate IS NULL')
            ->exec();
    }
    
    /**
    * Добавляет ещё один статус для онлайн выбивания чеков
    */
    function afterUpdate200198()
    {
        //Получим текущие сайты
        $sites = \RS\Site\Manager::getSiteList(false);
        if (!empty($sites)){
            $statuses = UserStatus::getDefaultStatues();
            $status   = $statuses[UserStatus::STATUS_NEEDRECEIPT];

            $new_user_status = new UserStatus();
            $new_user_status->getFromArray($status);
            $new_user_status['type'] = UserStatus::STATUS_NEEDRECEIPT;
            $new_user_status['is_system'] = 1;

            foreach ($sites as $site){
               unset($new_user_status['id']);
               $new_user_status['site_id'] = $site['id'];
               $new_user_status->insert();
            }
        }
    }

    /**
     * Установим флаги системным статусам
     */
    function afterUpdate200197()
    {
        $statuses = UserStatus::getDefaultStatusesTitles();
        \RS\Orm\Request::make()
            ->update(new UserStatus() )
            ->set([
                'is_system' => 1
            ])
            ->whereIn('type', array_keys($statuses))
            ->exec();
    }
}
