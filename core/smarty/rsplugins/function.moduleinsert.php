<?php

/**
* Плагин смарти для вставки контроллера
* 
* @param array $params                      - параметры
* @param Smarty_Internal_Template $template - объект шаблона
* @param string $filepath                   - прямой путь к шаблону
*/
function smarty_function_moduleinsert($params, $template, $filepath = null)
{
    static $block_iterator = [];

    if (!isset($params['name'])) {
        trigger_error("moduleinsert: param 'name' not found", E_USER_NOTICE);
        return;
    }

    if (isset($params['_params_array'])) { //Для загрузки параметров из массива
        $params += $params['_params_array'];
        unset($params['_params_array']);
    }
    $controller_name = trim($params['name'], '\\');
    $context = \RS\Theme\Manager::getCurrentTheme('blocks_context'); //Текущий контекст темы оформления

    if (empty($params['generate_by_grid'])) { //Данный флаг приходит только, если вставка блока происходит через конструктор сайта
        $params['generate_by_template'] = 1; //Флаг о том, что блок был вставлен в шаблоне через конструкцию {moduleinsert}

        //Формируем _block_id
        if (!isset($params[\RS\Controller\Block::BLOCK_ID_PARAM])) {
            if (!isset($block_iterator[$filepath.$controller_name])) {
                $block_iterator[$filepath.$controller_name] = 1;
            } else {
                $block_iterator[$filepath.$controller_name]++;
            }
            //принимаем за block_id - полный путь к шаблону и порядковый номер блока в шаблоне

            $block_id = $block_iterator[$filepath.$controller_name];

            $params[\RS\Controller\Block::BLOCK_ID_PARAM]   = $block_id;
            $params[\RS\Controller\Block::BLOCK_NUM_PARAM]  = $block_iterator[$filepath.$controller_name];
        }

        $params[\RS\Controller\Block::BLOCK_PATH_PARAM] = $filepath;
        $params[\RS\Controller\Block::BLOCK_INSERT_CONTEXT] = $context;
        $params['skip_admin_zone'] = 1;

        //Создаем относительный путь из абсолютного
        $relative_filepath = \Setup::$ENABLE_OLD_STYLE_BLOCK_ID ?
            $filepath : str_replace(['\\', \Setup::$PATH], ['/', ''], $filepath);

        $params[\RS\Controller\Block::BLOCK_ID_PARAM] = crc32(
            "{$relative_filepath}_{$controller_name}_{$context}_".$params[\RS\Controller\Block::BLOCK_ID_PARAM]);
    }

    //Записывам переменные, которые присутствовали в модуле в шаблон
    if (!empty($params['var'])) {
        $need_assign_var = $params['var'];
        unset($params['var']);
    }

    $mod_param = $params;
    unset($mod_param['name']);
    
    $vars = [];
    $result = \RS\Application\Block\Template::insert($params['name'], $mod_param, $vars);
    
    if (isset($need_assign_var)) {
        $template->assign($need_assign_var, $vars);
    }
    return $result;
}
