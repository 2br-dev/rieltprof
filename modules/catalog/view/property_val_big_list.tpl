{$page_size = 20}
{$selected_values = $self.available_value_in_string}
{$values = $self->valuesArr()}
{$value = $self.value}
{$search_params = [
    'page_size' => $page_size,
    'prop_id' => $self.id
]}
{$search_url = $router->getAdminUrl('ajaxPropertySearchListValues', $search_params, 'catalog-ctrl')}

<div class="property-type-big-list" data-search-url="{$search_url}" data-disabled="{if $disabled}1{else}0{/if}" data-prop-id="{$self.id}">
    <input type="hidden" name="prop[{$self.id}][value][]" value="">
    <div class="property-type-big-list_hint">{t}Выбранные значения{/t}</div>
    <div class="property-type-big-list_selected">
        {if empty($value)}
            <span class="property-type-big-list_selected-item-stub">- {t}Значения не указаны{/t} -</span>
        {/if}
        {foreach $value as $item}
            {if isset($values[$item])}
                <span class="property-type-big-list_selected-item" data-id="{$item}">
                    <label>
                        <input type="hidden" name="prop[{$self.id}][value][]" class="property-type-big-list_selected-item-checkbox" data-id="{$item}" {if $disabled}disabled{/if} value="{$item}" checked>
                        <span>{$values[$item]}</span>
                        <i class="property-type-big-list_selected-item-remove zmdi zmdi-close"></i>
                    </label>
                </span>
            {/if}
        {/foreach}
    </div>
    <div class="property-type-big-list_drop-box {if !empty($value)}closed{/if}">
        <div class="property-type-big-list_hint">{t}Все значения{/t}</div>
        <div class="property-type-big-list_search">
            <input class="property-type-big-list_search-input" placeholder="{t}поиск{/t}">
            <i class="property-type-big-list_search-input-icon zmdi zmdi-search"></i>
        </div>
        <div class="property-type-big-list_list-box">
            {$list = array_slice($self->valuesArr(), 0, $page_size, true)}
            {$count_values = count($values)}
            {$count_pages = ceil($count_values / $page_size)}
            {include file='%catalog%/property_val_big_list_items.tpl' list=$list page=1 count_values=$count_values count_pages=$count_pages disabled=$disabled}
        </div>
    </div>
    <div class="property-type-big-list_drop-box-toggle">
        <span class="closed"><i class="zmdi zmdi-chevron-down m-r-10"></i>{t}Показать все значения{/t}</span>
        <span class="opened"><i class="zmdi zmdi-chevron-up m-r-10"></i>{t}Скрыть значения{/t}</span>
    </div>
</div>