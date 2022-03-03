<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\FilterType;

use RS\Html\Filter\Type\Select;
use RS\Orm\Request;
use Users\Model\ApiVerification;
use Users\Model\Orm\User;

/**
* Фильтр по дублям. Отображается как селект.
*/
class Doubles extends Select
{
    public function __construct($key, $title, $options = [])
    {
        $this->title_attr = [
            'class' => 'standartkey hello'
        ];
        parent::__construct($key, $title, $this->getAllowFields(), $options);
    }

    /**
     * Возвращает список полей, по которым возможен поиск дублей
     *
     * @return array
     */
    function getAllowFields()
    {
        return [
            '' => t('не выбрано'),
            'phone'=> t('номер телефона'),
            'e_mail' => t('e-mail')
        ];
    }

    /**
     * Возвращает выбранное значение
     *
     * @return mixed|void
     */
    function getValue()
    {
        $value = parent::getValue();
        $allow_values = $this->getAllowFields();

        if (isset($allow_values[$value])) {
            return $value;
        }

        return '';
    }

    /**
     * Возвращает условие для отбора пользователей
     *
     * @return string
     */
    function getWhere()
    {
        $value = $this->getValue(); // в value находится имя поля, для которого надо найти дубли
        if ($value){
            $values = $this->checkDuplicateUserByField($value); //получим повторяющиеся значения

            /** @var $q Request*/
            $q = Request::make();
            if (!empty($values)) {
                $q->whereIn($value, $values);
            } else {
                $q->where(['id' => 0]);
            }

            $where = "($q->where)"; // сгруппируем
        }
        return $where ?? '';
    }

    /**
     * Возвращает пользователей, у которых дублируются поля $field
     *
     * @param string $field Имя поля
     * @return array
     */
    public function checkDuplicateUserByField($field)
    {
        $users = Request::make()
            ->select($field)
            ->from(new User())
            ->groupby($field)
            ->having("count(*) > 1")
            ->where("`$field` IS NOT NULL");

        return $users->exec()->fetchSelected(null, $field);
    }
}
