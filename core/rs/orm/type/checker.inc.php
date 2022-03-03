<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Type;

use RS\Captcha\Manager as CaptchaManager;
use RS\Orm\AbstractObject;

/**
* Класс валидации форм
* Каждая функция валидации возвращает либо true либо текст ошибки
*/
class Checker
{
    const CHECK_ALIAS = 'chkAlias';
    const CHECK_EMPTY = 'chkEmpty';
    const CHECK_PATTERN = 'chkPattern';
    const CHECK_CAPTCHA = 'chkCaptcha';
    const CHECK_NO_SELECT = 'chkNoselect';
    const CHECK_MIN_MAX = 'chkMinmax';
    const CHECK_EMAIL = 'chkEmail';

    /**
    * Проверяет на валидность значения поля "псевдоним". Ожидается, что данное значение должно строить ЧПУ
    * 
    * @param AbstractObject $orm_object - объект, который валидируется
    * @param mixed $value - значение для проверки
    * @param string $errtext - текст с ошибкой
    * @return bool(true) | string возвращает true в случае успеха или текст ошибки
    */
    public static function chkAlias($orm_object, $value, $errtext)
    {
        if ($errtext === null) $errtext = t('Название для ссылок должно состоять только из цифр, английских букв и символов тире, подчеркивание');
        if (!@preg_match('/^([a-zA-Z0-9\-_,.])*$/',$value)) return $errtext;
        return true;
    }

    /**
    * Возвращает true, если проверяемое значение не пустое. 0 - также считается пустым значением
    * 
    * @param AbstractObject $orm_object - объект, который валидируется
    * @param mixed $value - значение для проверки
    * @param string $errtext - текст с ошибкой
    * @return bool(true) | string возвращает true в случае успеха или текст ошибки
    */    
    public static function chkEmpty($orm_object, $value, $errtext)
    {
        
        if (is_string($value)) {
            $value = trim($value);
        }
        if (empty($value)) return $errtext;
        return true;
    }

    /**
    * Возвращает true, если проверяемое значение соответствует заданному регулярному выражению
    * 
    * @param AbstractObject $orm_object - объект, который валидируется
    * @param mixed $value - значение для проверки
    * @param string $errtext - текст с ошибкой
    * @param string $pattern - регулярное выражение
    * @return bool(true) | string возвращает true в случае успеха или текст ошибки
    */    
    public static function chkPattern($orm_object, $value, $errtext, $pattern)
    {
        if (!@preg_match($pattern, $value)) return $errtext;
        return true;
    }
    
    /**
    * Возвращает true, если проверяемое значение соответствует значению последней отображенной капчи
    * 
    * @param AbstractObject $orm_object - объект, который валидируется
    * @param mixed $value - значение для проверки
    * @param string $context - контекст капчи
    * @param string $errtext - текст с ошибкой
    * @return bool(true) | string возвращает true в случае успеха или текст ошибки
    */        
    public static function chkCaptcha($orm_object, $value, $context, $errtext)
    {
        if (CaptchaManager::currentCaptcha()->check($value, $context)) {
            return true;
        } else {
            return $errtext;
        }
    }

    /**
    * Возвращает true, если значение не равно -1. Удобно использовать для проверки списков на поле "не выбрано"
    * 
    * @param AbstractObject $orm_object - объект, который валидируется
    * @param mixed $value - значение для проверки
    * @param string $errtext - текст с ошибкой
    * @return bool(true) | string возвращает true в случае успеха или текст ошибки
    */
    public static function chkNoselect($orm_object, $value, $errtext)
    {
        if (!empty($value) && $value==-1) return $errtext;
        return true;
    }

    /**
    * Возвращает true, если min <= значение <= max. Для числовых полей
    * 
    * @param AbstractObject $orm_object - объект, который валидируется
    * @param mixed $value - значение для проверки
    * @param string $errtext - текст с ошибкой
    * @return bool(true) | string возвращает true в случае успеха или текст ошибки
    */            
    public static function chkMinmax($orm_object, $value, $errtext, $min, $max)
    {
        if ($value < $min || $value > $max) return $errtext;
        return true;
    }

    /**
    * Проверяет на валидность поле email
    * 
    * @param AbstractObject $orm_object - объект, который валидируется
    * @param mixed $value - значение для проверки
    * @param string $errtext - текст с ошибкой
    * @return bool(true) | string возвращает true в случае успеха или текст ошибки
    */     
    public static function chkEmail($orm_object, $value, $errtext)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) return $errtext;
        return true;
    }

    /**
     * Проверяет на валидность номер телефона
     * Успешно проходя валидацию телефоны вида:
     *   +79261234567
     *   89261234567
     *   79261234567
     *   +7 926 123 45 67
     *   8(926)123-45-67
     *   123-45-67
     *   9261234567
     *   79261234567
     *   (495)1234567
     *   (495) 123 45 67
     *   89261234567
     *   8-926-123-45-67
     *   8 927 1234 234
     *   8 927 12 12 888
     *   8 927 12 555 12
     *   8 927 123 8 123
     *
     * @param AbstractObject $orm_object - объект, который валидируется
     * @param mixed $value - значение для проверки
     * @param string $errtext - текст с ошибкой
     * @return bool(true) | string возвращает true в случае успеха или текст ошибки
     */
    public static function chkPhone($orm_object, $value, $errtext = null)
    {
        if ($errtext === null) {
            $errtext = t('Неверно указан номер телефона');
        }

        if (!preg_match('/^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){10,14}(\s*)?$/', $value)) {
            return $errtext;
        }
        return true;
    }
}
