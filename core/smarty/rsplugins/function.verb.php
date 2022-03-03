<?php
function smarty_function_verb($params, &$smarty)
{
    if (!isset($params['item'])) {
        trigger_error("verb: param 'item' not found", E_USER_NOTICE);
        return;
    }    
    
    if (!isset($params['values'])) {
        trigger_error("verb: param 'item' not found", E_USER_NOTICE);
        return;
    }
    
    $onlyword = isset($params['onlyword']);
    $words = preg_split('/[,]/', $params['values'], -1, PREG_SPLIT_NO_EMPTY);
    
    if (count($words) != 3) {
        trigger_error("param 'values' should contain 3 words, separated by comma (,)", E_USER_NOTICE);
        return;
    }
    
    $word = \RS\Helper\Tools::verb($params['item'], $words[0], $words[1], $words[2]);
    
    return ($onlyword) ? $word : $params['item'].' '.$word;
}
