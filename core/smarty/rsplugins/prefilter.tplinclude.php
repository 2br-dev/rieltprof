<?php
/**
* Префильтр, заменяющий %wrapper% - на {include file=$wrapper}
* необходим для облегчения работы с шаблонами
*/
function smarty_prefilter_tplinclude($source, &$smarty)
{
    $source = preg_replace('/%wrapper%/ui','{if !empty($tpl_wrapper)}{include file=$tpl_wrapper}{/if}', $source);
    $source = preg_replace('/%content%/ui','{if !empty($tpl_content)}{include file=$tpl_content}{/if}', $source);
    return $source;
}

