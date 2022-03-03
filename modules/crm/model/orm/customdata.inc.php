<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;

use RS\Orm\AbstractObject;
use RS\Orm\Request;
use RS\Orm\Type;

/**
 * ORM объект хранит данные по кастомным полям объектов (заданным в административной панели) пользователем
 * --/--
 * @property string $object_type_alias Тип объекта, к которому привязан статус
 * @property integer $object_id ID объекта
 * @property string $field Идентификатор поля
 * @property double $value_float Числовое значение для поиска
 * @property string $value_string Строковое значение для поиска
 * @property string $value Текстовое значение
 * --\--
 */
class CustomData extends AbstractObject
{
    const
        MAX_STRING_INDEX_LENGTH = 100;

    protected static
        $table = 'crm_custom_data';

    function _init()
    {
        $this->getPropertyIterator()->append([
            'object_type_alias' => new Type\Varchar([
                'description' => t('Тип объекта, к которому привязан статус'),
                'maxLength' => 50,
            ]),
            'object_id' => new Type\Integer([
                'description' => t('ID объекта')
            ]),
            'field' => new Type\Varchar([
                'description' => t('Идентификатор поля')
            ]),
            'value_float' => new Type\Real([
                'decimal' => 4,
                'maxLength' => 20,
                'description' => t('Числовое значение для поиска'),
                'hint' => t('Дублирует value, для числового поиска')
            ]),
            'value_string' => new Type\Varchar([
                'maxLength' => self::MAX_STRING_INDEX_LENGTH,
                'description' => t('Строковое значение для поиска'),
                'hint' => t('Дублирует value, для строкового поиска')
            ]),
            'value' => new Type\Text([
                'description' => t('Текстовое значение')
            ])
        ]);

        $this->addIndex(['object_type_alias', 'object_id', 'field'], self::INDEX_PRIMARY);

        $this->addIndex(['object_type_alias', 'object_id', 'value_float'], self::INDEX_KEY);
        $this->addIndex(['object_type_alias', 'object_id', 'value_string'], self::INDEX_KEY);
    }

    /**
     * Возвращает список полей, составляющих первичный ключ объекта
     *
     * @return array
     */
    function getPrimaryKeyProperty()
    {
        return [
            'object_type_alias',
            'field'
        ];
    }

    /**
     * Сохраняет кастомные поля
     *
     * @param string $object_type_alias - короткое имя ORM объекта
     * @param integer $object_id - ID объекта
     * @param array $values - значения полей
     * @return void
     */
    public static function saveCustomFields($object_type_alias, $object_id, $values)
    {
        Request::make()
            ->delete()
            ->from(new self())
            ->where([
                'object_type_alias' => $object_type_alias,
                'object_id' => $object_id
            ])->exec();

        if ($values) {
            foreach ($values as $field => $value) {
                $customdata = new self();
                $customdata['object_type_alias'] = $object_type_alias;
                $customdata['object_id'] = $object_id;
                $customdata['field'] = $field;
                $customdata['value'] = $value;
                $customdata['value_string'] = mb_substr($value, 0, self::MAX_STRING_INDEX_LENGTH);
                $customdata['value_float'] = (float)$value;
                $customdata->insert();
            }
        }
    }

    /**
     * Возвращает значения кастомных полей
     *
     * @param string $object_type_alias - короткое имя ORM объекта
     * @param integer $object_id - ID объекта
     * @return array
     */
    public static function loadCustomFields($object_type_alias, $object_id)
    {
        return Request::make()
            ->from(new self())
            ->where([
                'object_type_alias' => $object_type_alias,
                'object_id' => $object_id
            ])->exec()->fetchSelected('field', 'value');
    }

    /**
     * Валидирует значения кастомных полей
     *
     * @param AbstractObject $orm ORM объект
     * @param mixed $value массив значений для проверки
     * @param string $property_name название свойства в ORM объекте
     *
     * @return bool|string
     */
    public static function validateCustomFields($orm, $value, $property_name)
    {
        $fields_manager = $orm['__'.$property_name]->getFieldsManager();
        $check_result = $fields_manager->check($value);

        if (!$check_result) {
            return implode(', ', $fields_manager->getErrors());
        }
        return true;
    }
}