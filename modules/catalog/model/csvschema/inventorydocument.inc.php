<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvSchema;
use \RS\Csv\Preset,
    \Catalog\Model\Orm\Property as OrmProperty;

/**
 * Схема экспорта/импорта значений характеристик в CSV
 */
class InventoryDocument extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        $request = \RS\Http\Request::commonInstance();
        $params = $request->request('params', TYPE_ARRAY);
        if(!$params){
            $params = $_SESSION['export_data']['params'];
        }
        $request = \Catalog\Model\Inventory\DocumentApi::getSavedRequest($params['ctrl'].'_list');
        $request->from = '';
        $request->joins = [];
        $request->select = '';
        $request->select('A.*, item.*, offer.barcode as offer_barcode, prod.barcode as prod_barcode');
        $request->from(new \Catalog\Model\Orm\Inventory\Document(), 'A');
        $request->join(new \Catalog\Model\Orm\Inventory\DocumentProducts(), 'item.document_id = A.id', 'item');
        $request->join(new \Catalog\Model\Orm\Product(), 'prod.id = item.product_id', 'prod');
        $request->join(new \Catalog\Model\Orm\Offer(), 'item.offer_id = offer.id', 'offer');
        parent::__construct(new Preset\Base(
            [
                'ormObject' => new \Catalog\Model\Orm\Inventory\DocumentProducts(),
                'excludeFields' => [
                    'id', 'site_id', 'uniq', 'offer_id', 'product_id', 'amount',
                ],
                'titles' => ['warehouse' => t('Склад'), 'document_id' => t('Номер документа')],
                'savedRequest' => $request,
            ]),
            [
                new \Catalog\Model\CsvPreset\JoinedColumns(
                    [
                        'joinFields' => [
                            'type' => t('Тип документа'),
                            'date' => t('Дата'),
                            'applied' => t('Проведен'),
                            'archived' => t('Заархивирован'),
                            'prod_barcode' => t('Штрихкод товара'),
                            'offer_barcode' => t('Штрихкод комплектации'),
                            'amount' => t('Количество'),
                            'comment' => t('Комментарий'),
                        ],
                        'excludeFields' => [
                            'site_id', 'items_count'
                        ],
                        'modificateColumns' => [
                            'type' => function($value)
                            {
                                $api = new \Catalog\Model\Inventory\DocumentApi();
                                $titles = $api->getDocumentTitles();
                                return $titles[$value];
                            },
                            'amount' => function ($value)
                            {
                                return abs($value);
                            }
                        ]
                    ]
                )
            ]
        );
    }
}
