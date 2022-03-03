<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask\Ruleif;

use Catalog\Model\Orm\OneClickItem;
use Crm\Model\Autotask\TaskTemplate;
use RS\Orm\FormObject;
use RS\View\Engine;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Order;

/**
 * Класс описывает условие: "Создание покупки в 1 клик"
 */
class CreateOneClick extends AbstractIfRule
{
    const
        USER_CASE_CUSTOM_USER = 'custom_user',
        USER_CASE_RANDOM_MANAGER = 'random_manager';

    protected
        $one_click_item;

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Если создана покупка в 1 клик');
    }

    /**
     * Возвращает описание класса
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Задачи будут создаваться сразу после оформления покупки в 1 клик');
    }


    /**
     * Наполняет текущий класс значениями полей formObject'а
     *
     * @param array|formObject $data
     * @param Order $order
     */
    public function init(OneClickItem $one_click_item)
    {
        $this->one_click_item = $one_click_item;
    }

    /**
     * Модифицирует описание полей шаблона задачи
     *
     * @param $task_template
     */
    public function initTaskTemplate(TaskTemplate $task_template)
    {
        $task_template['__implementer_user_type']->setListFromArray([
            self::USER_CASE_CUSTOM_USER => t('Выбранный пользователь'),
            self::USER_CASE_RANDOM_MANAGER => t('Случайный менеджер')
        ]);

        $view = new Engine();
        $view->assign([
            'replace_vars' => $this->getReplaceVarTitles()
        ]);
        $replace_hint = $view->fetch('%crm%/form/autotaskrule/replace_vars_hint.tpl');

        $task_template['__title']->setHint($replace_hint);
        $task_template['__description']->setHint($replace_hint);
    }


    /**
     * Трансформирует значения полей шаблона задачи в значения для задачи
     *
     * @param array $values
     * @return array
     */
    public function transformTaskTemplateValues($values)
    {
        $values = parent::transformTaskTemplateValues($values);

        switch ($values['implementer_user_type']) {
            case self::USER_CASE_RANDOM_MANAGER:
                $managers_ids = array_keys(OrderApi::getUsersManagers());
                if ($managers_ids) {
                    $index = rand(0, count($managers_ids));
                    $values['implementer_user_id'] = $managers_ids[$index];
                } else {
                    $values['implementer_user_id'] = 0;
                }
        }

        return $values;
    }

    /**
     * Возвращает переменные, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceVarTitles()
    {
        return [
            'title' => t('Наименование заявки'),
            'client_name' => t('ФИО клиента'),
            'client_phone' => t('Телефон клиента'),
            'status' => t('Статус заявки'),
        ];
    }

    /**
     * Заменяет переменные в необходимых строках массива значений $values
     *
     * @param array $string
     * @return mixed
     */
    public function replaceVars($values)
    {
        $for_replace = [
            '{title}' => $this->one_click_item['title'],
            '{client_name}' => $this->one_click_item->getUser()->getFio(),
            '{client_phone}' => $this->one_click_item['user_phone'],
            '{status}' => $this->one_click_item['__status']->textView(),
        ];

        $replace_keys = ['title', 'description'];

        foreach($replace_keys as $key) {
            if (isset($values[$key])) {
                $values[$key] = str_replace(array_keys($for_replace), array_values($for_replace), $values[$key]);
            }
        }

        return $values;
    }
}