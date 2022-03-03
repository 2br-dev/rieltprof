{* Шаблон для фильтра с типом - число *}

<div class="filter rs-type-interval {if $filters[$prop.id] || $prop.is_expanded}open{/if}">
    <a class="expand">
        <span class="right-arrow"><i class="pe-2x pe-7s-angle-down-circle"></i></span>
        <span>{$prop.title} {if $prop.unit}({$prop.unit}){/if}</span>
    </a>
    <div class="detail">
        <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' class="rs-plugin-input">

        <div class="filter-fromto {if $prop.int_hide_inputs}hidden{/if}">
            <div class="input-wrapper">
                <label>{t}от{/t}</label>
                <input type="text" class="rs-filter-from" name="pf[{$prop.id}][from]" value="{$filters[$prop.id].from|default:$prop.interval_from}" data-start-value="{$prop.interval_from}">
            </div>
            <div class="input-wrapper">
                <label>{t}до{/t}</label>
                <input type="text" class="rs-filter-to" name="pf[{$prop.id}][to]" value="{$filters[$prop.id].to|default:$prop.interval_to}" data-start-value="{$prop.interval_to}">
            </div>
        </div>
    </div>
</div>