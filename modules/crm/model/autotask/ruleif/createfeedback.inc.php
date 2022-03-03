<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\AutoTask\RuleIf;

use Crm\Model\Autotask\TaskTemplate;
use Feedback\Model\Orm\ResultItem;
use RS\View\Engine;

/**
 * Класс описывает условие: "Создание обратной связи"
 */
class CreateFeedback extends AbstractIfRule
{
    /**
     * @var ResultItem
     */
    protected $result_item;

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Если получен ответ в форме обратной связи');
    }

    /**
     * Возвращает описание класса
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Задачи будут создаваться сразу после отправки пользователем ответа в форму обратной связи');
    }


    /**
     * Наполняет текущий класс значениями полей formObject'а
     *
     * @param ResultItem $feedback_result_item
     */
    public function init(ResultItem $feedback_result_item)
    {
        $this->result_item = $feedback_result_item;
    }

    /**
     * Модифицирует описание полей шаблона задачи
     *
     * @param $task_template
     */
    public function initTaskTemplate(TaskTemplate $task_template)
    {
        $view = new Engine();
        $view->assign([
            'replace_vars' => $this->getReplaceVarTitles()
        ]);
        $replace_hint = $view->fetch('%crm%/form/autotaskrule/replace_vars_hint.tpl');

        $task_template['__title']->setHint($replace_hint);
        $task_template['__description']->setHint($replace_hint);
    }


    /**
     * Возвращает переменные, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceVarTitles()
    {
        return [
            'title' => t('Наименование сообщения'),
            'field.<псевдоним поля формы>' => t('Значение указанного поля'),
            'form_name' => t('Название формы')
        ];
    }

    /**
     * Заменяет переменные в необходимых строках массива значений $values
     *
     * @param array $values
     * @return mixed
     */
    public function replaceVars($values)
    {
        $form = $this->result_item->getFormObject();

        $for_replace = [
            '{title}' => $this->result_item['title'],
            '{form_name}' => $form['title'],
        ];

        $fields_result = $this->result_item->tableDataUnserialized();

        foreach($form->getFields() as $field_item) {
            $alias = $field_item['alias'];
            $value = $this->findFieldValue($fields_result, $alias);
            if ($value !== false) {
                $for_replace['{field.' . $alias . '}'] = $value;
            }
        }

        $replace_keys = ['title', 'description'];

        foreach($replace_keys as $key) {
            if (isset($values[$key])) {
                $values[$key] = str_replace(array_keys($for_replace), array_values($for_replace), $values[$key]);
            }
        }

        return $values;
    }

    /**
     * Возвращает значение поля $alias среди данных $fields_result
     *
     * @param $fields_result
     * @param $alias
     * @return bool|string
     */
    private function findFieldValue($fields_result, $alias)
    {
        foreach($fields_result as $item) {
            if ($item['field']['alias'] == $alias) {
                $value = $item['value'];

                if ($item['field']['show_type'] == 'file') {
                    if (!isset($value['real_file_name'])) {
                        return t('Файл не загружен');
                    } else {
                        return $value['real_file_name'];
                    }
                }
                elseif ($item['field']['show_type'] == 'list') {
                    if (is_array($value)) {
                        return implode(', ', $value);
                    } else {
                        return $value;
                    }
                }
                else {
                    return $value;
                }
            }
        }

        return false;
    }
}