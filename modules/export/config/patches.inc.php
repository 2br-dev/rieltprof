<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Config;

use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use RS\Orm\Type as OrmType;

/**
* Патчи к модулю
*/
class Patches extends \RS\Module\AbstractPatches
{
    /**
    * Возвращает список имен существующих патчей
    */
    function init()
    {
        return [
            '20042',
            '3112',
            '4043'
        ];
    }

    function afterUpdate4043()
    {
        $offer = new Offer();
        $product = new Product();
        $offer->getPropertyIterator()->append([
            t('Экспорт'),
            'market_sku' => new OrmType\Varchar( [
                'description' => t('SKU на Яндекс.Маркете (market-sku)'),
                'hint' => t('Используется в выгрузке YML на Яндекс.Маркет')
            ]),
        ]);
        $product->getPropertyIterator()->append([
            t('Экспорт'),
            'market_sku' => new OrmType\Varchar( [
                'description' => t('SKU на Яндекс.Маркете (market-sku)'),
                'hint' => t('Используется в выгрузке YML на Яндекс.Маркет')
            ]),
        ]);

        $offer->dbUpdate();
        $product->dbUpdate();
    }
    
    /**
    * Переносит значение, в связи с переименованием поля
    */
    function afterUpdate20042()
    {
        try {
            \RS\Orm\Request::make()
                ->update(new \Export\Model\Orm\ExportProfile())
                ->set('available = avaible')
                ->exec();
        } catch (\RS\Exception $e) {}
    }
    
    /**
    * Переносит настройки google экспорта в fieldmap
    */
    function afterUpdate3112()
    {
        $api = new \Export\Model\Api();
        $list = $api->getList();
        foreach ($list as $profile) {
            if ($profile['class'] == 'google' && !isset($profile['data']['fieldmap'])) {
                $data = $profile['data'];
                
                $data['fieldmap']['g:google_product_category'] = [
                    'prop_id' => ($profile['data']['category_property_id'] == 0) ? -1 : $profile['data']['category_property_id'],
                    'value' => '',
                ];
                $data['fieldmap']['g:adult'] = [
                    'prop_id' => $profile['data']['adult_property_id'],
                    'value' => '',
                ];
                $data['fieldmap']['g:condition'] = [
                    'prop_id' => ($profile['data']['condition_property_id'] == 0) ? -1 : $profile['data']['condition_property_id'],
                    'value' => ($profile['data']['condition_property_id'] == 0) ? t("новый") : '',
                ];
                $data['offer_type'] = 'rss_2_0';
                
                $profile['data'] = $data;
                $profile->update();
            }
        }
    }
}
