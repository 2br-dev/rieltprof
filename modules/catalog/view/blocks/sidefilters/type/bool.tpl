<div class="filter">
    <h4>{$prop.title}:</h4>
    <select class="yesno" name="pf[{$prop.id}]" data-start-value="">
        <option value="">{t}Неважно{/t}</option>
        <option value="1" {if $filters[$prop.id] == '1'}selected{/if}>{t}Есть{/t}</option>
        <option value="0" {if $filters[$prop.id] == '0'}selected{/if}>{t}Нет{/t}</option>
    </select>
</div>