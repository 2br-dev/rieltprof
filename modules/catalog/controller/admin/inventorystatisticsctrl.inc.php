<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin;
use Catalog\Model\Orm\Inventory\Document;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar,
    \RS\Html\Toolbar\Button as ToolbarButton;
use \RS\Controller\Admin\Helper\CrudCollection;

/**
 *  Контроллер статистики складского учета
 *
 * Class InventoryStatisticsCtrl
 * @package Catalog\Controller\Admin
 */
class InventoryStatisticsCtrl extends \RS\Controller\Admin\Front
{
    protected
        $doc_amount = 10; // количество документов в таблице при отображении статистики. для пагинации


    function actionProductStatistics()
    {
        $product_id = $this->url->get('product_id', TYPE_INTEGER);
        $product = new \Catalog\Model\Orm\Product($product_id);
        $helper = new CrudCollection($this, null, null, [
            'topTitle' => t('Товар'.' '.'«'.$product['title'].'»'),
            'bottomToolbar' => new Toolbar\Element([
                    'Items' => [
                        new ToolbarButton\Cancel($this->router->getAdminUrl('edit', ['mod' => 'catalog'], 'modcontrol-control'), t('Закрыть'))
                    ]]
            ),
            'viewAs' => 'form',
        ]);

        $offers = $product->fillOffers();
        $api = new \Catalog\Model\Inventory\DocumentApi();
        $warehouse_api = new \Catalog\Model\WareHouseApi();
        $warehouses = $warehouse_api->getList();
        $result = [];
        $page = 1;

        $statistics_api = new \Catalog\Model\Inventory\StatisticsApi();

        foreach ($warehouses as $warehouse){
            foreach ($offers['items'] as $sortn => $offer){
                $stats = $statistics_api->getOfferStatistics($product_id, $offer['id'], $warehouse['id']);
                if($stats){
                    $total = count($stats['docs']);
                    $product_info = [
                        'product_id' => $product_id,
                        'offer_id' => $offer['id'],
                        'warehouse_id' => $warehouse['id'],
                    ];
                    $stats['paginator'] = $statistics_api->getPaginator($total, $this->doc_amount, $product_info, $page);
                    $offer_documents = array_slice($stats['docs'], 0, $this->doc_amount);
                    $stats['docs'] = $offer_documents;
                    $result[$warehouse['title']][$offer['id']] = $stats;
                }
            }
        }

        $this->view->assign([
            'result' => $result,
            'document_titles' => $api->getDocumentTitles(),
            'reserve_status' => Document::DOCUMENT_TYPE_RESERVE,
            'write_off_status' => Document::DOCUMENT_TYPE_WRITE_OFF,
        ]);

        if(empty($result)){
            $this->result->addMessage(t('Товар не состоит ни в одном документе'));
            $this->result->addSection([
                'close_dialog' => true,
            ]);
        }

        $helper['form'] = $this->view->fetch('%catalog%/form/inventory/product_statistics.tpl');
        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     *  Получить таблицу статистики одной комплектации
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionGetStatisticTable()
    {
        $product_id = $this->url->request('product_id', TYPE_INTEGER);
        $offer_id = $this->url->request('offer_id', TYPE_INTEGER);
        $warehouse_id = $this->url->request('warehouse_id', TYPE_INTEGER);
        $page = $this->url->request('p', TYPE_INTEGER, false);

        $product_info = [
            'product_id' => $product_id,
            'offer_id' => $offer_id,
            'warehouse_id' => $warehouse_id,
        ];

        $document_api = new \Catalog\Model\Inventory\DocumentApi();
        $api = new \Catalog\Model\Inventory\StatisticsApi();
        $offer_statistics = $api->getOfferStatistics($product_id, $offer_id, $warehouse_id);

        $total = count($offer_statistics['docs']);
        $offer_statistics['paginator'] = $api->getPaginator($total, $this->doc_amount, $product_info, $page);

        $offset = ($page - 1) * $this->doc_amount;
        $length = $this->doc_amount;

        $offer_statistics['docs'] = array_slice($offer_statistics['docs'], $offset, $length);

        $this->view->assign([
            'data' => $offer_statistics,
            'document_titles' => $document_api->getDocumentTitles(),
        ]);
        return $this->result->setTemplate('%catalog%/inventory/offer_statistics_table.tpl');
    }
}
