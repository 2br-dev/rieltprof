<?php
/**
* Расширение, добавленно автором CMS.
* Вставляет модуль в шаблон
* 
* @param mixed $params
* @param mixed $smarty
*/
function smarty_function_static_call($params, &$smarty)
{
    static $block_iterator = [];
    
    if (!isset($params['var'])) {
        trigger_error("static_call: param 'var' not found", E_USER_NOTICE);
        return;
    }
    if (!isset($params['callback'])) {
        trigger_error("static_call: param 'callback' not found", E_USER_NOTICE);
        return;
    }
    
    $callback_params = isset($params['params']) ? (array)$params['params'] : [];
    $result = call_user_func_array($params['callback'], $callback_params);
    
    $smarty->assign($params['var'], $result);
    return;
}

