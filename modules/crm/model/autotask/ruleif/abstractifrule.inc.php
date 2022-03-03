<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\AutoTask\RuleIf;
use Crm\Model\Autotask\TaskTemplate;
use Crm\Model\Utils;
use RS\Event\Manager;
use RS\Orm\FormObject;
use RS\Orm\Type;

/**
 * Базовый класс, описывающий возникающее условие для создания связанных задач.
 * Например: "Если создан заказ"
 * или "Если создана покупка в 1 клик"
 * ....
 *
 * Класс должен возвращать определенные данные, которые затем будут использованы при создании связанных задач
 */
abstract class AbstractIfRule
{
    protected $form_data = [];

    /**
     * Возвращает идентификатор типа
     *
     * @return string
     */
    public static function getId()
    {
        $class_name = strtolower(trim(str_replace('\\', '-', get_called_class()),'-'));
        $id = str_replace('-model-autotask-ruleif', '', $class_name);

        if ($id == $class_name) {
            throw new \RS\Exception(t('Класс типа связи должен находиться в пространстве имен: ИМЯ_МОДУЛЯ\Model\Autotask\RuleIf'));
        }

        return $id;
    }

    /**
     * Возвращает объект класса-условия по его идентификатору
     *
     * @param string $id
     */
    public static function makeById($id)
    {
        $class_name = preg_replace('/-/', '-model-autotask-ruleif-', $id, 1, $count);
        if ($count) {
            $class_name = str_replace('-', '\\', $class_name);
            if (class_exists($class_name)) {
                return new $class_name();
            }
        }

        throw new \RS\Exception(t('Класс типа связи `%0` не найден', [$class_name]));
    }

    /**
     * Возвращает все зарегистрированные в системе классы-условия
     *
     * @return  AbstractIfRule[]
     */
    final public static function getAllRules()
    {
        $if_rules_objects = Manager::fire('crm.getifrules', [])->getResult();
        foreach($if_rules_objects as $item) {
            if (!($item instanceof self)) {
                throw new \RS\Exception(t('Класс-условие для автозадач должен был потомком класса Crm\Model\AutoTask\RuleIf\AbstractIfRule'));
            }
        }

        return $if_rules_objects;
    }

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Возвращает дополнительную форму, которую необходимо заполнить
     *
     * @return FormObject|null
     */
    public function getFormObject()
    {
        return null;
    }

    /**
     * Возвращает описание условия, при котором будут создаваться задачи
     *
     * @return string
     */
    abstract public function getDescription();


    /**
     * Модифицирует описание полей шаблона задачи
     *
     * @param $task_template
     */
    public function initTaskTemplate(TaskTemplate $task_template)
    {}

    /**
     * Возвращает true, если условие сработало и необходимо создавать автозадачи.
     * Вызывается уже после вызова init, что означает, что инициализация уже проведена и
     * есть необходимые переменные, которые можно проверить при необходимости
     *
     * @return bool
     */
    public function match()
    {
        return true;
    }

    /**
     * Трансформирует значения полей шаблона задачи в значения для задачи
     *
     * @param array $values
     * @return mixed
     */
    public function transformTaskTemplateValues($values)
    {
        if ($values['date_of_planned_end']) {
            $delta = Utils::getDurationDeltaTimestamp($values['date_of_planned_end']);
            $values['date_of_planned_end'] = date('Y-m-d H:i:s', time() + $delta);
        } else {
            $values['date_of_planned_end'] = null;
        }

        return $values;
    }

    /**
     * Устанавливает
     *
     * @param array $form_data
     */
    public function setFormData(array $form_data)
    {
        $this->form_data = $form_data;
    }

    /**
     * Возвращает HTML формы объекта, полеченного из метода getFormObject
     *
     * @return string
     */
    public function getFormHtml()
    {
        if ($params = $this->getFormObject()) {
            $params->getPropertyIterator()->arrayWrap('rule_if_data_arr');
            $params->getFromArray((array)$this->form_data);
            $params->setFormTemplate(strtolower(str_replace('\\', '_', get_class($this))));
            $module = \RS\Module\Item::nameByObject($this);
            $tpl_folder = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$module.\Setup::$MODULE_TPL_FOLDER;

            return $params->getForm(null, null, false, null, '%system%/coreobject/tr_form.tpl', $tpl_folder);
        }

        return '';
    }

    /**
     * Возвращает переменные, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceVarTitles()
    {
        return [];
    }
}