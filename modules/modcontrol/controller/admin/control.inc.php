<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace ModControl\Controller\Admin;

use Main\Model\ModuleLicenseApi;
use ModControl\Model\ModuleApi;
use ModControl\Model\SearchOptionsApi;
use RS\Application\Application;
use RS\Controller\Admin\Front;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\ExceptionPageNotFound;
use RS\Controller\Result\Standard;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Html\Table\Control as TableControl;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Filter;
use RS\Html\Toolbar;
use RS\Html\Table;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use RS\Module\Exception as ModuleException;
use RS\Module\Installer;
use RS\Module\Item as ModuleItem;

class Control extends Front
{
    /** @var ModuleApi */
    protected $api;
    protected $action_var = 'do';
    protected $form_tpl = 'forms/%MODULE%_form.tpl';
    protected $modules;

    function __construct()
    {
        parent::__construct();
        $this->api = new ModuleApi();

        $this->app->addCss($this->mod_css . 'mcontrol.css', 'mcontrol', BP_ROOT);
    }

    /**
     * Отображение списка
     *
     * @return Standard
     * @throws EventException
     * @throws \SmartyException
     */
    public function actionIndex()
    {
        $helper = $this->helperIndex();

        $event_name = 'controller.exec.' . $this->getUrlName() . '.index'; //Формируем имя события
        $helper = EventManager::fire($event_name, $helper)->getResult();

        $helper->setTopTitle(t('Настройка модулей'));
        $this->view->assign('elements', $helper->active());
        $this->url->saveUrl($this->controller_name . 'index');
        return $this->result->setHtml($this->view->fetch($helper['template']))->getOutput();
    }

    function helperIndex()
    {

        $helper = new CrudCollection($this, $this->api, $this->url);
        $helper->viewAsTable();
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('class', [
                    'cellAttrParam' => 'checkbox_attribute'
                ]),
                new TableType\Text('name', t('Название'), [
                    'href' => $this->router->getAdminPattern('edit', [':mod' => '@class']),
                    'Sortable' => SORTABLE_BOTH
                ]),
                new TableType\Usertpl('description', t('Описание'), '%modcontrol%/col_description.tpl'),
                new TableType\Text('version', t('Версия'), ['TdAttr' => ['class' => 'cell-small'], 'Sortable' => SORTABLE_BOTH]),
                new TableType\Usertpl('enabled', t('Включен'), '%modcontrol%/col_enabled.tpl', ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('class', t('Идентификатор'), ['ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_ASC]),
                new TableType\Actions('class', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':mod' => '~field~']), null, ['noajax' => true]),
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')])
            ],
            'rowAttrParam' => 'row_attributes'
        ]));

        $helper->setFilter(new Filter\Control([
            'container' => new Filter\Container([
                'lines' => [
                    new Filter\Line(['items' => [
                        new Filter\Type\Text('name', t('Название')),
                        new Filter\Type\Text('class', t('Идентификатор')),
                    ]])
                ]
            ]),
            'caption' => t('Поиск по модулям')
        ]));

        $helper->setFilterContent($this->view->fetch('filter_by_options.tpl'));

        $helper->setListFunction('tableData');

        // Если не установлен запрет на установку модулей
        $items = [];
        if (!defined('CANT_UPLOAD_MODULE')) {
            $items[] = new ToolbarButton\Add($this->url->replaceKey([$this->action_var => 'add']), t('добавить модуль'), ['noajax' => false]);
        }
        $items[] = new ToolbarButton\Button($this->router->getAdminUrl('AjaxReloadLicenseData', [], 'main-modulelicensescontrol'), '<i class="zmdi zmdi-refresh f-18"></i><span class="visible-xs-inline">&nbsp;'.t('Обновить лицензии модулей').'</span>', [
                'attr' => [
                    'class' => 'crud-get btn-default',
                    'title' => t('Обновить лицензии модулей')
                ],
            ]);

        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => $items
        ]));

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Delete(null, null, [
                    'attr' => ['data-url' => $this->router->getAdminUrl('del')]
                ]),
            ]
        ]));

        return $helper;
    }

    /**
     * Окно редактирования модуля
     * Сохраняет настройки модуля
     *
     * @return Standard
     * @throws EventException
     * @throws RSException
     * @throws ExceptionPageNotFound
     * @throws \SmartyException
     */
    function actionEdit()
    {
        $this->app->addJs('%modcontrol%/module_view.js');

        $helper = new CrudCollection($this);
        $helper->setTemplate($this->mod_tpl . 'crud_module.tpl');

        $modname = $this->url->request('mod', TYPE_STRING);

        $mod = new ModuleItem($modname);
        $config_obj = $mod->getConfig();

        if (!$config_obj) $this->e404(t('Такого модуля не существует'));
        $helper->setTopTitle(t('Настройка модуля') . ' {name}', $config_obj);

        //Если пост идет для текущего модуля
        if ($this->url->isPost()) {
            $this->result->setSuccess($config_obj->save(1));

            if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                if (!$this->result->isSuccess()) {
                    $this->result->setErrors($config_obj->getDisplayErrors());
                } else {
                    $this->result->setSuccessText(t('Изменения успешно сохранены'));
                }
                return $this->result->getOutput();
            }

            if ($this->result->isSuccess()) {
                $this->successSave();
            } else {
                $error = $config_obj->getErrors();
            }
        }

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\SaveForm(null, null, null, true),
                new ToolbarButton\Cancel($this->url->request('referer', TYPE_STRING, $this->url->getSavedUrl($this->controller_name . 'index')))
            ]
        ]));

        $helper['form'] = $config_obj->getForm(null, null, false, null, '%system%/coreobject/config_form.tpl', $this->mod_tpl);

        $this->view->assign([
            'controller_list' => $mod->getBlockControllers(),
            'module_item' => $mod,
            'elements' => $helper,
            'errors' => isset($error) ? $error : [],
            'module_license_api' => new ModuleLicenseApi(),
        ]);

        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Добавляем модуль
     *
     * @return Standard
     * @throws RSException
     * @throws \SmartyException
     */
    function actionAdd()
    {
        // Если установлен запрет на установку модулей
        if (defined('CANT_UPLOAD_MODULE')) {
            return null;
        }

        $mod_install = Installer::getInstance();
        $helper = new CrudCollection($this);

        //Если пост идет для текущего модуля
        if ($this->url->isPost()) {
            $file = $this->url->files('module');
            $this->result->setSuccess($mod_install->extractFromPost($file));

            if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                if (!$this->result->isSuccess()) {
                    $this->result->setErrors($mod_install->getDisplayErrors());

                } else {
                    $this->result->setAjaxWindowRedirect($this->router->getAdminUrl('addStep2'));
                }
                return $this->result->getOutput();
            }

            if ($this->result->isSuccess()) {
                Application::getInstance()->redirect($this->router->getAdminUrl('addStep2'));
            } else {
                $helper['formErrors'] = $mod_install->getDisplayErrors();
            }
        }

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\SaveForm(null, t('Далее')),
                new ToolbarButton\Cancel($this->router->getAdminUrl(false))
            ]
        ]));

        $helper->setTopTitle(t('Установка модуля'));
        $helper->viewAsForm();

        $this->view->assign([
            'is_empty_tmp' => $mod_install->isEmptyTmp(),
            'elements' => $helper
        ]);

        $helper['form'] = $this->view->fetch('add.tpl');
        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Информация о распакованом модуле
     *
     * @return Standard
     * @throws ModuleException
     * @throws \SmartyException
     */
    function actionAddStep2()
    {
        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Параметры установки модуля'));
        $helper->viewAsForm();

        $mod_install = Installer::getInstance();

        if ($this->url->isPost()) {
            $mod_install->setOption('insertDemoData', $this->url->request('insertDemoData', TYPE_BOOLEAN, false));

            if ($mod_install->installFromTmp()) {
                $_SESSION['INSTALLED_MODULE'] = $mod_install->getModName();
                return $this->result->setAjaxWindowRedirect($this->router->getAdminUrl('addSuccess'));
            } else {
                return $this->result->setErrors($mod_install->getDisplayErrors());
            }
        }

        $valid = $mod_install->validateTmp();
        $this->view->assign([
            'mod_validate' => $valid,
            'mod_errors' => $mod_install->getErrors(),
            'mod_info' => $mod_install->getTmpInfo(),
            'elements' => $helper
        ]);

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                'next' => new ToolbarButton\SaveForm($this->router->getAdminUrl('addStep3'), t('установить')),
                'back' => new ToolbarButton\Cancel($this->router->getAdminUrl(false), t('назад')),
                'clean' => new ToolbarButton\Button($this->router->getAdminUrl('cleanTmp'), t('удалить модуль из временной папки'), [
                    'attr' => [
                        'class' => 'btn-danger'
                    ]
                ]),
            ]
        ]));

        if (!$valid) {
            $helper['bottomToolbar']->removeItem('next');
        }

        $helper['form'] = $this->view->fetch('add_step2.tpl');
        return $this->result->setTemplate($helper['template']);
    }

    function actionAddSuccess()
    {
        if (!isset($_SESSION['INSTALLED_MODULE'])) {
            Application::getInstance()->redirect($this->router->getAdminUrl(false));
        }

        $helper = new CrudCollection($this);
        $helper
            ->viewAsForm()
            ->setTopTitle(t('Установка модуля завершена'))
            ->setBottomToolbar(new Toolbar\Element([
                'Items' => [
                    new ToolbarButton\Cancel($this->router->getAdminUrl(false), t('к списку модулей'))
                ]
            ]));

        $this->view->assign([
            'elements' => $helper,
            'module_name' => $_SESSION['INSTALLED_MODULE']
        ]);
        unset($_SESSION['INSTALLED_MODULE']);
        $helper['form'] = $this->view->fetch('add_ok.tpl');

        return $this->result->setTemplate($helper['template']);
    }

    function actionCleanTmp()
    {
        $mod_install = Installer::getInstance();
        $mod_install->cleanTmpFolder();
        return $this->result->setSuccess(true)->setRedirect($this->router->getAdminUrl(false));
    }

    /**
     * Удаляет модуль
     *
     * @return Standard
     * @throws RSException
     */
    function actionDel()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_DELETE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }

        $chk = $this->url->request('chk', TYPE_ARRAY, []);
        $mod_install = Installer::getInstance();

        if (!$mod_install->uninstallModules($chk)) {
            foreach ($mod_install->getErrors() as $error) {
                $this->result->addEMessage($error);
            }
        }

        return $this->result->setSuccess(true);
    }

    function successSave()
    {
        header('location: ' . $this->url->replaceKey([$this->action_var => '']));
        exit;
    }

    /**
     * Устанавливает или переустанавливает модуль
     *
     * @return Standard
     * @throws EventException
     */
    function actionAjaxReInstall()
    {
        $mod = $this->url->request('module', TYPE_STRING);
        $module = new ModuleItem($mod);
        if ($module->exists()) {
            $this->result->setSuccess(($install_result = $module->install()) === true);

            if ($this->result->isSuccess()) {
                $this->result->addMessage(t('Модуль успешно установлен'));
            } else {
                foreach ($install_result as $error) {
                    $this->result->addEMessage($error);
                }
            }
        }
        return $this->result;
    }

    function actionAjaxInstallDemoData()
    {
        $mod = $this->url->request('module', TYPE_STRING);
        $params = $this->url->request('params', TYPE_ARRAY);

        $module = new ModuleItem($mod);
        if ($module->exists() && ($install = $module->getInstallInstance()) !== false) {
            $access_error = Rights::CheckRightError($mod, DefaultModuleRights::RIGHT_CREATE);
            if (!$access_error) {
                $result = $install->insertDemoData($params);

                $this->result->setSuccess($result);
                if ($this->result->isSuccess()) {
                    if ($result === true) {
                        $this->result->addMessage(t('Данные успешно добавлены'));
                    } else {
                        $this->result
                            ->addSection('repeat', true)
                            ->addSection('queryParams', [
                                'data' => [
                                    'params' => $result
                                ]
                            ]);
                    }
                } else {
                    foreach ($install->getErrors() as $error) {
                        $this->result->addEMessage($error);
                    }
                }
            } else {
                $this->result->addEMessage($access_error);
            }
        }
        return $this->result;
    }

    /**
     * Сбрасывает настройки модуля по умолчанию
     *
     * @return Standard
     */
    function actionAjaxResetSettings()
    {
        $mod = $this->url->request('module', TYPE_STRING);
        $module = new ModuleItem($mod);
        if ($module->exists()) {
            $config = $module->getConfig();
            $access_error = Rights::CheckRightError($mod, $config->getRightUpdate());
            if (!$access_error) {
                //Сохраняем значения системных полей
                $system_settings = ['enabled', 'installed', 'site_id', 'deactivated'];
                $system_values = array_intersect_key($config->getValues(), array_flip($system_settings));

                $config->clear();
                $config->_configInitDefaults();
                $config->getFromArray($system_values);
                if ($config->update()) {
                    return $this->result->addMessage(t('Настройки успешно восстановлены'));
                } else {
                    return $this->result->addEMessage($config->getErrorsStr());
                }
            } else {
                return $this->result->addEMessage($access_error);
            }
        } else {
            return $this->result->addEMessage(t('Модуль не найден'));
        }
    }

    /**
     * Отображает историю изменений модуля
     *
     * @return Standard
     */
    function actionAjaxShowChangelog()
    {
        $mod = $this->url->request('module', TYPE_STRING);
        $module = new ModuleItem($mod);

        if ($module->exists()) {
            $config = $module->getConfig();
            $helper = new CrudCollection($this);
            $helper->setTopTitle(t('История изменений модуля {module_title}'), ['module_title' => $config['name']]);
            $helper->setBottomToolbar(new Toolbar\Element([
                'items' => [
                    new ToolbarButton\Cancel('')
                ]
            ]));
            $helper->viewAsForm();

            $this->view->assign([
                'module_item' => $module
            ]);

            $helper['form'] = $this->view->fetch('show_changelog.tpl');
            $this->result->setTemplate($helper['template']);
        }

        return $this->result;
    }

    /**
     * Отображает панель со списком других модулей
     *
     * @return Standard
     */
    function actionAjaxModuleList()
    {
        $this->view->assign([
            'modules' => $this->api->tableData(ModuleApi::SORT_BY_MODULE_NAME)
        ]);

        return $this->result->addSection('title', t('Перейти к настройкам модуля'))->setTemplate('module_list.tpl');
    }

    /**
     * Отображает диалог поиска по опциям
     *
     * @return Standard
     * @throws \SmartyException
     */
    function actionSearchOptions()
    {
        $helper = new CrudCollection($this);
        $helper->viewAsAny();
        $helper->setTopTitle(t('Поиск по настройкам'));
        $helper->setForm($this->view->fetch('search_options.tpl'));

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Производит поиск по опциям, возвращает результат
     *
     * @return Standard
     */
    function actionSearchOptionsByTerm()
    {
        $term = $this->url->get('term', TYPE_STRING);

        $term = mb_strlen($term) > 1 ? $term : '';

        if ($term != '') {
            $search_options_api = new SearchOptionsApi();
            $tree = $search_options_api->search($term);
        } else {
            $tree = [];
        }

        $this->view->assign([
            'term' => $term,
            'tree' => $tree
        ]);

        return $this->result
                        ->setSuccess(true)
                        ->setTemplate('search_options_result.tpl');
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
        $helper = $this->helperTableOptions();
        $this->view->assign('elements', $helper);
        $helper['form'] = $this->view->fetch('%system%/admin/tableoptions.tpl');
        return $this->result->setHtml($this->view->fetch($helper['template']))->getOutput();
    }

    /**
     * Подготавливает Helper для опций таблицы
     *
     * @return CrudCollection
     */
    function helperTableOptions()
    {
        $helper = new CrudCollection($this, null, $this->url);
        $helper->setBottomToolbar(new Toolbar\Element([
                'Items' => [
                    'save' => new ToolbarButton\Button(null, t('сохранить'), ['attr' => [
                        'class' => 'btn-success saveToCookie'
                    ]]),
                    'cancel' => new ToolbarButton\Cancel(null),
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
}
