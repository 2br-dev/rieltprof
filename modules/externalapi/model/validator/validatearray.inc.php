<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\Validator;
use \ExternalApi\Model\Exception as ApiException;

/**
* Класс содержит функции валидации структуры массива
*/
class ValidateArray
{
    protected 
        $schema;
    
    /**
    * Конструктор валидатора
    * 
    * @param array $schema Массив с описание эталонной структуры данных
    * Пример:
    * array(
    *     '@validate' => function($value, $all_parameters) {}
    *     'fields' => array(
    *         '@title' => '...',
    *         '@validate' => '....',
    * 
    *         'status' => array(
    *             '@title' => t('ID статуса'),
    *             '@type' => 'integer',
    *             '@validate_callback' => function($value) {
    *             }
    *         ),
    *         'payment' => array(
    *             '@title' => t('ID способа оплаты'),
    *             '@type' => 'integer',
    *             '@validate_callback' => function($value) {
    *             }
    *         ),
    *         'is_payed' => array(
    *             '@title' => t('Флаг оплаты'),
    *             '@type' => 'integer',
    *             '@allowable_values' => array(1,0)
    *         ),
    *         'courier_id' => array(
    *             '@title' => t('ID курьера'),
    *             '@type' => 'integer',
    *             '@validate_callback' => function($value) {
    *             }
    *         )
    *     ),
    *     'remove_items' => array(
    *         '@title' => t('Уникальные коды удаляемых из заказа товаров'),
    *         '@type' => 'array',
    *         '@arrayitemtype' => 'string',
    *     )
    * );
    * 
    */
    function __construct(array $schema)
    {
        $this->schema = $schema;
    }
    
    /**
    * Производит валидацию данных
    * 
    * @param string $param_name
    * @param array $param_value
    */
    function validate($param_name, $param_value, $full_data)
    {
        if (!is_array($param_value)) {
            throw new ApiException(t('Параметр %0 должен быть массивом', [$param_name]), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
        
        return $this->recursiveValidation($this->schema, $param_name, $param_value, $full_data);
    }
    
    /**
    * Рекурсивно сверяет структуру $param_value со $schema
    * 
    * @param array $schema - результат выполнения метода $this->getUpdateDataScheme()
    * @param string $param_name - дмя переменной
    * @param array $param_value - данные для обновления
    * @param array $full_data - полные данные
    * @param array $path - параметр для внутренних нужд рекурсии
    * 
    * @throws \ExternalApi\Model\Exception
    * @return Возвращает $param_value с приведенными к нужным типам значения
    */
    protected function recursiveValidation($schema, $param_name, $param_value, $full_data, $path = [])
    {
        if ($full_data === null) {
            $full_data = $param_value;
        }
        $cur_path = implode(' > ', $path);
        
        foreach($schema as $key => $value) {

            //Запускаем callback для общей валидации
            if (!$path && $key == '@validate_callback') {
                $validate_result = call_user_func($value, $param_value, $full_data);
                if ($validate_result === false) {
                    throw new ApiException(t('Недопустимое значение в переменной %1', [$param_name]), ApiException::ERROR_WRONG_PARAM_VALUE);
                }
                
                if (is_string($validate_result)) {
                    throw new ApiException($validate_result, ApiException::ERROR_WRONG_PARAM_VALUE);
                }
            }
            
            if ($key[0] == '@') continue;
            
            $var_path = $cur_path.' > '.$key;
            
            if (is_array($value)) {
                //Проверяем текущую ветку
                $param_value = $this->validateBranch($key, $schema, $param_name, $param_value, $var_path, $full_data);
                
                //Переходим глубже
                $next_param_value = isset($param_value[$key]) ? $param_value[$key] : null;
                $result = $this->recursiveValidation($value, $param_name, $next_param_value, $full_data, array_merge($path, [$key]));
                if ($next_param_value !== null) {
                    $param_value[$key] = $result;
                }
                
            } else {
                throw new ApiException(t('Некорректная схема валидации параметра %0. Ожидался массив в ключе %1', [$param_name, $var_path]), ApiException::ERROR_INSIDE);
            }
        }                   
        
        //Проверяем на наличие лишних элементов
        if (is_array($param_value)) {
            $allowable_keys = [];
            foreach($schema as $subkey => $value) {
                if ($subkey[0] == '@') continue;
                $allowable_keys[$subkey] = $subkey;
            }
            if ($allowable_keys) {
                //Если не перечислены ключи, значит можно передавать все что угодно
                $extra_keys = array_keys(array_diff_key($param_value, $allowable_keys));
                if ($extra_keys) {
                    throw new ApiException(t("Обнаружены лишние элементы массива %0 в ключе '%1' в параметре {$param_name}", [implode(',', $extra_keys), $cur_path]), ApiException::ERROR_WRONG_PARAM_VALUE);
                }
            }
        }
        
        return $param_value;
    }
    
    /**
    * Валидирует одну ветку массива

    * @param mixed $key - текущий ключ массива
    * @param mixed $schema_orig - ветка схемы валидации
    * @param mixed $param_name - имя валидируемого параметра
    * @param mixed $param_value - ветка значения валидируемого параметра
    * @param mixed $cur_path - текущий путь валидации от корня массива
    * @param mixed $full_data - все параметры, переданные в метод
    */
    protected function validateBranch($key, $schema_orig, $param_name, $param_value, $cur_path, $full_data)
    {
        $schema = $schema_orig[$key];
        
        //Проверяем обязательные поля
        if (!empty($schema['@require']) && !isset($param_value[$key])) {
            throw new ApiException(t('Не найден ключ %0 в параметре %1', [$cur_path, $param_name]), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
        
        //if (!is_array($param_value)) {
        //    throw new ApiException(t('Ожидался массив в ключе %0 в переменной %1', array($cur_path, $param_name)), ApiException::ERROR_WRONG_PARAM_VALUE);
        //}
        
        //$param_value[$key] = isset($param_value[$key]) ? $param_value[$key] : null;
        
        if (isset($param_value[$key])) {
            //Приводим тип значения к необходимому
            if (isset($schema['@type']) && $schema['@type'] != 'mixed') {
                settype($param_value[$key], $schema['@type']);
            }
            
            //Проверяем на допустимые значения
            if (isset($schema['@allowable_values']) && !in_array($param_value[$key], $schema['@allowable_values'])) {
                throw new ApiException(t('Не допустимое значение в ключе %0 в переменной %1', [$cur_path, $param_name]), ApiException::ERROR_WRONG_PARAM_VALUE);
            }
            
            //Приводим значения элементов массива к необходимому типу
            if (isset($schema['@arrayitemtype'])) {
                foreach($param_value[$key] as $subkey => $value) {
                    settype($value, $schema['@arrayitemtype']);
                    $param_value[$key][$subkey] = $value;
                }
            }
            
            //Запускаем callback для валидации значения
            if (isset($schema['@validate_callback'])) {
                $validate_result = call_user_func($schema['@validate_callback'], $param_value[$key], $full_data);
                if ($validate_result === false) {
                    throw new ApiException(t('Недопустимое значение в ключе %0 в переменной %1', [$cur_path, $param_name]), ApiException::ERROR_WRONG_PARAM_VALUE);
                }
                
                if (is_string($validate_result)) {
                    throw new ApiException($validate_result, ApiException::ERROR_WRONG_PARAM_VALUE);
                }
            }        
        }    
        
        return $param_value;
    }
    
    
    /**
    * Возвращает схему валидации
    * 
    * @return array
    */
    function getSchema()
    {
        return $this->schema;
    }
    
    /**
    * Возвращает информацию о возможных значениях переменной в формате HTML, согласно схеме валидации
    * 
    * @return string
    */
    function getParamInfoHtml()
    {
        //Установим флаг is_node у промежуточных элементов
        $callback = function(&$item, $key) use (&$callback) {
            if (is_array($item)) {
                foreach($item as $k => $v) {
                    if ($k[0] == '@') continue;
                    
                    $item['@is_node'] = true;
                    array_walk($item, $callback);
                    break;
                }
            }
        };
        
        $schema = $this->schema;
        array_walk($schema, $callback);
        
        $view = new \RS\View\Engine();
        $view->assign([
            'data_scheme' => $schema
        ]);
        
        return $view->fetch('%externalapi%/update_data_info.tpl');
    }    
}
