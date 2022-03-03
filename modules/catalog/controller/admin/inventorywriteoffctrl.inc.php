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
 *  Контроллер документа списания
 *
 * Class InventoryWriteOff
 * @package Catalog\Controller\Admin
 */
class InventoryWriteOffCtrl extends \Catalog\Controller\Admin\AbstractDocument
{
    public
        $template_title = '%catalog%/form/inventory/field_write_off_title.tpl';

    function __construct()
    {
        $this->top_help = t('В документе указаны товары, которые списаны со склада. Влияет на поле "остаток" у товаров.');
        $this->top_title = t('Списания товаров');
        $this->type = Document::DOCUMENT_TYPE_WRITE_OFF;
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
        $helper->setTopTitle($primaryKeyValue ? t('Редактировать документ списания') : t('Добавить списание товаров'));
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }
}