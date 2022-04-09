{* Шаблон для фильтра с типом - число *}
{$is_open = $filters[$prop.id] || $prop.is_expanded}
<div class="accordion-item rs-type-interval">
    <div class="accordion-header">
        <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse"
                data-bs-target="#accordionFilter-{$prop.id}">
            <span class="me-2">{$prop.title} {if $prop.unit}({$prop.unit}){/if}</span>
        </button>
    </div>
    <div id="accordionFilter-{$prop.id}" class="accordion-collapse collapse {if $is_open}show{/if}">
        <div class="accordion-body">
            <div class="row row-cols-2 g-3">
                <div>
                    <label class="form-label">{t}От{/t}</label>
                    <input type="text" class="form-control rs-filter-from" name="pf[{$prop.id}][from]" value="{$filters[$prop.id].from|default:$prop.interval_from}" data-start-value="{$prop.interval_from}">
                </div>
                <div>
                    <label class="form-label">{t}До{/t}</label>
                    <input type="text" class="form-control rs-filter-to" name="pf[{$prop.id}][to]" value="{$filters[$prop.id].to|default:$prop.interval_to}" data-start-value="{$prop.interval_to}">
                </div>

                <div class="col-12">
                    <div class="px-3">
                        <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()},
                            "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' class="rs-plugin-input"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>