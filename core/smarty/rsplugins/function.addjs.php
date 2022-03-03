<?php
function smarty_function_addjs($params, &$smarty)
{
    if (!isset($params['file'])) {
        trigger_error("addJs: param 'file' not found", E_USER_NOTICE);
        return;
    }    
    
    if (!isset($params['name'])) $params['name'] = $params['file'];
    //if (!isset($params['from_root'])) $params['from_root'] = false;
    if (!isset($params['basepath'])) $params['basepath'] = null;
    if (!isset($params['no_compress'])) $params['no_compress'] = false;

    $file_params = array_diff_key($params, array_flip(['file','name','basepath','no_compress']));
    
    \RS\Application\Application::getInstance()->addJs($params['file'], $params['name'], $params['basepath'], $params['no_compress'], $file_params);
}
