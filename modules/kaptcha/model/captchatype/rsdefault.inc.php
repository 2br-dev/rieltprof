<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Kaptcha\Model\CaptchaType;

/**
* Базовая капча ReadyScript
* Картинка с цифрами и поле для ввода
*/
class RSDefault extends \RS\Captcha\AbstractCaptcha
{
    const
        SESSION_VAR = 'kaptcha_keystring-', // ключ сессии - код проверки
        SESSION_CHECKCOUNT_VAR = 'kaptcha_keystring_checkcount-', // ключ сессии - количество неудачных попыток
        DEFAULT_TEMPLATE = '%kaptcha%form/rs_default.tpl', // шаблон по умолчанию
        CHECK_LIMIT = 5; // количество попыток на один код
    
    /**
    * Возвращает идентификатор класса капчи
    * 
    * @return string
    */
    function getShortName()
    {
        return 'RS-default';
    }
    
    /**
    * Возвращает название класса капчи
    * 
    * @return string
    */
    function getTitle()
    {
        return 'ReadyScript "Стандарт"';
    }
    
    /**
    * Возвращает название поля для клиентских форм
    * 
    * @return string
    */
    function getFieldTitle()
    {
        return t('Защитный код');
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
        if ($template === null) {
            $template = self::DEFAULT_TEMPLATE;
        }
        $view = new \RS\View\Engine();
        $view->assign([
            'name' => $name,
            'context' => $context,
            'attributes' => $this->getReadyAttributes($attributes),
            'view_options' => $view_options,
        ]);
        return $view->fetch($template);
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
        if (\Setup::$DISABLE_CAPTCHA) {
            return true;
        }

        $result = false;
        $session_var = $this->getSessionVarKey($context);
        $session_checkcount_var = $this->getSessionVarKey($context, self::SESSION_CHECKCOUNT_VAR);
        
        if (isset($_SESSION[$session_var])) {
            if (!empty($_SESSION[$session_var]) && (strcmp($data, $_SESSION[$session_var]) == 0)) {
                $result = true;
            } else {
                @$_SESSION[$session_checkcount_var]++;
                if ($_SESSION[$session_checkcount_var] > self::CHECK_LIMIT) {
                    unset($_SESSION[$session_var]);
                    $_SESSION[$session_checkcount_var] = 0;
                }
            }
        }
        return $result;
    }
    
    /**
    * Возвращает текст ошибки
    * 
    * @return string
    */
    function errorText()
    {
        return t('Неверно указан код');
    }
    
    /**
    * Возврщает код проверки капчи из сессии
    * 
    * @param string $context - контекст капчи
    * @return string
    */
    protected function getKeyString($context = null)
    {
        return $_SESSION[self::getSessionVarKey($context)];
    }

    /**
    * Устанавливает код проверки капчи в сессию
    * 
    * @param string $str - код проверки
    * @param string $context - контекст капчи
    */
    protected function setKeyString($str, $context = null)
    {
        $_SESSION[self::getSessionVarKey($context)] = $str;
    }

    /**
    * Возвращает ключ переменной сесессии
    * 
    * @param string $context - контекст капчи
    * @param bool $type - имя ключа
    */
    protected function getSessionVarKey($context = null, $type = self::SESSION_VAR)
    {
        return $type . $context;
    }
    
    /**
    * Генерирует картинку капчи
    */
    function actionImage()
    {
        $context = \RS\Http\Request::commonInstance()->request('context', TYPE_STRING);
        new RSDefault\Img($this->getSessionVarKey($context));
    }
    
    /**
    * Действие по умолчанию вызывает генерацию картинки (для совместимости)
    */
    function actionDefault()
    {
        return $this->actionImage();
    }
}