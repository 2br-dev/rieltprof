<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Captcha;

/**
* Заглушка капчи, применяется если капча не используется
*/
class Stub extends AbstractCaptcha
{
    /**
    * Возвращает идентификатор класса капчи
    * 
    * @return string
    */
    function getShortName()
    {
        return 'stub';
    }
    
    /**
    * Возвращает название класса капчи
    * 
    * @return string
    */
    function getTitle()
    {
        return t('- Не использовать капчу -');
    }
    
    /**
    * Возвращает название поля для клиентских форм
    * 
    * @return string
    */
    function getFieldTitle()
    {
        return '';
    }
    
    /**
    * Возвращает HTML капчи
    * 
    * @param string $name - атрибут name для шаблона отображения
    * @param string $context - контекст капчи
    * @param array $attributes - дополнительные атрибуты для Dom элемента капчи
    * @param array|null $view_options - параметры отображения формы. если null, то отображать все
    *     Возможные элементы массива:
    *         'form' - форма,
    *         'error' - блок с ошибками,
    *         'hint' - ярлык с подсказкой,
    * @param string $template - используемый шаблон
    * 
    * @return string
    */
    function getView($name, $context = null, $attributes = [], $view_options = null, $template = null)
    {
        return '';
    }
    
    /**
    * Проверяет правильность заполнения капчи
    * 
    * @param mixed $data - данные для проверки
    * @param string $context - контекст капчи
    * @return bool
    */
    function check($data, $context = null)
    {
        return true;
    }
}