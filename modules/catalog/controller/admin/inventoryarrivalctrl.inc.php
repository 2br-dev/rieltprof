<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton;

/**
 *  Контроллер документа оприходования
 *
 * Class InventoryArrival
 * @package Catalog\Controller\Admin
 */
class InventoryArrivalCtrl extends \Catalog\Controller\Admin\AbstractDocument
{
    public
        $type = \Catalog\Model\Orm\Inventory\Document::DOCUMENT_TYPE_ARRIVAL,
        $template_title = '%catalog%/form/inventory/field_arrival_title.tpl';

    function __construct()
    {
        $this->top_help = t('В документе оприходования фиксируется прибытие товаров на склад. Влияет на поле "остаток" у товаров.');
        $this->top_title = t('Оприходования товаров');
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
        $helper->setTopTitle($primaryKeyValue ? t('Редактировать документ оприходования') : t('Добавить оприходование товаров'));
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }
}