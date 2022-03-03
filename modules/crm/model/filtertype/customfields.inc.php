<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\FilterType;
use Crm\Model\Orm\CustomData;
use RS\Config\UserFieldsManager;
use RS\Html\Filter\Type\AbstractType as FilterAbstractType;
use RS\Orm\Request;

/**
 * Фильтр по пользовательским полям
 */
class CustomFields extends FilterAbstractType
{
    public
        $tpl = '%crm%/admin/filtertype/customfields.tpl';

    private
        $object_type_alias,
        $user_fields_manager;

    protected
        $search_type = 'ids';

    function __construct($key, $user_fields_manager, $object_type_alias, array $options = [])
    {
        parent::__construct($key, '', $options);
        $this->setUserFieldsManager($user_fields_manager);
        $this->setObjectTypeAlias($object_type_alias);
    }

    /**
     * Устанавливает менеджер произвольных полей, для которого будет строиться фильтр
     *
     * @param UserFieldsManager $user_fields_manager
     * @return void
     */
    function setUserFieldsManager(UserFieldsManager $user_fields_manager)
    {
        $this->user_fields_manager = $user_fields_manager;
    }

    /**
     * Возвращает менеджер произвольных полей, для которого будет строиться фильтр
     * @return UserFieldsManager
     */
    function getUserFieldsManager()
    {
        return $this->user_fields_manager;
    }

    /**
     * Возвращает массив с установленными значениями фильтра
     *
     * @return array
     */
    function getValue()
    {
        return $this->value ?: [];
    }

    /**
     * Убирает не выбранные значения из values
     *
     * @return array
     */
    function cleanEmptyValues($values)
    {
        $result = [];
        foreach($values as $key => $value) {
            if ($value !== '') {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Устанавливает объект, с которым связаны произвольные поля
     *
     * @param string $object_type_alias
     */
    function setObjectTypeAlias($object_type_alias)
    {
        $this->object_type_alias = $object_type_alias;
    }

    /**
     * Возвращает объект, с которым связаны произвольные поля
     *
     * @return mixed
     */
    function getObjectTypeAlias()
    {
        return $this->object_type_alias;
    }

    /**
     * Возвращает выражение для поиска по кастомным полям
     *
     * @return string
     */
    protected function where_ids()
    {
        $values = $this->cleanEmptyValues($this->getValue());
        $structure = $this->getUserFieldsManager()->getStructure();

        $q = Request::make()
                ->select('object_id')
                ->from(new CustomData())
                ->where(['object_type_alias' => $this->getObjectTypeAlias()]);

        $ids_matrix = [];
        foreach($values as $key => $value) {
            $query = clone $q;

            //Получаем только первые 100 символов, так как поисковый индекс строится по первым 100 символам.
            $value = mb_substr($value, 0, CustomData::MAX_STRING_INDEX_LENGTH);

            if (isset($structure[$key])) {
                //Неточный поиск по строкам
                if ($structure[$key]['type'] == UserFieldsManager::TYPE_STRING
                    || $structure[$key]['type'] == UserFieldsManager::TYPE_TEXT) {

                    $query->where("(`value_string` like '%#string%' AND `field`='#key')", [
                        'string' => $value,
                        'key' => $key
                    ]);
                }

                //Точный поиск по списковому значению
                elseif ($structure[$key]['type'] == UserFieldsManager::TYPE_LIST) {
                    $query->where("(`value_string` = '#string' AND `field`='#key')", [
                        'string' => $value,
                        'key' => $key
                    ]);
                }

                //Точный поиск по полю да/нет
                elseif ($structure[$key]['type'] == UserFieldsManager::TYPE_BOOL) {
                    $query->where("(`value_float` = '#float' AND `field`='#key')", [
                        'float' => (integer)$value,
                        'key' => $key
                    ]);
                }
            }

            $ids_matrix[] = $query->exec()->fetchSelected(null, 'object_id');
        }

        if (count($ids_matrix) > 1) {
            //Находим пересечение всех найденных ID
            $ids = array_values(call_user_func_array("array_intersect", $ids_matrix));
        }
        elseif (count($ids_matrix) == 1) {
            $ids = $ids_matrix[0];
        } else {
            $ids = [];
        }



        if (!$ids) {
            $ids = [0]; //Если ничего не найдено добавим невыполнимое условие
        }

        return 'A.id IN ('.implode(',', $ids).')';
    }


    /**
     * Возвращает массив с данными, об установленых фильтрах для визуального отображения частиц
     *
     * @param array $current_filter_values - значения установленных фильтров
     * @param array $exclude_keys массив ключей, которые необходимо исключить из ссылки на сброс параметра
     * @return array of array ['title' => string, 'value' => string, 'href_clean']
     */
    public function getParts($current_filter_values, $exclude_keys = [])
    {
        $parts = [];

        $values = $this->cleanEmptyValues($this->getValue());
        $structure = $this->getUserFieldsManager()->getStructure();

        if ($values) {
            foreach ($values as $key => $value) {

                if ($structure[$key]['type'] == UserFieldsManager::TYPE_BOOL) {
                    $value = $value ? t('Да') : t('Нет');
                }

                $without_this = $current_filter_values;
                unset($without_this[$this->getKey()][$key]);

                $parts[] = [
                    'title' => $structure[$key]['title'],
                    'value' => $value,
                    'href_clean' => \RS\Http\Request::commonInstance()->replaceKey([$this->wrap_var => $without_this]) //Url, для очистки данной части фильтра
                ];
            }
        }
        return $parts;
    }
}