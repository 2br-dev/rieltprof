<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module\AbstractModel;

/**
* Базовый класс модели. Содержит только базовые функции обработки ошибок
*/
abstract class BaseModel
{
    protected
        $default_error_fieldname = 'Информация',
        $form_fieldname = [],
        $errors_by_form = [],
        $errors_non_form = [],
        $errors = [];
        
    /**
    * Добавляет ошибку в список
    * 
    * @param string $message - сообщение об ошибке
    * @param string $fieldname - название поля
    * @param string $form - техническое имя поля (например, атрибут name у input)
    * 
    * @return boolean(false)
    */
    function addError($message, $fieldname = null, $form = null)
    {
        $this->errors[] = $message;
        if ($form === null) {
            $form = $fieldname;
        }
        if ($form !== null) {
            $this->form_fieldname[$form] = $fieldname;
            $this->errors_by_form[$form][] = $message;
        } else {
            $this->errors_non_form[] = $message;
        }        
        return false;
    }
    
    /**
    * Возвращает true, если имеются ошибки
    * 
    * @return bool
    */
    function hasError()
    {
        return !empty($this->errors);
    }

    /**
    * Возвращает полный список ошибок
    * @return array
    */
    function getErrors()
    {
        $err = $this->errors;
        return $err;
    }    
    
    /**
    * Очищает ошибки
    * @return void
    */
    function cleanErrors()
    {
        $this->errors = [];
        $this->errors_by_form = [];
        $this->errors_non_form = [];
        $this->form_fieldname = [];
    }
    
    /**
    * Возвращает информацию об ошибках для отправки браузеру в формате json. 
    * Далее можно использовать JavaSript API, для визуального отображения ошибок
    * @return array
    */
    function getDisplayErrors()
    {
        $errors = [];
        
        if (count($this->errors_non_form)) {
            $errors['@system'] = [
                'class' => 'system',
                'fieldname' => t($this->default_error_fieldname),
                'errors' => $this->errors_non_form
            ];
        }
        
        foreach($this->errors_by_form as $key => $error_list) {
            $errors[$key] = [
                'class' => 'field',
                'fieldname' => $this->form_fieldname[$key],
                'errors' => $error_list
            ];
        }
        return $errors;        
    }    

    /**
    * Возвращает ошибки в виде строки
    * 
    * @return string
    */
    function getErrorsStr()
    {
        return implode(', ', $this->getErrors());
    }
    
    /**
    * Возвращает ошибки формы
    * 
    * @param string $form - имя формы
    * @param mixed $separator - разделитель, если задано false, то будет возвращен array, иначе строка со всеми ошибками.
    * @return string | array
    */
    function getFormErrors($form, $separator = ', ')
    {
        if (isset($this->errors_by_form[$form])) {
            return $separator ? implode($separator, $this->errors_by_form[$form]) : $this->errors_by_form[$form];
        }
        return [];
    }
    
    /**
    * Возвращает ошибки, не связанные с формами
    */
    function getNonFormErrors($separator = ', ')
    {
        return $separator ? implode($separator, $this->errors_non_form) : $this->errors_non_form;
    }

    /**
     * Экспортирует все данные по ошибкам
     *
     * @return array
     */
    function exportErrors()
    {
        return [
            'form_fieldname' => $this->form_fieldname,
            'errors_by_form' => $this->errors_by_form,
            'errors_non_form' => $this->errors_non_form,
            'errors' => $this->errors
        ];
    }

    /**
     * Импортирует все данные по ошибкам
     *
     * @param $data
     */
    function importErrors($data)
    {
        if (isset($data['form_fieldname']))
            $this->form_fieldname = $data['form_fieldname'];

        if (isset($data['errors_by_form']))
            $this->errors_by_form = $data['errors_by_form'];

        if (isset($data['errors_non_form']))
            $this->errors_non_form = $data['errors_non_form'];

        if (isset($data['errors']))
            $this->errors = $data['errors'];
    }
}