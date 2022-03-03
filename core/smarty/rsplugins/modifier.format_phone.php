<?php
/**
 * Нормализует номер телефона
 *
 * @param $phone - Номер телефона
 * @return string
 */
function smarty_modifier_format_phone($phone)
{
    return \Users\Model\Api::normalizePhoneNumber($phone);
}
