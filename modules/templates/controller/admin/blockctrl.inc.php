<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Controller\Admin;

use RS\Controller\Block;
use RS\Module\AbstractModel;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use Templates\Model\TemplateModuleApi;

/**
* Настройка блоков в административной панели (конструктор сайта)
*/
class BlockCtrl extends \RS\Controller\Admin\Crud
{
    /**
     * @var \Templates\Model\PageApi $pageApi
     */
    protected $pageApi;
    /**
     * @var \Templates\Model\ContainerApi $containerApi
     */
    protected $containerApi;
    /**
     * @var AbstractModel\EntityList $sectionApi
     */
    protected $sectionApi;
    /**
     * @var AbstractModel\EntityList $sectionModuleApi
     */
    protected $sectionModuleApi;

    /**
     * Конструктор класса
     */
    function __construct()
    {
        $this->pageApi = new \Templates\Model\PageApi();
        parent::__construct($this->pageApi);
        
        //Создаем типовые модели
        $this->containerApi = new \Templates\Model\ContainerApi();
        
        $this->sectionApi = new AbstractModel\EntityList(new \Templates\Model\Orm\Section, 
        [
            'loadOnDelete' => true
        ]);

        $this->sectionModuleApi = new AbstractModel\EntityList(new \Templates\Model\Orm\SectionModule,
        [
            'loadOnDelete' => true
        ]);
    }

    /**
     * Страница самого конструктора сайта
     *
     * @return mixed
     * @throws \RS\Theme\Exception
     */
    function actionIndex()
    {
        $page_id = $this->url->request('page_id', TYPE_INTEGER);
        $context = $this->url->request('context', TYPE_STRING, 'theme');


        $page = new \Templates\Model\Orm\SectionPage($page_id);
        $default_page = \Templates\Model\Orm\SectionPage::loadByRoute('default', $context);
        if (!$page['id']) {
            $page = $default_page;
        }
        $site_config = \RS\Config\Loader::getSiteConfig();
        $this->pageApi->setFilter('context', $context);
        $theme = \RS\Theme\Item::makeByContext($context);

        $pages = $this->pageApi->getList();
        //Сортируем страницы по алфавиту
        usort($pages, function($a, $b) {
            return strcmp($a->getRoute()->getDescription(), $b->getRoute()->getDescription());
        });

        $grid_system = $theme->getGridSystem();
        $this->view->assign([
            'defaultPage' => $default_page,
            'currentPage' => $page,
            'currentTheme' => $site_config['theme'],
            'pages' => $pages,
            'page_id' => $page_id,
            'context_list' => \RS\Theme\Manager::getContextList(),
            'context' => $context,
            'grid_system' => $grid_system,
            'is_bootstrap' => strpos($grid_system, 'bootstrap') !== false //True, если любая версия bootstrap
        ]);
        return parent::actionIndex();
    }

    /**
     * Обертка страницы контсруктора
     *
     * @return \RS\Controller\Admin\Helper\CrudCollection
     * @throws \SmartyException
     */
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Конструктор сайта'));
        $helper->setTopHelp($this->view->fetch('help/blockctrl_index.tpl'));
        $helper->setTopToolbar(new Toolbar\Element( [
            'Items' => [
                new ToolbarButton\Add($this->url->replaceKey([$this->action_var => 'add']), t('добавить страницу')),
                new ToolbarButton\Space(),
                new ToolbarButton\Button(null, t('сохранить эталон темы'), [
                    'attr' => [
                        'title' => t('Сохраняет block.xml в папке с темой'),
                        'class' => 'crud-get',
                        'data-confirm-text' => t('Вы действительно хотите перезаписать структуру блоков blocks.xml в каталоге темы ?'),
                        'data-url' => $this->router->getAdminUrl('saveTheme')
                    ]
                ]),
                new ToolbarButton\Button($this->url->replaceKey([$this->action_var => 'export']), t('экспорт'), [
                    'attr' => [
                        'title' => t('Экспорт в XML файл')
                    ]
                ]),
                new ToolbarButton\Button($this->url->replaceKey([$this->action_var => 'import']), t('импорт'), [
                    'attr' => [
                        'class' => 'crud-add',
                        'title' => t('Импорт из XML файла')
                    ]
                ]),
            ]]));
        $helper['template'] = 'block_manager.tpl';
        return $helper;
    }

    /**
     * Добавление страницы
     *
     * @param null|integer $primaryKeyValue
     * @param bool $returnOnSuccess
     * @param null $helper
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $this->api = $this->pageApi;
        $helper = $primaryKeyValue ? $this->helperEdit() : $this->helperAdd();
        if ($primaryKeyValue) {
            $helper->setTopTitle(t('Редактировать страницу'));
        } else {
            $helper->setTopTitle(t('Добавить страницу'));
        }
        
        $this->setHelper($helper);
        $this->api->getElement()->tpl_module_folders = \RS\Module\Item::getResourceFolders('templates');
        
        if ($primaryKeyValue == null) {
            $elem = $this->api->getElement();
            $elem['inherit'] = 1;
            $elem['context'] = $this->url->request('context', TYPE_STRING, 'theme');
        }

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * Редактирование страницы
     *
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionEditPage()
    {
        $this->view->assign('dialogTitle', t('Редактирование страницы'));
        $id = $this->url->get('id', TYPE_STRING, 0);
        $context = $this->url->get('context', TYPE_STRING, 'theme');
        if ($id) {
            $this->pageApi->getElement()->load($id);
        } else {
            $page = \Templates\Model\Orm\SectionPage::loadByRoute('default', $context);
            $this->pageApi->getElement()->getFromArray($page->getValues());
            $id = $page['id'];
        }                     
        return $this->actionAdd($id);
    }

    /**
     * Удаление страницу
     *
     * @return mixed
     */
    function actionDelPage()
    {
        $this->api = $this->pageApi;
        return parent::actionDel();
    }

    /**
     * Копирование контейнера
     *
     * @return mixed|\RS\Controller\Result\Standard
     * @throws \RS\Orm\Exception
     * @throws \SmartyException
     */
    function actionCopyContainer()
    {
        $context = $this->url->request('context', TYPE_STRING, 'theme');        
        if ($this->url->isPost()) {
            $to_page           = $this->url->request('page_id', TYPE_INTEGER);
            $to_container_type = $this->url->request('type', TYPE_INTEGER);
            $from_container    = $this->url->request('from_container', TYPE_INTEGER);

            $copy_id = $this->containerApi->copyContainer($from_container, $to_page, $to_container_type);
            $this->result->setSuccess( $copy_id ? true : false );
            if ($copy_id){
                $this->result->addSection('copy_id', $copy_id);
            }
            
            if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                if (!$this->result->isSuccess()) {
                    $this->result->addEMessage($this->containerApi->getErrorsStr());
                } else {
                    $this->result->setSuccessText(t('Изменения успешно сохранены'));
                    if (!$this->url->request('dialogMode', TYPE_INTEGER)) {
                        $this->result->setAjaxWindowRedirect( $this->url->getSavedUrl($this->controller_name.'index') );
                    }
                }
                return $this->result->getOutput();
            }
            
            if ($this->result->isSuccess()) {
                $this->successSave();
            }
        }
        
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->viewAsForm()->setTopTitle(t('Копировать контейнер'));
        $helper->setBottomToolbar($this->buttons(['save', 'cancel']));
        
        $pages = $this->api->setFilter('context', $context)->getAssocList('id');
        $containers = !$pages ? [] : $this->containerApi
                                               ->setFilter('page_id', array_keys($pages), 'in')
                                               ->queryObj()
                                               ->objects(null, 'page_id', true);
        
        $this->view->assign([
            'pages' => $pages,
            'containers' => $containers,
            'elements' => $helper
        ]);
        $helper['form'] = $this->view->fetch( 'copy_container.tpl' );
        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     * Добавление контейнера
     *
     * @param null|integer $primaryKeyValue
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionAddContainer($primaryKeyValue = null)
    {    
        $this->api = $this->containerApi;
        $helper    = $this->helperAdd();
        /**
         * @var \Templates\Model\Orm\SectionContainer $elem
         */
        $elem = $this->api->getElement();
        
        if (!$primaryKeyValue) {
            $elem->page_id = $this->url->get('page_id', TYPE_INTEGER, 0);
            $elem->type    = $this->url->get('type', TYPE_INTEGER, 0);
            $helper->setTopTitle(t('Добавить контейнер'));
        } else {
            $helper->setTopTitle(t('Редактировать контейнер'));
        }
                
        $grid_system = $this->pageApi->getPageGridSystem($elem->page_id);
        $helper->setFormSwitch($grid_system);        
        $elem->setColumnList($grid_system);

        $this->setHelper($helper);        
        return parent::actionAdd($primaryKeyValue);
    }

    /**
     * Редактирование контейнера
     *
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionEditContainer()
    {
        $id = $this->url->get('id', TYPE_STRING, 0);
        if ($id) $this->containerApi->getElement()->load($id);
        return $this->actionAddContainer($id);
    }

    /**
     * Удаление контейнера
     *
     * @return mixed
     */
    function actionRemoveContainer()
    {
        $this->api = $this->containerApi;
        return parent::actionDel();
    }

    /**
     * Удаление последнего контейнера
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionRemoveLastContainer()
    {
        $page_id = $this->url->get('page_id', TYPE_INTEGER, 0);
        $container = $this->containerApi->setFilter('page_id', $page_id)
                           ->setOrder('type DESC')
                           ->getFirst();
        
        if ($container['id']) {
            if ($container->delete()) {
                $this->result->setSuccess(true);
            } else {
                foreach($container->getErrors() as $error) {
                    $this->result->addEMessage($error);
                }
            }
        }
        return $this->result;
    }

    /**
     * Добавление секции
     *
     * @param null|integer $primaryKeyValue
     * @return bool|\RS\Controller\Result\Standard
     * @throws \RS\Db\Exception
     */
    function actionAddSection($primaryKeyValue = null)
    {
        $this->api = $this->sectionApi;
        /**
         * @var \Templates\Model\Orm\Section $elem
         */
        $elem   = $this->api->getElement();
        $helper = $this->helperAdd();
        $element_type = $this->url->get('element_type', TYPE_STRING, 'col');

        $section_id = $this->url->get('section_id', TYPE_INTEGER, 0);
        
        if (!$primaryKeyValue) {
            $parent_id  = $this->url->get('parent_id', TYPE_INTEGER, 0);
            $page_id    = $this->url->get('page_id', TYPE_INTEGER, 0);

            if (!$page_id && $parent_id){ //Если страница не указана, то определим её
                $parent  = new \Templates\Model\Orm\Section($parent_id);
                $page_id = $parent['page_id'];
            }

            if ($section_id){ //Если есть секция после которой нужно выставить эту
                $parent_section = new \Templates\Model\Orm\Section($section_id);
                $parent_id = $parent_section['parent_id'];
                $page_id   = $parent_section['page_id'];
            }


            $elem['page_id']      = $page_id;
            $elem['parent_id']    = $parent_id;
            $elem['element_type'] = $element_type;
            if ($element_type == 'row') {
                $helper->setTopTitle(t('Добавить строку'));
            } else {
                $helper->setTopTitle(t('Добавить секцию'));
            }
        } else {
            $helper->setTopTitle(t('Редактировать секцию'));
        }

        $elem['grid_system'] = $this->pageApi->getPageGridSystem($elem['page_id']); //Runtime информационное поле
        
        if ($elem['element_type'] == 'row') {
            $switch = 'row'.ucfirst($elem->grid_system);
        } else {
            //Определяем тип сеточного фреймворка, чтобы выставить параметры формы
            $switch = $elem->grid_system;
        }
        $elem->prepareFieldsForGridSystem($elem->grid_system);
        $helper->setFormSwitch($switch);
        
        //Устанавливаем максимальную ширину секции
        if ($elem['parent_id']<0) {
            $this->containerApi->setFilter('type', abs($elem['parent_id']));
            $this->containerApi->setFilter('page_id', $elem['page_id']);
            $container = $this->containerApi->getFirst();
        } else {
            /**
             * @var \Templates\Model\Orm\Section $parent_section
             */
            $parent_section = $this->sectionApi->getOneItem(abs($elem['parent_id']));
            $container = $parent_section->getContainer();
            if (!$container['id']) $container = false;
        }
        
        if ($container) {
            $pwidth = $this->api->getElement()->getProp('width');
            $pwidth->setListFromArray(array_combine(range(1, $container['columns']), range(1, $container['columns'])));
        }
                
        $this->setHelper($helper);
        $result = parent::actionAdd($primaryKeyValue);
        if ($this->url->isPost() && !$primaryKeyValue && $elem['id'] && $section_id){ //Если мы сохранили все удачно и нужно перестроить позицию относительно блока
            $position = $this->url->get('position', TYPE_STRING, "after");
            $elem->moveToPositionRelativeOfSection($section_id, $position);
        }
        return $result;
    }

    /**
     * Редактирование секции
     *
     * @return bool|\RS\Controller\Result\Standard
     * @throws \RS\Db\Exception
     */
    function actionEditSection()
    {
        $id = $this->url->get('id', TYPE_INTEGER);
        if ($id) $this->sectionApi->getElement()->load($id);
        return $this->actionAddSection($id);
    }

    /**
     * Удаление секции
     *
     * @return mixed
     */
    function actionDelSection()
    {
        $this->api = $this->sectionApi;
        return parent::actionDel();
    }

    /**
     * Перемещение блоков между собой
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Db\Exception
     */
    function actionAjaxMoveBlock()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->addEMessage($access_error);
        }

        $id            = $this->url->get('id', TYPE_INTEGER, 0); //id блока
        $position      = $this->url->get('position', TYPE_MIXED); //Позиция перемещения
        $new_parent_id = $this->url->get('parent_id', TYPE_INTEGER, null); //id нового родителя

        if (!$id){
            return $this->result->setSuccess(false)->addEMessage(t('Укажите id блока'));
        }

        /**
         * @var \Templates\Model\Orm\SectionModule $block
         */
        $block = $this->sectionModuleApi->getElement();
        $block->load($id);

        if ($position){ //Если позиция передана
            $block_id    = $this->url->get('block_id', TYPE_INTEGER, 0);
            $old_block   = new \Templates\Model\Orm\SectionModule($block_id);
            $move_result = $block->moveToPositionRelativeOfBlock($old_block['id'], $position);
        }else{
            $move_result = $block->moveToPosition($position, $new_parent_id);
        }

        $result = [
            'success' => $move_result
        ];

        if (!$result['success']) {
            return $this->result->setSuccess(false)->addSection('error', $this->sectionModuleApi->getElement()->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Перемещение секция между собой
     *
     * @return false|\RS\Controller\Result\Standard|string
     * @throws \RS\Db\Exception
     */
    function actionAjaxMoveSection()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->addEMessage($access_error);
        }        
        
        $id            = $this->url->get('id', TYPE_INTEGER, null);
        $position      = $this->url->get('position', TYPE_MIXED);
        $new_parent_id = $this->url->get('parent_id', TYPE_INTEGER, null);

        if (!$id){
            return $this->result->addEMessage(t('Укажите идентификатор секции'));
        }

        /**
         * @var \Templates\Model\Orm\Section $section
         */
        $section = $this->sectionApi->getElement();
        $section->load($id);
        if ($position){ //Если позиция передана
            $section_id  = $this->url->get('section_id', TYPE_INTEGER, 0);
            $old_section = new \Templates\Model\Orm\Section($section_id);
            $move_result = $section->moveToPositionRelativeOfSection($old_section['id'], $position);
        }else{
            $move_result = $section->moveToPosition($position, $new_parent_id);
        }

        $result = [
            'success' => $move_result
        ];
        
        if (!$result['success']) {
            $result['error'] = implode(',', $this->sectionApi->getElement()->getErrors());
        }
        return json_encode($result);
    }

    /**
     * Перемещение контейнеров между собой
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Db\Exception
     * @throws \RS\Orm\Exception
     */
    function actionAjaxMoveContainer()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->addEMessage($access_error);
        }        
        
        $source_id      = $this->url->get('source_id', TYPE_INTEGER, 0);
        $destination_id = $this->url->get('destination_id', TYPE_INTEGER, 0);
        /**
         * @var \Templates\Model\Orm\SectionContainer $container
         */
        $container = $this->containerApi->getElement();

        return $this->result
            ->setSuccess($container->load($source_id) && $container->changePosition($destination_id));
    }


    /**
     * Форма добавления модуля
     *
     * @return mixed
     * @throws \SmartyException
     */
    function actionAddModule()
    {
        $section_id = $this->url->get('section_id', TYPE_INTEGER, 0);
        $type       = $this->url->get('type', TYPE_INTEGER, 0);
        $page_id    = $this->url->get('page_id', TYPE_INTEGER, 0);
        $context    = $this->url->get('context', TYPE_STRING, "");

        $module_manager   = new \RS\Module\Manager();
        $controllers_tree = $module_manager->getBlockControllers();

        $this->view->assign([
            'controllers_tree' => $controllers_tree,
            'section_id' => $section_id,
            'type' => $type,
            'page_id' => $page_id,
            'context' => $context,
        ]);
        $this->result->setHtml($this->view->fetch('block_manager_add_module_form.tpl'));
        return $this->result->getOutput();
    }

    /**
     * Возвращает ошибки для второго шага добавления модуля
     *
     * @param null|integer $primaryKeyValue - идентификатор модуля
     * @param \RS\Orm\ControllerParamObject | false $object - объект параметров контроллера
     * @param \Templates\Model\Orm\SectionModule $sectionModule - объект модуля
     * @param integer $block_id - id блока
     * @return array
     * @throws \RS\Exception
     */
    private function getErrorForAddModuleStep2($primaryKeyValue, $object, $sectionModule, $block_id)
    {
        $errors = [];
        if ($this->url->isPost()) {
            if ($object->checkData()) {

                $sectionModule->setParams($object->getValues());

                //Параметры контроллера заданы корректно.
                if ($sectionModule->save($primaryKeyValue)) {

                    if ($block_id){ //Если есть блок относительно которого нужно добавить другой блок
                        $position = $this->request('position', TYPE_STRING, 'after');
                        $sectionModule->moveToPositionRelativeOfBlock($block_id, $position);
                    }

                    $this->result->setSuccess(true);
                } else {
                    $errors = $sectionModule->getErrors();
                    $this->result->setSuccess(false)->setErrors($sectionModule->getErrors());
                }
            } else {
                $errors = $object->getErrors();
                $this->result->setSuccess(false)->setErrors($object->getErrors());
            }
        }
        return $errors;
    }

    /**
     * Заполняет данными новый блок для второго шага добавления модуля
     *
     * @param \Templates\Model\Orm\SectionModule $sectionModule - объект модуля
     * @param integer $section_id - id секции куда надо добавить модуль
     * @param integer $block_id - блок относительного, которого нужно добавить модуль
     * @throws \RS\Controller\ParameterException
     */
    private function fillSectionModuleParamsForAddModuleStep2($sectionModule, $section_id, $block_id)
    {
        $block = $this->url->get('block', TYPE_STRING);

        $block_controller = \RS\Module\Item::getBlockControllerInstance($block);
        if (!$block_controller) {
            throw new \RS\Controller\ParameterException('block_controller, section');
        }
        $sectionModule['section_id']        = $section_id;
        $sectionModule['module_controller'] = $block;
        $sectionModule['public']            = 1;
        if ($block_id){ //Если относительно другого блока
            $relative_block = new \Templates\Model\Orm\SectionModule($block_id);
            $sectionModule['page_id'] = $relative_block['page_id'];
        }
    }

    /**
     * Второй шаг добавления модуля с сохранением параметррв
     *
     * @param null|integer $primaryKeyValue - идентификатор модуля
     * @return mixed|\RS\Controller\Result\Standard
     * @throws \RS\Controller\ParameterException
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    function actionAddModuleStep2($primaryKeyValue = null)
    {
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this, $this->sectionApi, $this->url);
        $helper->setBottomToolbar($this->buttons(['save', 'cancel']));
        $helper->setTemplate('%templates%/crud-block-form.tpl');

        $section_id = $this->url->get('section_id', TYPE_INTEGER, 0);

        $context = $this->url->get('context', TYPE_STRING, "");
        if (!empty($context) && $this->url->isPost()){ //Добавление блока без контейнера, его предварительное создание
            $type    = $this->url->get('type', TYPE_INTEGER, 0);
            $page_id = $this->url->get('page_id', TYPE_INTEGER, 0);
            $section_id = $this->pageApi->getSectionIdWithCreationContainerAndSections($page_id, $type, $context);
        }

        /**
        * @var \Templates\Model\Orm\SectionModule $sectionModule
        */
        $sectionModule = $this->sectionModuleApi->getElement();

        $block_id   = $this->request('block_id', TYPE_INTEGER, null);

        if (!$primaryKeyValue) {
            //Если добавляем новый блок
            $this->fillSectionModuleParamsForAddModuleStep2($sectionModule, $section_id, $block_id);
        }
        
        $controller = $sectionModule->getControllerInstance();

        $block_info = $controller->getInfo();
        $helper->setTopTitle(t('Настройки блока {title}'), ['title' => $block_info['title']]);
        $helper['block_controller'] = $sectionModule['module_controller'];
        
        $object = $controller->getParamObject();
        
        if ($object) {
            //Отображаем настройки модуля, если таковые имеются
            $object->getFromArray( $sectionModule->getParams() + $controller->getParam());
            $object->setLocalParameter('form_template', 'moduleblock_'.str_replace('\\', '_', $sectionModule['module_controller']));
            $helper['form'] = $object->getForm(null, null, false, null, null, $this->mod_tpl);
        } else {
            $helper['form'] = '';
            //Если у контроллера нет параметров, то сразу отдаем JSON
            if (!$primaryKeyValue) {
                if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
                    return $this->result
                            ->addSection('close_dialog', true)
                            ->addEMessage($access_error);
                }

                $sectionModule->insert();
                //Сбрасываем кэш
                \RS\Cache\Manager::obj()->invalidateByTags(CACHE_TAG_BLOCK_PARAM);
                return $this->result
                    ->addSection('close_dialog', true)
                    ->setNoAjaxRedirect($this->url->getSavedUrl($this->controller_name.'index'))
                    ->getOutput();
            }
        }

        $errors = $this->getErrorForAddModuleStep2($primaryKeyValue, $object, $sectionModule, $block_id);

        if ($this->url->isPost()){
            //Сбрасываем кэш
            \RS\Cache\Manager::obj()->invalidateByTags(CACHE_TAG_BLOCK_PARAM);
            if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                if ($this->result->isSuccess()) {
                    $this->result->setSuccessText(t('Изменения успешно сохранены'));
                }
                return $this->result->getOutput();
            }

            if ($this->result->isSuccess()) {
                $this->successSave();
            }
        }

        $this->view->assign([
            'elements' => $helper,
            'errors' => $errors
        ]);
        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     * Возвращает окно редактирования блочного контроллера
     *
     * @return mixed|\RS\Controller\Result\Standard
     * @throws \RS\Controller\ParameterException
     * @throws \RS\Db\Exception
     */
    function actionEditModule()
    {
        $id = $this->url->get('id', TYPE_INTEGER); //id блока
        $this->sectionModuleApi->getElement()->load($id);
        return $this->actionAddModuleStep2($id);
    }

    /**
     * Возращает окно для редактирования блочного контроллера, который был вставлен через moduleinsert в шаблон
     * Сохраняет настройки блока
     * сбрасывает кэш блока
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionEditTemplateModule()
    {
       $block_id  = $this->request('_block_id',TYPE_INTEGER);    //id блока в кэше
       $block_url = $this->request('block',TYPE_STRING); //короткое имя контроллера блока 

       $api = new \Templates\Model\TemplateModuleApi();
       /** @var \RS\Controller\StandartBlock $block */

//        var_dump($block_id);
//        var_dump($block_url);
       if ($block = $api->getBlockFromCache($block_id, $block_url)) { //Подгрузим блок

           $block_info = $block->getInfo();
           $helper     = new \RS\Controller\Admin\Helper\CrudCollection($block);
           $helper->setBottomToolbar($this->buttons(['save', 'cancel']));
           
           $helper->setTemplate($this->mod_tpl.'crud-block-form.tpl'); 
           $helper->setTopTitle(t('Настройки блока {title}'), ['title' => $block_info['title']]);
           
           $block_info['block_class']  = mb_strtolower(get_class($block));
           $helper['block_controller'] = $block_info['block_class'];
           
           /** @var \RS\Orm\ControllerParamObject $object */
           $object = $block->getParamObject(); //Получим объект с параметрами
           
           //Пытаемся получить окно для редактирования блока
           if ($object) { //Если получить объекть удалось
                //Отображаем настройки модуля, если таковые имеются
                $object->getFromArray($block->getParam());
                $object->setLocalParameter('form_template', 'moduleblock_'.str_replace('\\', '_', $block_info['block_class'])); // Установил параметр для генерирования шаблона
                $helper['form'] = $object->getForm(null, null, false, null, null, $this->mod_tpl);
           } else { //Если не извлеч параметры
                $helper['form'] = '';
                //Если не удалось получить объект, то сбросим кэш, т.к. он возможно устарел
                \RS\Cache\Manager::obj()->invalidateByTags(CACHE_TAG_BLOCK_PARAM);
                return $this->result->setSuccess(true)->setSuccessText(t('Попытка получить блок не удалась.<br/> Нужна перезагрука страницы'));
           }
           
           $errors = [];
           //Обработаем пост если он к нам пришёл
           if ($this->url->isPost()){
              if ($object->checkData()){ //Заполняем объект с проверкой

                 // Исправление ошибки при вставке } в текст HtmlBlock
                 if(!empty($object->getValues()['html'])) {
                   $object->offsetSet('html',str_replace('}','&#125;',$object->getValues()['html']));
                 }
                 //Сохранение
                 $api->saveBlockValues($block,$object);
                 
                 //Сбросим кэш
                 \RS\Cache\Manager::obj()->invalidateByTags(CACHE_TAG_BLOCK_PARAM); 
                 $cleaner = new \RS\Cache\Cleaner();
                 $cleaner->clean($cleaner::CACHE_TYPE_COMMON);
                 $cleaner->clean($cleaner::CACHE_TYPE_TPLCOMPILE);
                 return $this->result->setSuccess(true)->setSuccessText('Успешно сохранено');
              } 
              //Или возвратим ошибку
              $errors = $object->getErrors();  
           }

           if ($block->getParam(Block::BLOCK_LOADED_FROM_DB_PARAM)) {
               $helper->getBottomToolbar()->addItem(new ToolbarButton\Button($this->router->getAdminUrl('templateModuleDeleteBdData', [
                   'module_id' => $block->getParam(Block::BLOCK_LOADED_FROM_DB_PARAM),
               ], 'templates-blockctrl'), t('Сбросить параметры'), ['attr' => [
                   'class' => 'btn btn-warning crud-get crud-close-dialog'
               ]]));
           }


           $this->view->assign([
               'elements' => $helper,
               'errors' => $errors,
               'params_loaded_from_db' => $block->getParam(Block::BLOCK_LOADED_FROM_DB_PARAM)
           ]);
           
           return $this->result->setTemplate($helper['template']);
       }
       
       //Если не удалось получить объект, то сбросим кэш, т.к. он возможно устарел
       \RS\Cache\Manager::obj()->invalidateByTags(CACHE_TAG_BLOCK_PARAM); 
       
       //Перезагрузим страницу
       return $this->result->setSuccess(true)->setSuccessText(t('Попытка получить блок не удалась.<br/> Нужна перезагрука страницы'));
    }

    public function actionTemplateModuleDeleteBdData()
    {
        $module_id  = $this->request('module_id',TYPE_STRING);
        TemplateModuleApi::deleteSavedParamsByModule($module_id);
        return $this->result->setSuccess(true);
    }

    /**
     * Удаление модуля
     *
     * @return mixed
     */
    function actionDelModule()
    {
        $this->api = $this->sectionModuleApi;
        return parent::actionDel();
    }

    /**
     * Окно импорта структуры блоков из XML
     *
     * @return mixed|\RS\Controller\Result\Standard
     * @throws \SmartyException
     */
    function actionImport()
    {
        $context = $this->url->request('context', TYPE_STRING, 'theme');
        $helper  = parent::helperAdd();
        $helper['form'] = $this->view->fetch('form/import.tpl');
        $helper->setTopTitle(t('Импорт структуры блоков'));
        
        $helper['bottomToolbar']->addItem(new ToolbarButton\SaveForm(null, t('импортировать')), 'save');
        if ($this->url->isPost()) {
            $this->result->setSuccess( $this->api->importXML($this->url->files('file', TYPE_ARRAY), $context ) );
            $helper['formErrors'] = $this->api->getDisplayErrors();
            
            if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                if (!$this->result->isSuccess()) {
                    $this->result->setErrors( $this->api->getDisplayErrors() );
                } else {
                    $this->result->setSuccessText(t('Данные успешно импортированы'));
                    $this->result->setAjaxRedirect( $this->router->getAdminUrl(false, ['context' => $context]) );
                    if (!$this->url->request('dialogMode', TYPE_INTEGER)) {
                        $this->result->setAjaxWindowRedirect( $this->url->getSavedUrl($this->controller_name.'index') );
                    }
                }
                return $this->result->getOutput();
            }
        }
        
        $this->view->assign('elements', $helper);
        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     * Экспорт структуры блоков темы в XML
     *
     * @return mixed
     */
    function actionExport()
    {
        $context = $this->url->request('context', TYPE_STRING);
        $this->wrapOutput(false);
        $filename = 'blocks.xml';
        $this->app->headers->addHeaders([
            'Content-Type' => 'text/xml',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Connection' => 'close'
        ]);
        return $this->api->getBlocksXML($context);
    }

    /**
     * Сохранение структуры блоков темы в XML лежащий в теме оформления
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionSaveTheme()
    {
        $this->result
            ->setSuccess( $this->api->saveThemeBlocks() )
            ->addSection( 'noUpdate', true);
            
        if ($this->result->isSuccess()) {
            $this->result->addMessage(t('Структура блоков успешно сохранена в теме'));
        } else {
            $this->result->addEMessage( $this->api->getErrorsStr() );
        }
        return $this->result;
    }
    
    function helperContextOptions()
    {
        return parent::helperAdd();
    }
    
    function actionContextOptions()
    {
        $context = $this->url->request('context', TYPE_STRING);
        
        $helper = $this->getHelper();
        
        $theme = \RS\Theme\Item::makeByContext($context);
        $theme_info = $theme->getInfo();
        $options = $theme->getContextOptions();

        if ($this->url->isPost()) {            
            $options->replaceOn(true);
            $this->result->setSuccess($options->save());
            if ($this->result->isSuccess()) {
                $this->result->setSuccess(true)
                             ->setSuccessText(t('Изменения успешно сохранены'));
            } else {
                $this->result->setErrors($options->getDisplayErrors());
            }
            
            return $this->result;
        }        
        
        //Получаем динамический объект для генерации формы
        $form_object = $options->getContextFormObject();
        
        $helper['form'] = $form_object->getForm();
        $helper->setTopTitle(t('Настройка темы {title}'), ['title' => $theme_info['name']]);
        
        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     * Переключение публичности модуля
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionAjaxToggleViewModule()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->addEMessage($access_error);
        }        
                
        $block_id = $this->url->request('id', TYPE_INTEGER);
        $module = new \Templates\Model\Orm\SectionModule($block_id);

        if (!$module['id']) {
            $this->e404(t('Модуль не найден'));
        }
        $module['public'] = !$module['public'];
        $module->update();
        
        //Очищаем кэш, связанный с блоками
        \RS\Cache\Manager::obj()->invalidateByTags(CACHE_TAG_BLOCK_PARAM);
        
        return $this->result->setSuccess(true);
    }
}
