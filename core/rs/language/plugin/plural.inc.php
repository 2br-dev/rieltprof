<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Language\Plugin;
/**
* Обеспечивает корректное отображение слова во множественном числе
*/
class Plural implements PluginInterface
{
    
    public function process($param_value, $value, $params, $lang)
    {
        $values = explode('|', $value);
        if ($lang == 'ru') {
            list($first, $second, $five) = $values;
            
            $prepare = abs( intval( $param_value ) );
            if( $prepare !== 0 ) 
            {
                if( ( $prepare - $prepare % 10 ) / 10 == 1 ) {
                    $result = $five;
                } else {
                    $prepare = $prepare % 10;
                    if( $prepare == 1 ) {
                        $result = $first;
                    } elseif( $prepare > 1 && $prepare < 5 ) {
                        $result = $second;
                    } else {
                        $result = $five;
                    }
                }
            }
            else {
                $result = $five;
            }
        } elseif ($lang == 'en') {
            $result = $param_value == 1 ? $values[0] : $values[1];
        } else {
            $result = $values[0];
        }
        return $result;
    }
}

