<div class="filter typeInterval">
    <h4>{$prop.title}:</h4>
    <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
    <div class="fullwidth fromToLine {if $prop.int_hide_inputs}hidden{/if}">
        <div>{t}от{/t}</div>
        <div class="p50"><input type="text" min="{$prop.interval_from}" max="{$prop.interval_to}" class="textinp fromto" name="pf[{$prop.id}][from]" value="{$filters[$prop.id].from|default:$prop.interval_from}" data-start-value="{$prop.interval_from}"></div>
        <div>{t}до{/t}</div>
        <div class="p50"><input type="text" min="{$prop.interval_from}" max="{$prop.interval_to}" class="textinp fromto" name="pf[{$prop.id}][to]" value="{$filters[$prop.id].to|default:$prop.interval_to}" data-start-value="{$prop.interval_to}"></div>
        {if !empty($prop.unit)}<div>{$prop.unit}</div>{/if}
    </div>
</div>