<div class="property-type-big-list_list">
    {if $list}
        {foreach $list as $key => $item}
            <span class="property-type-big-list_list-item">
                <label>
                    <input type="checkbox" class="property-type-big-list_list-item-checkbox" data-id="{$key}" {if $disabled}disabled{/if} {if isset($selected_values[$key])}checked{/if}>
                    <span class="property-type-big-list_list-item-value">{$item}</span>
                </label>
            </span>
        {/foreach}
    {else}
        <span class="property-type-big-list_list-item">- {t}Значений не найдено{/t} -</span>
    {/if}
</div>
<div class="property-type-big-list_list-paginator paginator">
    <span class="text hidden-xs">{t}страница{/t}</span>
    <i class="property-type-big-list_list-paginator-prev prev zmdi zmdi-chevron-left" title="{t}предыдущая страница{/t}"></i>
    <input class="property-type-big-list_list-paginator-page page" value="{$page}" data-max="{$count_pages}" >
    <i class="property-type-big-list_list-paginator-next next zmdi zmdi-chevron-right" title="{t}следующая страница{/t}"></i>
    <span class="text">{t}из{/t} {$count_pages}</span>
    <span class="total">{t}всего значений{/t}: <span class="total_value">{$count_values}</span></span>
</div>