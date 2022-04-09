{$type = $cell->getRow()->getTypeObject()}
<a class="type zmdi {$type->getIconClass()}" title="{t title=$type->getTitle()}Тип:%title{/t}">&nbsp;</a>
<a href="{adminUrl do="edit" id=$cell->getRow('id')}" class="edit crud-edit{if !$cell->getRow('public')} c-gray{/if}" title="{t}Нажмите, чтобы отредактировать{/t}">{$cell->getValue()}</a>
