<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Config\ModuleRights;
use Crm\Model\AutoTask\RuleIf\AbstractIfRule;
use Crm\Model\Autotask\TaskTemplate;
use Crm\Model\Orm\Status;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;

class AutoTaskRuleCtrl extends Crud
{
    function __construct()
    {
        //Устанавливаем, с каким API будет работать CRUD контроллер
        parent::__construct(new \Crm\Model\AutoTaskRuleApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->setTopTitle('Правила для создания автозадач'); //Установим заголовок раздела
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить правило')]));
        $helper->setTopHelp(t('Здесь описываются правила, согласно которым система будет автоматически создавать задачи при наступлении выбранных событий'));

        //Отобразим таблицу со списком объектов
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Yesno('enable', t('Включен'), ['toggleUrl' => $this->router->getAdminPattern('ajaxTogglePublic', [':id' => '@id'])]),

                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                )
            ]]));

        //Добавим фильтр значений в таблице по названию
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['items' => [
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                    ]
                    ])
                ],
            ])
        ]));

        return $helper;
    }

    function actionAjaxTogglePublic()
    {
        if ($access_error = \RS\AccessControl\Rights::CheckRightError($this, ModuleRights::AUTOTASK_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }
        $id = $this->url->get('id', TYPE_STRING);

        $action_template = $this->api->getOneItem($id);
        if ($action_template) {
            $action_template['enable'] = !$action_template['enable'];
            $action_template->update();
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Возвращает HTML-код дополнительных форм для условия задачи
     */
    function actionAjaxGetRuleFormHtml()
    {
        $rule_if_class = $this->url->get('rule_if_class', TYPE_STRING);
        $rule_id_object = AbstractIfRule::makeById($rule_if_class);

        $this->view->assign('rule_if_object', $rule_id_object);
        return $this->result->setTemplate( 'form/autotaskrule/rule_data_form.tpl' );
    }

    function helperAddTaskRule()
    {
        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Добавить автозадачу'));
        $helper->setBottomToolbar($this->buttons([
            'save',
            'cancel'
        ]));
        $helper->viewAsForm();

        return $helper;
    }

    /**
     * Открывает диалог добавления шаблона задачи
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAddTaskRule()
    {
        $task_template_values_base64 = $this->url->get('task_template_values', TYPE_STRING);
        $rule_if_id = $this->url->get('rule_if_id', TYPE_STRING);

        $helper = $this->getHelper();
        $rule_if_object = AbstractIfRule::makeById($rule_if_id);

        $task_template = new TaskTemplate();
        $task_template->getFromBase64($task_template_values_base64); //Загружаем значения по умолчанию
        $rule_if_object->initTaskTemplate($task_template);

        if ($task_template_values_base64) {
            $helper->setTopTitle( t('Редактировать автозадачу {title}'), ['title' => $task_template['title']]);
        }

        if ($this->url->isPost()) {
            if ($task_template->checkData()) {

                $this->view->assign([
                    'task_tpl' => $task_template
                ]);

                return $this->result
                    ->setSuccess(true)
                    ->addSection('task_template_block', $this->view->fetch('%crm%/form/autotaskrule/rule_then_data_item.tpl'));

            } else {
                return $this->result->setSuccess(false)->setErrors($task_template->getDisplayErrors());
            }
        }


        $helper->setFormObject($task_template);

        $this->view->assign([
            'elements' => $helper->active(),
        ]);
        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     * Возвращает HTML одного правила для смены статуса
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxGetAutochangeStatusRule()
    {
        $statuses = Status::getStatusesTitles('crm-task');
        $first_status = count($statuses) > 1 ? reset($statuses) : 0;

        $uniq = uniqid(time());
        $rules_data = [
            $uniq => [
                'set_status' => $first_status,
                'groups' => [
                    0 => [
                        'items' => [
                            0 => [
                                'task_index' => '1',
                                'task_status' => $first_status
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->view->assign([
            'statuses' => $statuses,
            'rules' => $rules_data
        ]);

        return $this->result->setTemplate('form/tasktemplate/autochange_rule_parts/rule_item.tpl');

    }

    /**
     * Возвращает HTML одной группы для смены статуса
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxGetAutochangeStatusGroupItem()
    {
        $rule_uniq = $this->url->get('rule_uniq', TYPE_STRING);
        $statuses = Status::getStatusesTitles('crm-task');
        $first_status = count($statuses) > 1 ? reset($statuses) : 0;
        $uniq = uniqid(time());

        $groups_data = [
            $uniq => [
                'items' => [
                    0 => [
                        'task_index' => '1',
                        'task_status' => $first_status
                    ]
                ]
            ]
        ];

        $this->view->assign([
            'statuses' => $statuses,
            'rule_uniq' => $rule_uniq,
            'groups' => $groups_data
        ]);

        return $this->result->setTemplate('form/tasktemplate/autochange_rule_parts/group_item.tpl');
    }

    /**
     * Возвращает HTML одной записи в группе для смены статуса
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxGetAutochangeStatusOrItem()
    {
        $rule_uniq = $this->url->get('rule_uniq', TYPE_STRING);
        $group_uniq = $this->url->get('group_uniq', TYPE_STRING);

        $statuses = Status::getStatusesTitles('crm-task');
        $first_status = count($statuses) > 1 ? reset($statuses) : 0;
        $uniq = uniqid(time());

        $groups_items_data = [
            $uniq => [
                'task_index' => '1',
                'task_status' => $first_status
            ]
        ];

        $this->view->assign([
            'statuses' => $statuses,
            'rule_uniq' => $rule_uniq,
            'group_uniq' => $group_uniq,
            'group_items' => $groups_items_data
        ]);

        return $this->result->setTemplate('form/tasktemplate/autochange_rule_parts/or_item.tpl');

    }

}