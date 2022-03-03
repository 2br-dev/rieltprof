<?php
/**
* Возвращает URL в административной части
* 
* @param mixed $params
* @param mixed $smarty
*/
function smarty_function_adminUrl($params, &$smarty)
{
    $router = \RS\Router\Manager::obj();
    $mod_controller = isset($params['mod_controller']) ? $params['mod_controller'] : null;
    unset($params['mod_controller']);
    
    $absolute = isset($params['absolute']) ? $params['absolute'] : false;
    unset($params['absolute']);
    
    return $router->getAdminUrl($params['do'] ?: null, $params, $mod_controller, $absolute);
}  

