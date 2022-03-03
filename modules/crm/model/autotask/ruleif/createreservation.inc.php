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
use Shop\Model\Orm\Reservation;

/**
 * Класс описывает условие: "Создание покупки в 1 клик"
 */
class CreateReservation extends AbstractIfRule
{
    const
        USER_CASE_CUSTOM_USER = 'custom_user',
        USER_CASE_RANDOM_MANAGER = 'random_manager';

    protected
        $reservation;

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Если создан предзаказ на покупку');
    }

    /**
     * Возвращает описание класса
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Задачи будут создаваться сразу после оформления предзаказа');
    }


    /**
     * Наполняет текущий класс значениями полей formObject'а
     *
     * @param array|formObject $data
     * @param Order $order
     */
    public function init(Reservation $reservation)
    {
        $this->reservation = $reservation;
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
            'id' => t('Номер заявки'),
            'product_barcode' => t('Артикул продукта'),
            'product_title' => t('Название товара'),
            'offer' => t('Название комплектации'),
            'amount' => t('Количество'),
            'phone' => t('Телефон клиента'),
            'email' => t('E-mail'),
            'status' => t('Статус')
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
            '{id}' => $this->reservation['id'],
            '{product_barcode}' => $this->reservation['product_barcode'],
            '{product_title}' => $this->reservation['product_title'],
            '{offer}' => $this->reservation['offer'],
            '{amount}' => $this->reservation['amount'],
            '{phone}' => $this->reservation['phone'],
            '{email}' => $this->reservation['email'],
            '{status}' => $this->reservation['__status']->textView()
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