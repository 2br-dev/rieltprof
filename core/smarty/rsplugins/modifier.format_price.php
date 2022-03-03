<?php
/**
* Меняет формат числа, на отображения формата в цену
* Например:
* 18770 меняет на 18 770
* или
* 18 770 руб. если указана валюта
* 
* @param string|integer $text - число для преобразования
* @param string $currency_liter - валюта
*/
function smarty_modifier_format_price($text, $currency_liter = null)
{
    return \RS\Helper\CustomView::cost($text, $currency_liter);
}  
