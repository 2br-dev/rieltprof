<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller\Admin;

use Main\Model\NoticeSystem\HasMeterInterface;
use RS\Application\Application;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Event\Manager as EventManager;
use RS\Html\Filter;
use RS\Html\Table\Control as TableControl;
use RS\Html\Toolbar;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Tree;
use RS\Module\AbstractModel\EntityList;
use RS\Module\AbstractModel\TreeList;
use RS\Orm\AbstractObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type as OrmType;
use RS\View\Engine as ViewEngine;

/**
 * Стандартный конроллер спискового компонента.
 * У которого есть табличная форма, форма создания, форма редактирования, форма мультиредактирования
 */
abstract class Crud extends Front
{
    private $helper;

    protected $allow_crud_actions;
    protected $sqlMultiUpdate = true; //Если True - то используется метод api->multiUpdate иначе открывается каждый coreobject и персонально обновляется во время группового редактирования.
    protected $selectAllVar = 'selectAll';
    protected $edit_url_var = 'edit_url';
    protected $sess_where = '_list'; //имя переменной в сесии с условием последней выборки должно быть следующим ИМЯ_КОНТРОЛЕРА.
    protected $multiedit_check_func; //callback, вызываемый для проверки данных при мультиредактировании
    protected $user_post_data = []; //Этот массив мержится с массивом POST
    /** @var EntityList */
    protected $api;
    /** @var TreeList */
    protected $tree_api;
    protected $tree_entity_type_accusative = '';
    /** @var EntityList */
    protected $category_api;
    protected $category_entity_type_accusative = '';

    public $edit_call_action = 'actionAdd';

    public function __construct(EntityList $api)
    {
        parent::__construct();
        $this->api = $api;
    }

    /**
     * Возвращает HTML для отображения ветвей дерева
     *
     * @return Standard
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    public function actionGetTreeChildsHtml()
    {
        $parent_ids = $this->url->request('ids', TYPE_ARRAY);
        $render_recursive = $this->url->request('recursive', TYPE_INTEGER);
        $render_opened = $this->url->request('render_opened', TYPE_INTEGER);
        $force_open = $this->url->request('force_open', TYPE_INTEGER);

        $html = [];
        if ($parent_ids) {
            $tree_api = $this->getTreeApi();

            if ($filter = $this->getIndexTreeFilterControl()) {
                $filter->fill();
                $tree_api->addFilterControl($filter);
            }

            foreach ($parent_ids as $parent_id) {
                $list = $tree_api->getTreeList($parent_id);
                $view = new ViewEngine();
                $view->assign([
                    'list' => $list,
                    'tree' => $this->getIndexTreeElement(),
                    'render_all_nodes' => $render_recursive,
                    'render_opened_nodes' => $render_opened,
                    'forced_open_nodes' => $force_open,
                ]);
                $html[$parent_id] = $view->fetch('system/admin/html_elements/tree/tree_branch.tpl');
            }
        }
        return $this->result->setSuccess(true)->addSection('child_html', $html);
    }

    /**
     * Возвращает список orm объектов, с которыми мложет работать данный контроллер, в виде [сокращённое_наименование => экземпляр_orm]
     *
     * @return AbstractObject[]
     */
    protected function getAllowableOrmClasses()
    {
        $allowable_classes = [];

        return $allowable_classes;
    }

    /**
     * Возвращает объект с настройками отображения дерева
     * Перегружается у наследника
     *
     * @return Tree\Element
     */
    protected function getIndexTreeElement()
    {
        return null;
    }

    /**
     * Возвращает объект с настройками фильтра дерева
     * Перегружается у наследника
     *
     * @return Filter\Control
     */
    protected function getIndexTreeFilterControl()
    {
        return null;
    }

    /**
     * Действие множественного редактирования элементов дерева
     *
     * @return Standard
     * @throws \SmartyException
     */
    function actionTreeMultiEdit()
    {
        $this->api = $this->getTreeApi();
        $this->multiedit_check_func = [$this->getTreeApi(), 'multiEditCheck'];
        $this->setHelper($this->helperMultiEdit());
        return self::actionMultiEdit();
    }

    /**
     * Действие редактирования элемента дерева
     *
     * @return mixed
     */
    public function actionTreeEdit()
    {
        $this->edit_call_action = 'actionTreeAdd';
        return self::actionEdit();
    }

    /**
     * Форма редактирования элемента дерева
     *
     * @return CrudCollection
     */
    public function helperTreeEdit()
    {
        return $this->helperTreeAdd();
    }

    /**
     * Действие клонирования элемента дерева
     *
     * @return Standard|bool
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    public function actionTreeClone()
    {
        $this->setHelper($this->helperTreeAdd());
        $id = $this->url->get('id', TYPE_INTEGER);

        $elem = $this->getTreeApi()->getElement();

        if ($elem->load($id)) {
            $clone = $elem->cloneSelf();
            $this->getTreeApi()->setElement($clone);
            $clone_id = $clone['id'];

            return $this->actionTreeAdd($clone_id);
        } else {
            return $this->e404();
        }
    }

    /**
     * Действие добавления элемента дерева
     *
     * @param int $primary_key_value - id объекта
     * @return \RS\Controller\Result\Standard|bool
     */
    public function actionTreeAdd($primary_key_value = null)
    {
        $helper = $this->getHelper();

        if ($primary_key_value === null) {
            $helper->setTopTitle(t('Добавить') . " {$this->tree_entity_type_accusative}");
        } else {
            $helper->setTopTitle(t('Редактировать') . " {$this->tree_entity_type_accusative} {".$this->getTreeApi()->getNameField()."}");
        }

        return self::actionAdd($primary_key_value);
    }

    /**
     * Форма добавления элемента дерева
     *
     * @return CrudCollection
     */
    public function helperTreeAdd()
    {
        $this->api = $this->getTreeApi();
        return $this->helperAdd();
    }

    /**
     * Действие перемещения элемента дерева
     *
     * @return mixed
     * @throws \RS\Db\Exception
     */
    public function actionTreeMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $parent = $this->url->request('parent', TYPE_INTEGER);
        $flag = $this->url->request('flag', TYPE_STRING); //Указывает выше или ниже элемента to находится элемент from

        if ($this->getTreeApi()->moveElement($from, $to, $flag, null, $parent)) {
            $this->result->setSuccess(true);
        } else {
            $this->result->setSuccess(false)->setErrors($this->getTreeApi()->getErrors());
        }
        return $this->result->getOutput();
    }

    /**
     * Действие удаления элемента дерева
     *
     * @return mixed
     */
    public function actionTreeDel()
    {
        $this->api = $this->getTreeApi();
        return self::actionDel();
    }

    /**
     * Действие множественного редактирования элементов списка категорий
     *
     * @return Standard
     * @throws \SmartyException
     */
    public function actionCategoryMultiEdit()
    {
        $this->api = $this->getCategoryApi();
        $this->setHelper($this->helperMultiEdit());
        return self::actionMultiEdit();
    }

    /**
     * Действие редактирования элемента списка категорий
     *
     * @return mixed
     */
    public function actionCategoryEdit()
    {
        $this->edit_call_action = 'actionCategoryAdd';
        return self::actionEdit();
    }

    /**
     * Форма редактирования элемента списка категорий
     *
     * @return CrudCollection
     */
    public function helperCategoryEdit()
    {
        return $this->helperCategoryAdd();
    }

    /**
     * Действие клонирования элемента списка категорий
     *
     * @return Standard|bool
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    public function actionCategoryClone()
    {
        $this->setHelper($this->helperCategoryAdd());
        $id = $this->url->get('id', TYPE_INTEGER);
        $elem = $this->getCategoryApi()->getElement();

        if ($elem->load($id)) {
            $clone = $elem->cloneSelf();
            $this->getCategoryApi()->setElement($clone);
            $clone_id = $clone['id'];

            return $this->actionCategoryAdd($clone_id);
        } else {
            return $this->e404();
        }
    }

    /**
     * Действие добавления элемента списка категорий
     *
     * @param int $primaryKey - id объекта
     * @return bool|Standard
     */
    function actionCategoryAdd($primaryKey = null)
    {
        $helper = $this->getHelper();

        if ($primaryKey === null) {
            $helper->setTopTitle(t('Добавить') . " {$this->category_entity_type_accusative}");
        } else {
            $helper->setTopTitle(t('Редактировать') . " {$this->category_entity_type_accusative} {title}");
        }

        return self::actionAdd($primaryKey);
    }

    /**
     * Форма добавления элесента списка категорий
     *
     * @return mixed
     */
    public function helperCategoryAdd()
    {
        $this->api = $this->getCategoryApi();
        return self::helperAdd();
    }

    /**
     * Действие перемещения элемента списка категорий
     *
     * @return mixed
     */
    public function actionCategoryMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $flag = $this->url->request('flag', TYPE_STRING); //Указывает выше или ниже элемента to находится элемент from

        if ($this->getCategoryApi()->moveElement($from, $to, $flag, null)) {
            $this->result->setSuccess(true);
        } else {
            $this->result->setSuccess(false)->setErrors($this->getCategoryApi()->getErrors());
        }
        return $this->result->getOutput();
    }

    /**
     * Действие удаления элемента дерева
     *
     * @return mixed
     */
    public function actionCategoryDel()
    {
        $this->api = $this->getCategoryApi();
        return self::actionDel();
    }

    /**
     * Отображение списка
     */
    public function actionIndex()
    {
        $helper = $this->getHelper();
        $this->view->assign('elements', $helper->active());
        $this->url->saveUrl($this->controller_name . 'index');
        $this->api->saveRequest($this->controller_name . '_list');
        return $this->result->setHtml($this->view->fetch($helper['template']))->getOutput();
    }

    /**
     * Вызывается перед действием Index и возвращает коллекцию элементов,
     * которые будут находиться на экране.
     */
    protected function helperIndex()
    {
        return new Helper\CrudCollection($this, $this->api, $this->url, [
            'paginator',
            'topToolbar' => $this->buttons(['add']),
            'bottomToolbar' => $this->buttons(['delete']),
            'viewAs' => 'table'
        ]);
    }

    /**
     * Форма добавления элемента
     *
     * @param mixed $primaryKeyValue - id редактируемой записи
     * @param boolean $returnOnSuccess - Если true, то будет возвращать === true при успешном сохранении, иначе будет вызов стандартного _successSave метода
     * @param CrudCollection $helper - текуй хелпер
     * @return \RS\Controller\Result\Standard|bool
     */
    public function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        if ($primaryKeyValue < 0 || $primaryKeyValue === 0) $primaryKeyValue = null;
        $orm_object = $this->api->getElement();

        if ($helper === null) {
            $helper = $this->getHelper();
        }

        if ($primaryKeyValue === null) {
            $orm_object->fillDefaults();
        }
        //Если пост идет для текущего модуля
        if ($this->url->isPost()) {
            $this->result->setSuccess($this->api->save($primaryKeyValue, $this->user_post_data));

            if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                if (!$this->result->isSuccess()) {
                    $this->result->setErrors($orm_object->getDisplayErrors());
                } else {
                    $this->result->setSuccessText(t('Изменения успешно сохранены'));
                    if ($primaryKeyValue === null && !$this->url->request('dialogMode', TYPE_INTEGER)) {
                        $this->result->setAjaxWindowRedirect($this->url->getSavedUrl($this->controller_name . 'index'));
                    }
                }
                if ($returnOnSuccess) {
                    return true;
                } else {
                    return $this->result;
                }
            }

            if ($this->result->isSuccess()) {
                if ($returnOnSuccess) return true;
                else $this->successSave();
            } else {
                $helper['formErrors'] = $orm_object->getDisplayErrors();
            }
        }

        $this->view->assign([
            'elements' => $helper->active(),
        ]);
        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Подготавливает Helper объекта для добавления
     *
     * @return Helper\CrudCollection
     */
    protected function helperAdd()
    {
        return new Helper\CrudCollection($this, $this->api, $this->url, [
            'bottomToolbar' => $this->buttons(['save', 'cancel']),
            'viewAs' => 'form',
            'formTitle' => t('Добавить')
        ]);
    }

    /**
     * Редактирование элемента
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = $this->url->get('id', TYPE_STRING, 0);
        if ($id) {
            //Загружаем объект с учетом фильтров, установленных для списков
            $query = $this->api->queryObj();
            if (!empty($query->where)) {
                $query->where = "({$query->where})";
            }
            $this->api->setFilter($this->api->getIdField(), $id);
            if ($element = $this->api->getFirst()) {
                $this->api->getElement()->getFromArray($element->getValues(), null, false, true);
            } else {
                $this->e404();
            }
        }

        //Установим временно необходимый мультисайт в качестве активного
        //Это позволит редактировать объекты на других (не текущих) мультисайтах без смены текущего сайта
        $this->setSiteIdByOrmObject($this->api);

        //Отмечаем объект просмотренным
        //Передаем в JS сведения с новыми счетчиками
        if ($this->api instanceof HasMeterInterface) {
            $meter_api = $this->api->getMeterApi();
            $new_counter = $meter_api->markAsViewed($id);
            $this->result->addSection([
                'meters' => [
                    $meter_api->getMeterId() => $new_counter
                ],
                'markViewed' => [
                    $meter_api->getMeterId() => $id
                ]
            ]);
        }

        $result = $this->{$this->edit_call_action}($id);

        $this->restoreSiteId();

        return $result;
    }

    /**
     * Подготавливает Helper объекта для редактирования
     *
     * @return Helper\CrudCollection
     */
    protected function helperEdit()
    {
        return $this->helperAdd()
            ->setBottomToolbar($this->buttons(['saveapply', 'cancel']))
            ->setTopTitle(t('Редактировать'));
    }

    /**
     * Сортировка в списке
     *
     * @return mixed
     */
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $flag = $this->url->request('flag', TYPE_STRING); //Указывает выше или ниже элемента to находится элемент from

        if ($this->api->moveElement($from, $to, $flag)) {
            $this->result->setSuccess(true);
        } else {
            $this->result->setSuccess(true)->setErrors($this->api->getErrors());
        }

        return $this->result->getOutput();
    }

    /**
     * Удаляет записи
     *
     * @return mixed
     */
    public function actionDel()
    {
        $ids = $this->modifySelectAll($this->url->request('chk', TYPE_ARRAY, [], false));
        $id = $this->url->get('id', TYPE_STRING, false);

        if (empty($ids) && !empty($id)) {
            $ids = (array)$id;
        }
        $result = $this->api->multiDelete($ids);

        if ($result && $this->api instanceof HasMeterInterface) {
            //Передаем в JS сведения с новыми счетчиками
            /** @var HasMeterInterface $api */
            $api = $this->api;
            $meter_api = $api->getMeterApi();
            $new_counter = $meter_api->removeViewedFlag($ids);

            $this->result->addSection([
                'meters' => [
                    $meter_api->getMeterId() => $new_counter
                ]
            ]);
        }

        //Если передан параметр redirect, то перенаправляем пользователя
        if ($this->url->isAjax()) {
            $return = $this->result->setSuccess($result);

            if (!$result) {
                foreach ($this->api->getElement()->getErrors() as $error) {
                    $return->addEMessage($error);
                }
                foreach ($this->api->getErrors() as $error) {
                    $return->addEMessage($error);
                }
            }
            return $return->getOutput();
        } else {
            $this->redirectToIndex();
        }
        return null;
    }

    function redirectToIndex()
    {
        $redirect_url = urldecode($this->url->request('redirect', TYPE_STRING));

        if (!empty($redirect_url)) {
            if (preg_match('/^saved:(.*)$/u', $redirect_url, $match)) {
                $redirect_url = $this->url->getSavedUrl($this->controller_name . $match[1], '?');
            }
        } else {
            $redirect_url = $this->url->getSavedUrl($this->controller_name . 'index');
        }

        if (!empty($redirect_url)) Application::getInstance()->redirect($redirect_url);
    }

    /**
     * Успешное сохранение объекта и редирект
     */
    function successSave()
    {
        Application::getInstance()->redirect($this->url->getSavedUrl($this->controller_name . 'index'));
    }

    /**
     * Возвращает диалог настройки таблицы
     *
     * @return mixed
     * @throws \Exception
     * @throws \SmartyException
     */
    function actionTableOptions()
    {
        $helper = $this->getHelper();
        $this->view->assign('elements', $helper);
        $helper['form'] = $this->view->fetch('%system%/admin/tableoptions.tpl');
        return $this->result->setHtml($this->view->fetch($helper['template']))->getOutput();
    }

    /**
     * Подготавливает Helper для опций таблицы
     *
     * @return Helper\CrudCollection
     * @throws \RS\Event\Exception
     */
    function helperTableOptions()
    {
        $helper = new CrudCollection($this, $this->api, $this->url);
        $helper->setBottomToolbar(new Toolbar\Element([
                'Items' => [
                    'save' => new ToolbarButton\Button(null, t('сохранить'), ['attr' => [
                        'class' => 'btn-success saveToCookie'
                    ]]),
                    'cancel' => $this->buttons('cancel'),
                    'reset' => new ToolbarButton\Button(null, t('Сброс'), ['attr' => [
                        'class' => 'btn-danger reset'
                    ]])
                ]])
        )->setTopTitle(t('Настройка таблицы'));

        $index_helper = $this->helperIndex(); //Получаем структуру таблицы из helper'а

        $event_name = 'controller.exec.' . $this->getUrlName() . '.index'; //Формируем имя события
        $index_helper = EventManager::fire($event_name, $index_helper)->getResult();

        if (isset($index_helper['table'])) {
            /** @var TableControl $table */
            $table = $index_helper['table'];
            $table->fill();
            $helper['tableOptionControl'] = $table;
        }

        $helper->viewAsForm();
        return $helper;
    }

    /**
     * Групповое редактирование элементов
     *
     * @return \RS\Controller\Result\Standard
     * @throws \Exception
     * @throws \SmartyException
     */
    function actionMultiEdit()
    {
        $ids = $this->modifySelectAll($this->url->request('chk', TYPE_ARRAY, []));

        if (count($ids) == 1) { //Перекидываем на обычное редактирование, если выбран один элемент
            $edit_url = $this->url->request($this->edit_url_var, TYPE_STRING, $this->router->getAdminUrl('edit', ['id' => reset($ids)]));
            Application::getInstance()->redirect(str_replace('%ID%', reset($ids), $edit_url));
        }

        $doedit = $this->url->request('doedit', TYPE_ARRAY, []);
        $this->param['name'] .= 'multi';

        if ($this->url->isPost() && !empty($ids)) {

            $obj = $this->api->getElement();
            $allow_keys = $obj->getProperties()->getMultieditKeys();
            $post = array_intersect_key($_POST, $allow_keys);

            //Устанавливаем checkBox'ы
            foreach ($allow_keys as $key => $val) {
                if (isset($obj['__' . $key])) {
                    $property = $obj->getProp($key);

                    if (count($property->getCheckboxParam())) {
                        $post[$key] = isset($post[$key]) ? $property->getCheckboxParam('on') : $property->getCheckboxParam('off');
                    }
                    if ($property instanceof OrmType\ArrayList && !isset($post[$key])) $post[$key] = [];
                }
            }

            $post = array_intersect_key($post, array_flip($doedit));
            $this->result->setSuccess(empty($post));

            $element_class = $this->api->getElementClass();
            /** @var AbstractObject $prototype */
            $prototype = new $element_class();

            //Экранируем необходимые значения
            foreach ($post as $key => $value) {
                if (isset($prototype['__' . $key])) {
                    $post[$key] = $prototype['__' . $key]->escape($value);
                }
            }

            if (!empty($post)) {
                $obj->setCheckFields($doedit);
                if ($obj->checkData($post, [], [], $doedit)
                    //Проводим дополнительую проверку, если установлено свойство multiedit_check_func
                    && (!isset($this->multiedit_check_func) || call_user_func($this->multiedit_check_func, $obj, $post, $ids))
                ) {

                    if ($this->sqlMultiUpdate) {
                        $this->api->clearFilter();
                        $this->api->setFilter($this->api->getIdField(), $ids, 'in');
                        $this->api->multiUpdate($post, $ids);
                    } else {
                        foreach ($ids as $id) {
                            $prototype->load($id);
                            $prototype->setCheckFields($doedit);
                            $prototype->save($id, $post, [], []);
                            if ($prototype->hasError()) {
                                $prototype->addError(t("Во время обработки элемента %0 произошла ошибка", [$id]));
                                $error = $prototype->getErrors();
                                break;
                            }
                        }
                    }
                    $this->result->setSuccess(true);
                } else {
                    $error = $obj->getErrors();
                    $this->result->setSuccess(false)->setErrors($obj->getDisplayErrors());
                }
            }

            if ($this->url->isAjax()) {
                return $this->result->getOutput();
            } else {
                if ($this->result->isSuccess()) {
                    $this->successSave();
                }
            }

        } //POST

        $hidden_fields = [];
        if ($this->url->request($this->selectAllVar, TYPE_STRING) == 'on') {
            $hidden_fields[$this->selectAllVar] = 'on';
        } else {
            foreach ($ids as $key => $id)
                $hidden_fields["chk[$key]"] = $id;
        }

        $this->app->addJs('jquery.rs.ormobject.js', 'jquery.rs.ormobject.js');


        $helper = $this->getHelper();
        $helper['hiddenFields'] = $hidden_fields;

        $this->view->assign([
            'elements' => $helper,
            'errors' => isset($error) ? $error : [],
            'param' => [
                'doedit' => $doedit,
                'ids' => $ids,
                'sel_count' => count($ids)
            ]
        ]);

        $this->result->setHtml($this->view->fetch($helper['template']));
        return $this->result->getOutput();
    }

    function helperMultiEdit()
    {
        return new Helper\CrudCollection($this, $this->api, $this->url, [
            'topTitle' => t('Редактировать'),
            'bottomToolbar' => $this->buttons(['save', 'cancel']),
            'template' => '%system%/admin/crud_form.tpl',
            'multieditMode' => true
        ]);
    }

    /**
     * Если был выделен checkbox "Выделить все на всех страницах", то добываем все id, которые были на странице, иначе возвращаем, входящий параметр
     *
     * @param array $ids
     * @return array
     */
    function modifySelectAll($ids)
    {
        $request_object = $this->api->getSavedRequest($this->controller_name . '_list');
        if ($this->url->request($this->selectAllVar, TYPE_STRING) == 'on' && $request_object !== null) {
            return $this->api->getIdsByRequest($request_object);
        }
        return $ids;
    }

    /**
     * Возвращает массив для элемента html/toolbar со стандартными кнопками и установленными для контроллеров crud параметрами
     *
     * @param array|string $buttons - имя кнопок, которые должны присутствовать: add,delete,multiedit,save,cancel
     * @param array $buttons_text - массив с текстами для кнопок. например: 'add' => 'Добавить .....'
     * @param bool $ajax - Если true, то кнопкам будут спецпараметры для работы в ajax режиме
     * @return Toolbar\Element
     */
    function buttons($buttons, $buttons_text = null, $ajax = true)
    {
        $default_buttons = [
            'add' => new ToolbarButton\Add($this->url->replaceKey([$this->action_var => 'add']), null, ['noajax' => !$ajax]),
            'delete' => new ToolbarButton\Delete(null, null, ['attr' =>
                ['data-url' => $this->router->getAdminUrl('del')],
                'noajax' => !$ajax
            ]),
            'multiedit' => new ToolbarButton\Multiedit($this->router->getAdminUrl('multiedit'), null, ['noajax' => !$ajax]),
            'save' => new ToolbarButton\SaveForm(null, null, ['noajax' => !$ajax]),
            'saveapply' => new ToolbarButton\SaveForm(null, null, ['noajax' => !$ajax], true),

            'apply' => new ToolbarButton\ApplyForm(null, null, ['noajax' => !$ajax]),
            'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name . 'index'), null, ['noajax' => !$ajax]),
            'moduleconfig' => new ToolbarButton\ModuleConfig($this->router->getAdminUrl('edit', ['mod' => $this->mod_name], 'modcontrol-control'))
        ];

        if (is_array($buttons_text)) {
            foreach ($buttons_text as $key => $title) {
                $default_buttons[$key]->setTitle($title);
            }
        }

        if (is_array($buttons)) {
            $options = [];
            foreach ($buttons as $button) {
                if (isset($default_buttons[$button])) {
                    $options['Items'][$button] = $default_buttons[$button];
                }
            }
            return new Toolbar\Element($options);
        } else {
            return $default_buttons[$buttons];
        }
    }

    /**
     * Устанавливает произвольный helper, который потом может использоваться в Action
     * @param Helper\CrudCollection $helper - объект crud coolection
     * @return Helper\CrudCollection
     */
    function setHelper($helper)
    {
        return $this->helper = $helper;
    }

    /**
     * Возвращает установленный helper
     * @return Helper\CrudCollection
     */
    function getHelper()
    {
        return $this->helper;
    }

    /**
     * Устанавливает какие действия могут быть запущены именно из данного класса.
     *
     * @param string|array $actions , $actions, ....
     * @return void
     */
    function setCrudActions($actions = null)
    {
        $this->allow_crud_actions = is_array($actions) ? $actions : func_get_args();
    }

    /**
     * Выполняет action(действие) текущего контроллера, возвращает результат действия
     *
     * @param boolean $returnAsIs - возвращать как есть. Если true, то метод будет возвращать точно то,
     * что вернет действие, иначе результат будет обработан методом processResult
     *
     * @return mixed
     * @throws \RS\Controller\Exception
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    function exec($returnAsIs = false)
    {
        $act = $this->getAction();

        if (!empty($this->allow_crud_actions)) {
            if (!in_array($act, $this->allow_crud_actions) && is_callable([__CLASS__, 'action' . $act])) {
                $this->e404(t('Указанного действия не существует'));
            }
        }

        $helper_method = 'helper' . $act;
        if (is_callable([$this, $helper_method])) {
            $helper_result = $this->$helper_method(); //Вызываем метод, который должен сформировать helper

            if ($helper_result !== null) {
                $event_name = 'controller.exec.' . $this->getUrlName() . '.' . $act; //Формируем имя события

                /**
                 * Event: controller.exec.Короткое имя контроллера.Имя действия
                 * Вызывается перед рендерингом страницы. Обработчики данного события могут изменить содержимое helper'а
                 * paramtype mixed - helper
                 */
                $helper_result = EventManager::fire($event_name, $helper_result)->getResult();
                $this->setHelper($helper_result); //Сохраяем helper
            }
        }

        return parent::exec($returnAsIs);
    }

    /**
     * Метод для клонирования
     *
     * @return bool|\RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionClone()
    {
        $this->setHelper($this->helperAdd());
        $id = $this->url->get('id', TYPE_INTEGER);

        $elem = $this->api->getElement();

        if ($elem->load($id)) {
            $clone = $elem->cloneSelf();
            $this->api->setElement($clone);
            $clone_id = (int)$clone['id']; //ID = 0, а не null

            return $this->actionAdd($clone_id);
        } else {
            return $this->e404();
        }
    }

    /**
     * Метод обеспечивает отметку о прочтении одного объекта,
     * если API объекта это поддерживает
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionMarkOneAsViewed()
    {
        if (!($this->api instanceof HasMeterInterface)) {
            $this->e404();
        }

        $id = $this->url->request('id', TYPE_STRING);
        $meter_api = $this->api->getMeterApi();
        $new_counter = $meter_api->markAsViewed($id);

        return $this->result->setSuccess(true)
            //Сообщим новое значение счетчика в JS
            ->addSection('meters', [
                $meter_api->getMeterId() => $new_counter
            ])
            ->addSection('markViewed', [
                $meter_api->getMeterId() => $id
            ]);
    }

    /**
     * Метод обеспечивает отметку о прочтении всех объектов,
     * если API объекта это поддерживает
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionMarkAllAsViewed()
    {
        if (!($this->api instanceof HasMeterInterface)) {
            $this->e404();
        }

        $meter_api = $this->api->getMeterApi();
        $new_counter = $meter_api->markAllAsViewed();

        return $this->result->setSuccess(true)
            //Сообщим новое значение счетчика в JS
            ->addSection('meters', [
                $meter_api->getMeterId() => $new_counter
            ])
            ->addSection('markViewed', [
                $meter_api->getMeterId() => 'all'
            ]);
    }

    /**
     * Возвращает api древовидного списка
     *
     * @return TreeList
     */
    public function getTreeApi()
    {
        return $this->tree_api;
    }

    /**
     * Устанавливает api древовидного списка
     *
     * @param TreeList $tree_api - api древовидного списка
     * @param string $tree_entity_type_accusative - тип сущности в винительном падеже
     * @return void
     */
    protected function setTreeApi(TreeList $tree_api, $tree_entity_type_accusative = '')
    {
        $this->tree_api = $tree_api;
        $this->tree_entity_type_accusative = $tree_entity_type_accusative;
    }

    /**
     * Возвращает api списка категорий
     *
     * @return EntityList
     */
    protected function getCategoryApi()
    {
        return $this->category_api;
    }

    /**
     * Устанавливает api списка категорий
     *
     * @param EntityList $category_api - api списка категорий
     * @param string $category_entity_type_accusative - тип сущности в винительном падеже
     * @return void
     */
    protected function setCategoryApi(EntityList $category_api, $category_entity_type_accusative = '')
    {
        $this->category_api = $category_api;
        $this->category_entity_type_accusative = $category_entity_type_accusative;
    }

    /**
     * Возвращает основное api
     *
     * @return EntityList
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Устанавливает основное api
     *
     * @param EntityList $api - объект api
     * @return void
     */
    protected function setApi(EntityList $api)
    {
        $this->api = $api;
    }
}
