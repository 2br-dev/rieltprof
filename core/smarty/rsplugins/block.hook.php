<?php
/**
* Smarty плагин, добавляет возможность использования хуков в шаблоне
*/
function smarty_block_hook($params, $content, $template, &$repeat)
{
    if (!$repeat) {
        //Запишем, какие были вызваны хуки в шаблоне
        $hook_params = array_diff_key($params, ['name' => null]);
        $template->smarty->called_hooks[$params['name']] = $hook_params;
        
        if (isset($params['name'])) {
            $content = $template->smarty->hooks->callHook($params['name'], $hook_params, $content);
        } else {
            trigger_error(t('Не задан обязательный параметр "name"'), E_USER_NOTICE);
        }
    }
    
    return $content;
}  

