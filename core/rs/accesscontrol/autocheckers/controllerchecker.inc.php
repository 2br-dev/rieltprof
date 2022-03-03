<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\AccessControl\AutoCheckers;

use RS\AccessControl\Rights;
use RS\Http\Request as HttpRequest;

/**
* Объект для автоматической проверки прав
*/
class ControllerChecker implements AutoCheckerInterface
{
    protected
        $controller_mask,
        $method,
        $action,
        $request_params,
        $right,
        $ignore_missing_rights;

    /**
     * ControllerChecker конструктор.
     *
     * @param string $controller_mask - регулярное выражение описывающее имя контроллера
     * @param string|string[] $method - список методов HTTP, значение '*' применяет проверку для любого метода
     * @param string|string[] $action - список действий контроллера, значение '*' применяет проверку для любого действия
     * @param array $request_params - параметры запроса, например:
     *  array(
     *      GET => array(
     *          'param_name' => array(value1, value2),
     *      ),
     *  )
     * @param string $right - идентификатор проверяемого права
     * @param bool $ignore_missing_rights - не считать ошибкой отсутствие в модуле проверяемого права
     */
    public function __construct($controller_mask, $method, $action, $request_params, $right, $ignore_missing_rights = false)
    {
        $this->controller_mask = $controller_mask;
        $this->method = array_map('strtolower', (array) $method);
        $this->action = array_map('strtolower', (array) $action);
        $this->request_params = $request_params;
        $this->right = $right;
        $this->ignore_missing_rights = $ignore_missing_rights;
    }
    
    /**
    * Если условия проверки соблюдены - проверят наличие права
    * Возвращает текст ошибки или false
    * 
    * @param array $params - параметры для проверки - [
    *       'controller': (\RS\Controller\AbstractModule) - объект контроллера
    *   ]
    * @return string|false
    */
    public function checkError($params)
    {
        $controller = $params['controller'];

        $controller_match = preg_match('/'.$this->controller_mask.'/i', $controller->getUrlName());
        $method_match = (in_array('*', $this->method) || in_array(strtolower(HttpRequest::commonInstance()->getMethod()), $this->method));
        $action_match = (in_array('*', $this->action) || in_array(strtolower($controller->getAction()), $this->action)); 
        $request_params_match = true;
        foreach ($this->request_params as $storage_key=>$params) {
            $storage = HttpRequest::commonInstance()->getSource($storage_key);
            foreach ($params as $key=>$value) {
                if (isset($storage[$key]) && !in_array($storage[$key], (array) $value)) {
                    $request_params_match = false;
                }
            }
        }
        
        if ($controller_match && $method_match && $action_match && $request_params_match) {
            return Rights::CheckRightError($controller, $this->right, $this->ignore_missing_rights);
        }
        return false;
    }

    /**
     * Возвращает тип объекта автоматической проверки прав
     *
     * @return string
     */
    public static function getCheckerType()
    {
        return 'controller';
    }
}
