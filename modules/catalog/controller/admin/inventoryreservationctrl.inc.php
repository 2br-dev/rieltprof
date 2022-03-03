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
    \RS\Html\Toolbar\Button as ToolbarButton;

/**
 *  Контроллер документа резервирования
 *
 * Class InventoryReservation
 * @package Catalog\Controller\Admin
 */
class InventoryReservationCtrl extends \Catalog\Controller\Admin\AbstractDocument
{
    public
        $template_title = '%catalog%/form/inventory/field_reserve_title.tpl';

    function __construct()
    {
        $this->top_help = t('В документе резервирования указаны товары, которые еще находятся на складе, но вскором времени будут списаны, он увеличивает поле "резерв" у товара. Документ создается автоматически после оформления заказа.');
        $this->top_title = t('Резервирования товаров');
        $this->type = Document::DOCUMENT_TYPE_RESERVE;
        $this->config = \RS\Config\Loader::byModule($this);
        parent::__construct();
    }

    /**
     * Форма добавления документа
     *
     * @param mixed $primaryKeyValue - id редактируемой записи
     * @param boolean $returnOnSuccess - Если true, то будет возвращать === true при успешном сохранении,
     *                                   иначе будет вызов стандартного _successSave метода
     * @param null|\RS\Controller\Admin\Helper\CrudCollection $helper - текуй хелпер
     * @return \RS\Controller\Result\Standard|bool
     */
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $helper = $this->getHelper();
        $helper->setTopTitle($primaryKeyValue ? t('Редактировать документ резервирования') : t('Добавить резервирование товаров'));
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     *  Создает документ по заказу
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionCreateFromOrder()
    {
        $order_id = $this->url->request('order_id', TYPE_INTEGER, 0);
        $this->result->addSection([
            'noUpdate' => true,
            'reserve' => true,
        ]);
        if($order_id){
            $order = new \Shop\Model\Orm\Order($order_id);
            $stock_manager = \Catalog\Model\StockManager::getInstance();
            $result = $stock_manager->updateRemainsFromOrder($order, \RS\Orm\AbstractObject::INSERT_FLAG);

            if(!$result){
                $this->result->addEMessage('В заказе нет товаров');
                return $this->result->setSuccess(false);
            }
        }
        return $this->result->addSection([
            'success' => true,
            'noUpdate' => true,
        ]);
    }

    /**
     *  Переводит документ в списание
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionMakeWriteOff()
    {
        $reserve_id = $this->url->request('id', TYPE_INTEGER, 0);
        if($reserve_id){
            $tools = new \Catalog\Model\Inventory\InventoryTools();
            $tools->changeDocumentType($reserve_id, $this->type, \Catalog\Model\Orm\Inventory\Document::DOCUMENT_TYPE_WRITE_OFF);
            return $this->result->addSection([
                'success' => true,
                'redirect' => $this->router->getAdminUrl('edit', ['id' => $reserve_id], 'catalog-inventorywriteoff'),
            ]);
        }
    }
}
