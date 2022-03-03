<?php
function smarty_function_csrf($params, &$smarty)
{
    $code = \RS\Http\Request::commonInstance()->setCsrfProtection($params['form'] ?: '');
    return '<input type="hidden" name="csrf_protection" value="'.$code.'">';
}
