<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifier
 */

/**
 * Объединяет массив в строк через разделитель
 *
 * Type:     modifier<br>
 * Name:     join<br>
 *
 * @param string $string исходная строка
 * @param string $glue  разделитель
 *
 * @return string
 */
function smarty_modifier_join($string, $glue = '')
{
    return implode($glue, $string);
}
