<?php
function smarty_function_addmeta($params, &$smarty)
{    
    $key = null;
    if (isset($params['key'])) {
        $key = $params['key'];
        unset($params['key']);
    }
    
    \RS\Application\Application::getInstance()->meta->add($params, $key);
}
