<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_options} function plugin
 *
 * Type:     function<br>
 * Name:     html_options<br>
 * Input:<br>
 *           - name       (optional) - string default "select"
 *           - values     (required if no options supplied) - array
 *           - options    (required if no values supplied) - associative array
 *           - selected   (optional) - string default not set
 *           - output     (required if not options supplied) - array
 * Purpose:  Prints the list of <option> tags generated from
 *           the passed parameters
 * @link http://smarty.php.net/manual/en/language.function.html.options.php {html_image}
 *      (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_rshtml_options($params, &$smarty)
{
    require_once(SMARTY_PLUGINS_DIR . 'shared.escape_special_chars.php');
    
    $name = null;
    $values = null;
    $options = null;
    $selected = [];
    $output = null;
    
    //Расширение
    $find_level = isset($params['find_level']) ? $params['find_level'] : str_repeat('&nbsp;',4);
    //Конец расширения
    
    $extra = '';
    
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'name':
                $$_key = (string)$_val;
                break;
            
            case 'options':
                $$_key = (array)$_val;
                break;
                
            case 'values':
            case 'output':
                $$_key = array_values((array)$_val);
                break;

            case 'selected':
                $$_key = array_map('strval', array_values((array)$_val));
                break;
                
            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    trigger_error("html_options: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    if (!isset($options) && !isset($values))
        return ''; /* raise error here? */

    $_html_result = '';
    
    // перемещаем искомые значения в ключи массива, так как функция isset() работает значительно быстрее чем in_array()
    $flipped_selected = array_flip($selected);
    
    if (isset($options)) {
        foreach ($options as $_key=>$_val){
            $_html_result .= smarty_function_rshtml_options_optoutput($_key, $_val, $flipped_selected, $find_level);
        }
    } else {
        foreach ($values as $_i=>$_key) {
            $_val = isset($output[$_i]) ? $output[$_i] : '';
            $_html_result .= smarty_function_rshtml_options_optoutput($_key, $_val, $flipped_selected, $find_level);
        }
    }

    if(!empty($name)) {
        $_html_result = '<select name="' . $name . '"' . $extra . '>' . "\n" . $_html_result . '</select>' . "\n";
    }

    return $_html_result;

}

function smarty_function_rshtml_options_optoutput($key, $value, $selected, $find_level = null) {
    if(!is_array($value)) {
        if (!empty($find_level)) {
            $level = mb_substr_count($value, $find_level);
            $class = ' class="lev_'.$level.'" data-level="'.$level.'"';
        }
        $data_value = !empty($find_level) ? 'data-value="' . smarty_function_escape_special_chars(str_replace($find_level, '', $value)).'"' : '';
        $_html_result = '<option '. $data_value . ' value="' . smarty_function_escape_special_chars($key) . '"';
        if (isset($selected[(string)$key])) {
            $_html_result .= ' selected="selected"';
        }
        $_html_result .= $class.'>' . smarty_function_escape_special_chars($value) . '</option>' . "\n";
    } else {
        $_html_result = smarty_function_rshtml_options_optgroup($key, $value, $selected);
    }
    return $_html_result;
}

function smarty_function_rshtml_options_optgroup($key, $values, $selected) {
    $optgroup_html = '<optgroup label="' . smarty_function_escape_special_chars($key) . '">' . "\n";
    foreach ($values as $key => $value) {
        $optgroup_html .= smarty_function_rshtml_options_optoutput($key, $value, $selected);
    }
    $optgroup_html .= "</optgroup>\n";
    return $optgroup_html;
}

/* vim: set expandtab: */

