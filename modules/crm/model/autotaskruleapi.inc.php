<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Model\AutoTask\RuleIf\AbstractIfRule;
use Crm\Model\Orm\AutoTaskRule;
use Crm\Model\Orm\Task;
use RS\Cache\Manager;
use RS\Helper\Log;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;

/**
 * API для работы с авто задачами
 */
class AutoTaskRuleApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\AutoTaskRule());
    }

    /**
     * Выполняет проверку на необходимость создания
     * автозадач при наступлении события $event_if_rule
     *
     * @param AbstractIfRule $event_if_rule
     */
    public static function run($event_if_rule)
    {
        $id = $event_if_rule->getId();
        $auto_tasks = self::getAutoTaskRules($id);
        if (isset($auto_tasks[$id])) {
            foreach($auto_tasks[$id] as $autotask_rule) {
                $event_if_rule->setFormData($autotask_rule['rule_if_data_arr']);
                if ($event_if_rule->match()) {

                    self::triggerMakeAutoTask($autotask_rule, $event_if_rule);

                }
            }
        }
    }

    /**
     * Запускает создание автозадач
     *
     * @param $autotask_rule
     * @param $event_if_rule
     */
    public static function triggerMakeAutoTask(AutoTaskRule $autotask_rule, $event_if_rule)
    {
        $autotask_group = null;
        foreach($autotask_rule->getTasks() as $n => $task_template) {
            $task_values = $event_if_rule->transformTaskTemplateValues($task_template->getValues());
            $task_values = $event_if_rule->replaceVars($task_values);

            unset($task_values['id']);

            $task = new Task();
            $task->getFromArray($task_values);

            $task['task_num'] = \RS\Helper\Tools::generatePassword(8, range('0', '9'));
            $task['autotask_index'] = $n + 1;
            $task['autotask_group'] = $autotask_group;
            $task['date_of_create'] = date('Y-m-d H:i:s');

            if ($task->insert()) {
                if ($n == 0) {
                    $autotask_group = $task['id'];
                    $task['autotask_group'] = $autotask_group;
                    $task->update();
                }
            } else {
                break;
            }
        }
    }

    /**
     * Возвращает автозадачи, сгруппированные по классам-условиям
     *
     * @param $rule_if_class
     * @param bool $cache
     * @return array|mixed
     */
    public static function getAutoTaskRules($rule_if_class, $cache = true)
    {
        if ($cache) {
            return Manager::obj()
                ->expire(0)
                ->watchTables(new AutoTaskRule())
                ->request([__CLASS__, 'getAutoTaskRules'], $rule_if_class, false);
        } else {
            $tasks = Request::make()
                ->from(new AutoTaskRule())
                ->where([
                    'rule_if_class' => $rule_if_class,
                    'enable' => 1
                ])
                ->objects(null, 'rule_if_class', true);

            return $tasks;
        }
    }


    /**
     * Проверят, нужно ли изменить статус другим связанным задачам. Если нужно, то изменяет.
     *
     * @param Task $task
     */
    public static function autoChangeStatus(Task $task)
    {
        if ($task['autotask_group']) {

            //Получаем другие задачи
            $linked_tasks = Request::make()
                ->from(new Task)
                ->where([
                    'autotask_group' => $task['autotask_group'],
                ])
                ->objects(null, 'autotask_index');

            foreach($linked_tasks as $linked_task) {
                if ($linked_task['is_autochange_status'] && $linked_task['autotask_index'] != $task['autotask_index']) {
                    foreach ($linked_task['autochange_status_rule_arr'] as $rule) {

                        if (self::isDependFromTask($linked_task, $task)
                            && self::checkIfRule($rule['groups'], $linked_tasks)) {
                            $linked_task['status_id'] = $rule['set_status'];
                            $linked_task['disable_autochange_status'] = true;
                            $linked_task->update();
                        }

                    }
                }
            }
        }
    }

    /**
     * Возвращает true, если linked_task имеет условия по смене статуса, зависящие от task
     *
     * @param $linked_task - проверяемая задача
     * @param $task - изменяемая задача
     * @return bool
     */
    protected static function isDependFromTask($linked_task, $task)
    {
        $result = false;
        if ($linked_task['is_autochange_status']) {
            foreach ($linked_task['autochange_status_rule_arr'] as $rule) {
                foreach($rule['groups'] as $group) {
                    foreach($group['items'] as $or_rule) {
                        if ($or_rule['task_index'] == $task['autotask_index']) {
                            $result = true;
                            break 3;
                        }
                    }
                }

            }
        }

        return $result;
    }

    /**
     * Возвращает true, если условие всей группы
     *
     * @param array $groups группы условий
     * @param array $linked_tasks все связанные задачи
     * @param integer $self_index номер задачи, чьи правила в настоящее время проверяются
     * @return bool
     */
    protected static function checkIfRule($groups, $linked_tasks)
    {
        $result = count($groups)>0;

        foreach($groups as $group) {
            $or_group_result = false;
            foreach($group['items'] as $or_rule) {
                if (isset($linked_tasks[$or_rule['task_index']])) {

                    $or_group_result = $or_group_result
                                        || ($linked_tasks[$or_rule['task_index']]['status_id'] == $or_rule['task_status']);
                }
            }
            $result = $result && $or_group_result;
        }

        return $result;
    }
}