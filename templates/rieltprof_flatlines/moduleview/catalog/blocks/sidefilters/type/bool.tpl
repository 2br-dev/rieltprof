{* Шаблон для фильтра с типом - Да/Нет *}

<div class="filter {if $filters[$prop.id] || $prop.is_expanded}open{/if}">
    <a class="expand">
        <span class="right-arrow"><i class="pe-2x pe-7s-angle-down-circle"></i></span>
        <span>{$prop.title}</span>
    </a>
    <div class="detail">
        <select class="select yesno" name="pf[{$prop.id}]" data-start-value="">
            <option value="">{t}Неважно{/t}</option>
            <option value="1" {if $filters[$prop.id] == '1'}selected{/if}>{t}Есть{/t}</option>
            <option value="0" {if $filters[$prop.id] == '0'}selected{/if}>{t}Нет{/t}</option>
        </select>
    </div>
</div>