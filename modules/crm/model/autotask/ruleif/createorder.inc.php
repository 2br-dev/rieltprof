<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask\Ruleif;

use Crm\Model\Autotask\TaskTemplate;
use Crm\Model\Links\Type\LinkTypeOrder;
use RS\Helper\Tools;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use RS\View\Engine;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Order;

/**
 * Класс описывает условие: "Создание заказа"
 */
class CreateOrder extends AbstractIfRule
{
    const
        USER_CASE_CUSTOM_USER = 'custom_user',
        USER_CASE_ORDER_MANAGER = 'order_manager',
        USER_CASE_RANDOM_MANAGER = 'random_manager';

    protected $order;


    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Если создан заказ');
    }

    /**
     * Возвращает описание класса
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Задачи будут создаваться сразу после оформления заказа в клиентской части');
    }

    /**
     * Наполняет текущий класс значениями полей formObject'а
     *
     * @param array|formObject $data
     * @param Order $order
     */
    public function init(Order $order)
    {
        $this->order = $order;
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
            self::USER_CASE_ORDER_MANAGER => t('Менеджер заказа'),
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

        //Добавляем связь с заказом
        $values['links'][LinkTypeOrder::getId()] = [$this->order['id']];

        switch ($values['implementer_user_type']) {
            case self::USER_CASE_ORDER_MANAGER:
                $values['implementer_user_id'] = (int)$this->order['manager_user_id'];
                break;

            case self::USER_CASE_RANDOM_MANAGER:
                $managers_ids = array_keys(OrderApi::getUsersManagers());

                if ($managers_ids) {
                    $index = rand(0, count($managers_ids)-1);
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
            'order_num' => t('Номер заказа'),
            'total_cost' => t('Сумма заказа'),
            'client_name' => t('ФИО клиента заказа'),
            'address' => t('Адрес доставки'),
            'payment' => t('Название способа оплаты'),
            'delivery' => t('Название способа доставки')
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
            '{order_num}' => $this->order['order_num'],
            '{total_cost}' =>  \RS\Helper\CustomView::cost($this->order['totalcost']),
            '{client_name}' => $this->order->getUser()->getFio(),
            '{address}' => $this->order->getAddress()->getLineView(),
            '{payment}' => $this->order->getPayment()->title,
            '{delivery}' => $this->order->getDelivery()->title
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