{if $cell->getRow('typelink')=='link'}<a href="{$cell->getRow('link')}" target="_blank" title="{t}Тип: ссылка{/t}" class="type {$cell->getRow('typelink')}">&nbsp;</a>{/if}
{if $cell->getRow('typelink')=='article'}<a href="{adminUrl do="edit" id=$cell->getRow('id')}" title="{t}Тип: статья{/t}" class="crud-edit type {$cell->getRow('typelink')}">&nbsp;</a>{/if}
{if $cell->getRow('typelink')=='empty'}<a class="type {$cell->getRow('typelink')}" title="{t}Тип:шаблон{/t}">&nbsp;</a>{/if}

<a href="{adminUrl do="edit" id=$cell->getRow('id')}" class="edit crud-edit{if !$cell->getRow('public')} c-gray{/if}" title="{t}Нажмите, чтобы отредактировать{/t}">{$cell->getValue()}</a>
