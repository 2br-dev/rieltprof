<?php
function smarty_block_t($params, $content, $template, &$repeat)
{
    if (is_null($content)) {
        return;
    }
    
    if (isset($params['context'])) {
        $context = $params['context'];
        $content .= '^'.$context;
        unset($params['context']);
    }
    
    if (isset($params['alias'])) {
        $alias = $params['alias'];
        unset($params['alias']);
    } else {
        $alias = null;
    }
    
    return t($content, $params, $alias);
}  

