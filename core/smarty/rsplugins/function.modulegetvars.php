<?php
/**
* Расширение, добавленно автором CMS.
* Вставляет модуль в шаблон
* 
* @param mixed $params
* @param mixed $smarty
*/
function smarty_function_modulegetvars($params, &$smarty)
{
    static $block_iterator = [];
    
    if (!isset($params['name'])) {
        trigger_error("modulegetvars: param 'name' not found", E_USER_NOTICE);
        return;
    }
    if (!isset($params['var'])) {
        trigger_error("modulegetvars: param 'var' not found", E_USER_NOTICE);
        return;
    }

    //Формируем _block_id
    if (!isset($params[\RS\Controller\Block::BLOCK_ID_PARAM])) {
        if (!isset($block_iterator[$smarty->source->filepath])) {
            $block_iterator[$smarty->source->filepath] = 1;
        } else {
            $block_iterator[$smarty->source->filepath]++;
        }
        //принимаем за block_id - полный путь к шаблону и порядковый номер блока в шаблоне
        $params[\RS\Controller\Block::BLOCK_ID_PARAM] = crc32($smarty->source->filepath.$block_iterator[$smarty->source->filepath]);
    }
    
    $mod_param = $params;
    unset($mod_param['name']);
    unset($mod_param['var']);
    
    $smarty->assign($params['var'], \RS\Application\Block\Template::getVariable($params['name'], $mod_param));
    return;
}

