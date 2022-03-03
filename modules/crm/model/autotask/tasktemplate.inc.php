<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask;
use Crm\Model\AutoTask\RuleIf\AbstractIfRule;
use Crm\Model\Utils;
use RS\Orm\Type;

/**
 * Шаблон задачи, используется в форме создания правила для автозадач.
 * Некоторые поля обычной формы задачи перегружены
 */
class TaskTemplate extends \Crm\Model\Orm\Task
{
    function _init()
    {
        parent::_init();

        $property_iterator = $this->getPropertyIterator();

        //Скрываем поля, которые не могут шаблонно задаваться.
        $this['__task_num']->setVisible(false);
        $this['__date_of_create']->setVisible(false);
        $this['__date_of_planned_end']->setVisible(false);
        $this['__implementer_user_id']->setVisible(false);
        $this['__date_of_end']->setVisible(false);
        $this['____files__']->setVisible(false);
        $this['__links']->setVisible(false);
        $this['__autotask_index']->setVisible(false);
        $this['__autotask_group']->setVisible(false);

        //Добавляем, изменяем поля
        $property_iterator->append([
            t('Основные'),
                'implementer_user_type' => new Type\Varchar([
                    'description' => t('Исполнитель'),
                    'listFromArray' => [[
                        'user' => t('Выбранный пользователь')
                    ]],
                    'template' => '%crm%/form/task/implementer_user_type.tpl'
                ]),
                'date_of_planned_end' => new Type\Varchar([
                    'description' => t('Планируемая дата завершения'),
                    'hint' => t('d - дней, h - часов, m - минут, s - секунд. Например: 1d 4h 2m 1s')
                ])

        ]);
    }

    /**
     * Загружает объект значениями из base64
     */
    function getFromBase64($values_base64)
    {
        $serialized = base64_decode($values_base64);
        $data = @unserialize($serialized) ?: [];

        if ($data) {
            $this->getFromArray($data);
        }
    }

    /**
     * Возвращает значения данного объекта в base64
     *
     * @return string
     */
    function getBase64Values()
    {
        return base64_encode(serialize($this->getValues()));
    }

    /**
     * Возвращает в читаемом виде срок, заданный в поле $field
     *
     * @returns string
     */
    function getDurationView($field)
    {
        return Utils::renderDurationString($this[$field]);
    }
}