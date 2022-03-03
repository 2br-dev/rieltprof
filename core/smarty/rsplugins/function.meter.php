<?php
function smarty_function_meter($params, &$smarty)
{
    $number = \Main\Model\NoticeSystem\Meter::getInstance()->getNumber($params['key']) ?: 0;
    $visible_number = $number > 99 ? '99+' : $number;
    $visible_class = $number > 0 ? 'visible' : '';
    if (empty($params['key'])) {
        $visible_class .= ' rs-meter-node';
    }

    return '<i class="hi-count '.$visible_class.' '.$params['class'].'" data-meter="'.$params['key'].'" data-number="'.$number.'">'.$visible_number.'</i>';
}