{* Шаблон для фильтра с типом - строка *}
<div class="filter filter-string {if $filters[$prop.id] || $prop.is_expanded}open{/if}">
    <a class="expand">
        <span class="right-arrow"><i class="pe-2x pe-7s-angle-down-circle"></i></span>
        <span>{$prop.title}</span>
    </a>
    <div class="detail">
        <input type="text" class="string" name="pf[{$prop.id}]" value="{$filters[$prop.id]}" data-start-value="">
    </div>
</div>