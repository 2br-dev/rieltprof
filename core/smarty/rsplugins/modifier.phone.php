<?php
/**
 * Оборачивает номер телефона ссылкой, позволяющей позвонить на данный номер
 *
 * @param $text
 * @return mixed
 */
function smarty_modifier_phone($text)
{
    if (\RS\Module\Manager::staticModuleExists('crm')
        && \RS\Config\Loader::byModule('crm')->tel_active_provider)
    {
        $href = \Crm\Model\Telephony\Manager::getCallUrl($text);
    } else {
        $href = 'tel:'.$text;
    }

    if (!preg_match('/^tel:/', $href)) {
        $class = 'class="crud-get"';
    } else {
        $class = '';
    }

    return '<a href="'.$href.'" '.$class.'>'.$text.'</a>';
}
