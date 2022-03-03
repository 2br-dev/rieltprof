<?php
/**
* Расширенный плагин форматирования даты
*/
function smarty_modifier_dateformat($string, $format = "@date", $default_date = '',$formatter='auto')
{

    /**
    * Include the {@link shared.make_timestamp.php} plugin
    */
    require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');
    
    if ($string != '') {
        $timestamp = smarty_make_timestamp($string);
    } elseif ($default_date != '') {
        $timestamp = smarty_make_timestamp($default_date);
    } else {
        return;
    } 
    
    if($formatter=='strftime'||($formatter=='auto' && (strpos($format,'%')!==false ||  strpos($format, '@')!==false ) )) {
        $format = \RS\Helper\Tools::dateExtend($format, $timestamp);        
        
        if (DS == '\\') {
            $_win_from = ['%D', '%h', '%n', '%r', '%R', '%t', '%T'];
            $_win_to = ['%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S'];
            if (strpos($format, '%e') !== false) {
                $_win_from[] = '%e';
                $_win_to[] = sprintf('%\' 2d', date('j', $timestamp));
            } 
            if (strpos($format, '%l') !== false) {
                $_win_from[] = '%l';
                $_win_to[] = sprintf('%\' 2d', date('h', $timestamp));
            } 
            $format = str_replace($_win_from, $_win_to, $format);
        } 
        
        return strftime($format, $timestamp);
    } else {
        return date($format, $timestamp);
    }
} 

