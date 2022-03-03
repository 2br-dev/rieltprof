<?php
/**
* Плагин подключает шаблон, если он существует
*/
function smarty_function_tryinclude($params, &$smarty)
{
    if (!isset($params['file'])) {
        trigger_error("file: param 'file' not found", E_USER_NOTICE);
        return;
    }    
    
    if ($smarty->templateExists($params['file'])) {
        return $smarty->fetch($params['file']);
    }
}
