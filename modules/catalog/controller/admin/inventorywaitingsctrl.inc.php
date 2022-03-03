<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin;
use Catalog\Model\Inventory\InventoryTools;
use Catalog\Model\Orm\Inventory\Document;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton;

/**
 *  Контроллер документа ожидания
 *
 * Class InventoryWaitings
 * @package Catalog\Controller\Admin
 */
class InventoryWaitingsCtrl extends \Catalog\Controller\Admin\AbstractDocument
{
    public
        $template_title = '%catalog%/form/inventory/field_waiting_title.tpl';

    function __construct()
    {
        $this->top_help = t('В этом документе находятся товары, которых еще нет на складе, но скоро прибудут, он увеличивает поле "ожидание" у товара. Этот документ Можно перевести в оприходование.');
        $this->top_title = t('Ожидания товаров');
        $this->type = Document::DOCUMENT_TYPE_WAITING;
        $this->config = \RS\Config\Loader::byModule($this);
        parent::__construct();
    }

    function helperAdd()
    {
        $helper = parent::helperAdd();
        $helper->setFormSwitch('writeoff');
        return $helper;
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
        $helper->setTopTitle($primaryKeyValue ? t('Редактировать документ ожидания') : t('Добавить ожидаение товаров'));
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     *  Переводит документ в оприходование
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionMakeArrival()
    {
        $waiting_id = $this->url->request('id', TYPE_INTEGER, 0);
        if($waiting_id){
            $tools = new InventoryTools();
            $tools->changeDocumentType($waiting_id, $this->type, Document::DOCUMENT_TYPE_ARRIVAL);
            return $this->result->addSection([
                'success' => true,
                'redirect' => $this->router->getAdminUrl('edit', ['id' => $waiting_id], 'catalog-inventoryarrivalctrl'),
            ]);
        }
    }
}
