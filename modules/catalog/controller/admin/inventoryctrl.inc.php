<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar,
    \RS\Orm\Type;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use \Catalog\Model\Orm\Inventory\Inventorization;
use \Catalog\Model\Inventory\DocumentApi;
use \RS\Controller\Admin\Helper\CrudCollection;
use \Catalog\Model\Inventory\InventoryTools;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

/**
 *  Основной контроллер складского учета
 *
 * Class Inventory
 * @package Catalog\Controller\Admin
 */
class InventoryCtrl extends \RS\Controller\Admin\Crud
{

    function __construct()
    {
        parent::__construct(new DocumentApi());
    }

    /**
     *  Открывает диалоговое окно включения складского учета
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionEnableControl()
    {
        $config = \RS\Config\Loader::byModule($this);
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->addSection([
                'success' => false,
                'close_dialog' => true,
            ])
                ->addEMessage($access_error);
        }

        $helper = new CrudCollection($this, null, null, [
            'topTitle' => t('Включить складской учет'),
            'bottomToolbar' => new Toolbar\Element([
                    'Items' => [
                        'save' => new ToolbarButton\SaveForm($this->router->getAdminUrl('EnableControlBySteps', null, 'catalog-inventoryctrl'), t('Продолжить'), ['attr' => ['class' => 'crud-get btn-success']]),
                        new ToolbarButton\Cancel($this->router->getAdminUrl('edit', ['mod' => 'catalog'], 'modcontrol-control'))
                    ]]
            ),
            'viewAs' => 'form'
        ]);

        $this->view->assign([
            'enabled' => $config['inventory_control_enable'],
        ]);
        $helper['form'] = $this->view->fetch('form/inventory/enable_form.tpl');
        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     *  Скачать форму документа для печати
     */
    function actionGetDocumentPrintForm()
    {
        $document_id = $this->url->request('document_id', TYPE_INTEGER);
        $document_type = $this->url->request('document_type', TYPE_STRING);
        $document_api = new DocumentApi();
        $api = $document_api->getApiForDocumentType($document_type);
        $this->wrap_output = false;
        $form = $api->getDocumentPrintForm($document_id, $document_type);
        header('Content-Type: application/pdf');
        return $form;
    }

    /**
     *  Выключает складской учет
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionDisable()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->addSection([
                'success' => false,
                'close_dialog' => true,
            ])
                ->addEMessage($access_error);
        }
        $config = \RS\Config\Loader::byModule('catalog');
        $config['inventory_control_enable'] = 0;
        $config->update();
        $this->result->addMessage(t('Складской учет отключен'));
        return $this->result->addSection([
            'success' => true,
            'noUpdate' => true,
        ]);
    }

    /**
     *  Пошагово создает документы инвентаризации
     */
    function actionEnableControlBySteps()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->addSection([
                'success' => false,
                'close_dialog' => true,
            ])
                ->addEMessage($access_error);
        }
        $warehouse = $this->url->request('warehouse', TYPE_INTEGER, 0);
        $offset = $this->url->request('offset', TYPE_INTEGER, 0);
        $inventory_id = $this->url->request('inventory', TYPE_INTEGER, 0);
        $inventory = new \Catalog\Model\Inventory\InventorizationApi();
        $result = $inventory->makeTotalInventorization($warehouse, $offset, $inventory_id);
        if($result === true){
            // завершено формирование документов
            $config = \RS\Config\Loader::byModule('catalog');
            $config['inventory_control_enable'] = 1;
            $config->update();
            $this->result->addMessage(t('Складской учет включен'));
            return $this->result->addSection([
                'success' => true,
                'noUpdate' => true,
                'close_dialog' => true,
            ]);
        }else{
            // продолжить формирование документов
            return $this->result
                ->setSuccess($result['success'])
                ->addSection('repeat',true)
                ->addSection('noUpdate', true)
                ->addSection('queryParams',$result['queryParams']);
        }
    }

    /**
     *  Скачивает csv файл с товарами документа
     */
    function actionGetProductsCsv()
    {
        $this->wrapOutput(false);
        $id   = $this->url->request('id', TYPE_INTEGER, 0);
        $document_type =  $this->url->request('type', TYPE_STRING, 0);
        if($id){
            $tools = new InventoryTools();
            return $tools->getProductsCsv($id, $document_type);
        }
    }

    /**
     *  Загружает csv файл и добавляет товары в открытый документ
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionLoadProductsCsv()
    {
        $type = $this->url->request('type', TYPE_STRING, 0);
        $helper = new CrudCollection($this, null, null, [
            'topTitle' => t('Загрузить csv файл'),
            'bottomToolbar' => new Toolbar\Element([
                    'Items' => [
                        'save' => new ToolbarButton\SaveForm($this->router->getAdminUrl('LoadProductsCsv', null, 'catalog-inventoryctrl'), t('Загрузить')),
                        new ToolbarButton\Cancel($this->router->getAdminUrl('edit', ['mod' => 'catalog'], 'modcontrol-control'))
                    ]]
            ),
            'viewAs' => 'form'
        ]);
        $formObject = new \RS\Orm\FormObject(new \RS\Orm\PropertyIterator(
            [
                'file' => new Type\File([
                    'description' => t('Файл CSV'),
                    'allow_file_types' => ['csv'],
                ]),
                'example_file' => new Type\Integer([
                    'description' => t('Пример файла'),
                    'template' => '%catalog%/form/inventory/download_example_csv.tpl',
                ])
            ]
        ));
        $helper->setFormObject($formObject);
        if($this->url->isPost()){
            $api = new DocumentApi();
            $tools = new InventoryTools();

            $uploader = new \RS\File\Uploader();
            $uploader->uploadFile($this->url->files('file'));
            $file = $uploader->getAbsolutePath();

            $string = file_get_contents($file);
            $rows = explode(PHP_EOL, $string);
            $rows = array_slice($rows, 1);

            $items = [];
            foreach ($rows as $row){
                if(!$item = $tools->prepareItemsFromCsv($row)){
                    continue;
                }
                $items[$item['uniq']] = $item;
            }
            $table = $api->getProductsTable(null, $type, $items);
            $inputs = $api->getAddedItems(null, $type, $items);

            if ($products = $tools->getExcluded()) {
                $this->result->addEMessage(t('Следующие товары были пропущены: %0',
                    [implode(',', $products)]));
            }

            return $this->result->addSection(
                [
                    'success' => true,
                    'table' => $table,
                    'inputs' => $inputs,
                    'noUpdateTarget' => true,
                ]
            )
                ->addSection('close_dialog', true);
        }
        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     *  Скачать присмер файла csv для импорта товаров в документ
     */
    function actionGetExampleCsv()
    {
        $this->wrap_output = false;
        $tools = new InventoryTools();
        $tools->GetExampleCsv();
    }

    /**
     *  Добывляет тоавры из каталога товаров
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAddProductsFromCatalog()
    {
        $ids = $this->url->request('chk', TYPE_ARRAY, [], false);
        $tools = new InventoryTools();
        $catalog_controller = new \Catalog\Controller\Admin\Ctrl();
        $request_object = $this->api->getSavedRequest($catalog_controller->controller_name.'_list');
        if ($this->url->request($this->selectAllVar, TYPE_STRING) == 'on' &&  $request_object !== null) {
            $ids = $this->api->getIdsByRequest($request_object);
        }
        $type = $this->url->request('document_type', TYPE_STRING, 0);

        $helper = new CrudCollection($this, null, null, [
            'topTitle' => t('Добавить товары в документ'),
            'bottomToolbar' => new Toolbar\Element([
                    'Items' => [
                        'save' => new ToolbarButton\SaveForm($this->router->getAdminUrl('MakeDocumentFromCatalog', null, 'catalog-inventoryctrl'), t('Далее')),
                        new ToolbarButton\Cancel($this->router->getAdminUrl('edit', ['mod' => 'catalog'], 'modcontrol-control'))
                    ]]
            ),
            'viewAs' => 'form'
        ]);
        $filter = new \RS\Html\Filter\Type\User('user_id', t('Пользователь'));

        $form_object = new FormObject(new PropertyIterator([
            'exist' => new Type\Varchar([
                'listFromArray' => [[
                    'no' => t('Создать новый документ'),
                    'yes' => t('Добавить в существующий'),
                ]],
                'radioListView' => true,
                'default' => 'no'
            ]),
            'document_id' => new \Catalog\Model\OrmType\SelectDocument([
                'type' => $type,
                'minLength' => 1,
                'attr' => [['size' => 40, 'placeholder' => t('id документа')]]
            ])
        ]));

        if ($this->url->isPost()) {
            $exist = $this->url->request('exist', TYPE_STRING, null);
            $document_id = $this->url->request('document_id', TYPE_INTEGER, null);
            if($exist == 'yes' && !$document_id){
                $form_object->addError("Не выбран документ", 'document_id');
            }
            if ($form_object->checkData()) {

                $ids = $this->url->request('ids', TYPE_ARRAY, [], false);
                $type = $this->url->request('document_type', TYPE_STRING, null);
                $api = new DocumentApi();
                if($exist == 'yes' && $document_id){
                    $do = "edit";
                }else{
                    $do = "add";
                }
                $crud_add_url = $api->getControllerUrlByDocumentType($type, $do, ['document_type' => $type, 'id' => $document_id, 'exist' => $exist]);
                $_SESSION[$tools::$session_product_ids] = $ids;

                return $this->result
                    ->setSuccess(true)
                    ->addSection('callCrudAdd', $crud_add_url);

            } else {
                return $this->result
                                ->setSuccess(false)
                                ->setErrors($form_object->getDisplayErrors());
            }
        }

        $this->view->assign([
            'amount' => count($ids),
            'ids' => $ids,
            'inputs' => $ids,
            'document_type' => $type,
            'filter' => $filter,
            'form_object' => $form_object
        ]);

        $helper['form'] = $this->view->fetch('%catalog%/form/inventory/add_to_document.tpl');
        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     *  Функция для поля поиска документа
     *
     * @return string
     */
    function actionAjaxSearchDocument()
    {
        $type = $this->url->request('document_type', TYPE_STRING, null);

        $api = new DocumentApi();
        $api = $api->getApiForDocumentType($type);

        $term = $this->url->request('term', TYPE_STRING);
        $api->setFilter('id', $term);
        $api->setFilter('type', $type);
        $list = $api->getList();

        $json = [];
        foreach ($list as $document) {
            $json[] = [
                'label' => t('Документ №%id от %date', [
                    'id' => $document['id'],
                    'date' => date('d.m.Y', strtotime($document['date']))
                ]),
                'id' => $document['id'],
                'desc' => "",
            ];
        }
        return json_encode($json);
    }

    /**
     *  Открыть окно архивации/восстановления документов
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionOpenArchiveWindow()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->addSection([
                'success' => false,
                'close_dialog' => true,
            ])
                ->addEMessage($access_error);
        }
        $tools = new InventoryTools();
        $mode = $this->url->request('mode', TYPE_STRING);
        if ($this->url->isPost()) {
            $archive = $this->url->request('archive', TYPE_STRING, null);
            if($archive == 'all'){
                $date = 'all';
            }else{
                $date = $this->url->request('date', TYPE_STRING, null);
            }
            $no_error = $tools->checkArchiveErrors($date, $mode);
            if($no_error === true){
                return $this->result
                    ->addSection('callCrudAdd', $this->router->getAdminUrl('ProcessArchive', ['date' => $date, 'mode' => $mode]))
                    ->setSuccess(true);
            }else{
                $tools->addError($no_error['error'], t('Дата'), 'separator');
                return $this->result
                    ->setSuccess(false)
                    ->setErrors($tools->getDisplayErrors());
            }
        }

        $helper = new CrudCollection($this, null, null, [
            'topTitle' => $mode == $tools::$do_archive ? t('Архивация документов') : t('Разархивировать документы'),
            'bottomToolbar' => new Toolbar\Element([
                    'Items' => [
                        new ToolbarButton\SaveForm($this->router->getAdminUrl('OpenArchiveWindow', [], 'catalog-inventoryctrl'), t('Начать')),
                        new ToolbarButton\Cancel($this->router->getAdminUrl('edit', ['mod' => 'catalog'], 'modcontrol-control'), t('Закрыть'))
                    ]]
            ),
            'viewAs' => 'form'
        ]);
        $form_object = new FormObject(new PropertyIterator([
            'date' => new Type\Datetime([
                'description' => $mode == $tools::$do_archive ? t('Архивировать до даты') : t('Начиная с даты'),
            ]),
        ]));
        $this->view->assign([
            'form_object' => $form_object,
            'mode' => $mode,
            'archive_constant' => $tools::$do_archive,
        ]);
        $helper['form'] = $this->view->fetch('%catalog%/form/inventory/arcive_window.tpl');
        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     *  Пошагово архивирует/восстанавливает документы
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionProcessArchive()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->addSection([
                'success' => false,
                'close_dialog' => true,
            ])
                ->addEMessage($access_error);
        }
        $date = $this->url->request('date', TYPE_STRING, null);
        $step = $this->url->request('step', TYPE_INTEGER, 0);
        $mode = $this->url->request('mode', TYPE_STRING);
        $tools = new InventoryTools();
        $params = [];
        if($step){
            $params = $tools->getStepData();
        }

        $result = $tools->archiveProducts($date, $step, $mode, $params);
        $tools->setStepData($result);

        $helper = new CrudCollection($this, null, null, [
            'topTitle' => $mode == $tools::$do_archive ? t('Архивация документов') : t('Восстановление документов'),
            'bottomToolbar' => new Toolbar\Element([
                    'Items' => [
                        new ToolbarButton\Cancel($this->router->getAdminUrl('edit', ['mod' => 'catalog'], 'modcontrol-control'), t('Закрыть'))
                    ]]
            ),
            'viewAs' => 'form'
        ]);

        $this->view->assign([
            'step' => $step,
            'date' => $date,
            'params' => $result,
            'mode' => $mode,
        ]);
        $helper['form'] = $this->view->fetch('%catalog%/form/inventory/process_archive.tpl');
        return $this->result->setTemplate($helper->getTemplate());
    }
}