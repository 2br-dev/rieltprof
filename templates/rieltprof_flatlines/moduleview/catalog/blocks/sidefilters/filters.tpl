{* Фильтры по характеристикам товаров *}

{addcss file="libs/nouislider.css"}
{addjs file="libs/nouislider.min.js"}
{addjs file="libs/wnumb.js"}
{addjs file="rs.filter.js"}
{$catalog_config=ConfigLoader::byModule('catalog')}

<div class="tabs-wrapper filterSection sidebar sec-filter rs-filter-section{if $smarty.cookies.filter} expand{/if}" data-query-value="{$url->get('query', $smarty.const.TYPE_STRING)}">
{*    <div class="sec-filter_overlay"></div>*}

{*    <a class="sec-filter_toggle  visible-xs visible-sm" data-toggle-class="expand"*}
{*                                                        data-target-closest=".sec-filter"*}
{*                                                        data-toggle-cookie="filter">*}
{*        <i class="pe-2x pe-7s-filter pe-va"></i>*}
{*        <span class="expand-text">{t}Открыть фильтр{/t}</span>*}
{*        <span class="collapse-text">{t}Свернуть фильтр{/t}</span>*}
{*    </a>*}

    <form method="GET" class="filters rs-filters" action="{urlmake filters=null pf=null bfilter=null p=null}" autocomplete="off">
        <div class="tabs-header">
            <a href="#" data-target="params" class="tab-link active">Параметры</a>
            <a href="#" data-target="geo" class="tab-link">Гео</a>
            <a href="#" data-target="payment" class="tab-link">Доп-но</a>
        </div>
        <div class="tabs">
            <div class="tab" id="geo">
                {foreach $prop_list as $item}
                    {foreach $item.properties as $prop}
                        {if $prop['title'] == 'Округ'}
                            <div class="filter typeMultiselect">
                                <div class="input-header">Округ</div>
                                <div class="row">
                                    <div class="col">
                                        <div class="checkgroup">
                                            {$i = 1}
                                            {foreach $prop->getAllowedValues() as $key => $value}
                                                <div class="checkgroup-item">
                                                    <input
                                                            type="checkbox"
                                                            {if is_array($filters[$prop.id]) && in_array($key, $filters[$prop.id])}checked{/if}
                                                            name="pf[{$prop.id}][]"
                                                            value="{$key}"
                                                            class="cb checkbox"
                                                            id="cb_{$prop.id}_{$value@iteration}"
                                                    >
                                                    <label for="cb_{$prop.id}_{$value@iteration}">{$value}</label>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Район'}
                            <div class="filter typeMultiselect">
                                <div class="input-header">Район</div>
                                <div class="row">
                                    <div class="col">
                                        <div class="checkgroup">
                                            {$i = 1}
                                            {foreach $prop->getAllowedValues() as $key => $value}
                                                <div class="checkgroup-item">
                                                    <input
                                                            type="checkbox"
                                                            {if is_array($filters[$prop.id]) && in_array($key, $filters[$prop.id])}checked{/if}
                                                            name="pf[{$prop.id}][]"
                                                            value="{$key}"
                                                            class="cb checkbox"
                                                            id="cb_{$prop.id}_{$value@iteration}"
                                                    >
                                                    <label for="cb_{$prop.id}_{$value@iteration}">{$value}</label>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Улица'}
                            <div class="filter row">
                                <div class="col">
                                    <div class="input-field">
                                        <input
                                            type="text"
                                            class="textinp string"
                                            name="pf[{$prop.id}]"
                                            value="{$filters[$prop.id]}"
{*                                            data-start-value=""*}
                                        >
                                        <label for="">Улица</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Дом' || $prop['title'] == 'Литер/корпус'}
                            <div class="row">
                        {/if}
                        {if $prop['title'] == 'Дом'}
                            <div class="col">
                                <div class="input-field">
                                    <input
                                        type="text"
                                        class="textinp string"
                                        name="pf[{$prop.id}]"
                                        value="{$filters[$prop.id]}"
{*                                            data-start-value=""*}
                                    >
                                    <label for="">Дом</label>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Литер/корпус'}
                            <div class="col">
                                <div class="input-field">
                                    <input
                                        type="text"
                                        class="textinp string"
                                        name="pf[{$prop.id}]"
                                        value="{$filters[$prop.id]}"
{*                                            data-start-value=""*}
                                    >
                                    <label for="">Корпус</label>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Дом' || $prop['title'] == 'Литер/корпус'}
                            </div>
                        {/if}
                    {/foreach}
                {/foreach}
            </div>
            <div class="tab active" id="params">
                {foreach $prop_list as $item}
                    {foreach $item.properties as $prop}
                        {if $prop['title'] == 'Цена'}
                            <div class="filter typeInterval">
                                <div class="input-header">Цена</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Цена аренды в мес.'}
                            <div class="filter typeInterval">
                                <div class="input-header">Цена аренды в мес.</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Цена за м2'}
                            <div class="filter typeInterval">
                                <div class="input-header">Цена за м2</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Количество комнат'}
                            <div class="filter typeInterval">
                                <div class="input-header">Количество комнат</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Количество комнат (квартиры, новостройки)'}
                            <div class="filter typeMultiselect">
                                <div class="input-header">Количество комнат</div>
                                <div class="row">
                                    <div class="col">
                                        <div class="checkgroup">
                                            {$i = 1}
                                            {foreach $prop->getAllowedValues() as $key => $value}
                                                <div class="checkgroup-item">
                                                    <input
                                                            type="checkbox"
                                                            {if is_array($filters[$prop.id]) && in_array($key, $filters[$prop.id])}checked{/if}
                                                            name="pf[{$prop.id}][]"
                                                            value="{$key}"
                                                            class="cb checkbox"
                                                            id="cb_{$prop.id}_{$value@iteration}"
                                                    >
                                                    <label for="cb_{$prop.id}_{$value@iteration}">{$value}</label>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Все комнаты изолированы'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="isolated"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="isolated">Все комнаты изолированы?</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Материал стен'}
                            <div class="filter typeMultiselect">
                                <div class="input-header">Материал стен</div>
                                <div class="row">
                                    <div class="col">
                                        <div class="checkgroup">
                                            {$i = 1}
                                            {foreach $prop->getAllowedValues() as $key => $value}
                                                <div class="checkgroup-item">
                                                    <input
                                                            type="checkbox"
                                                            {if is_array($filters[$prop.id]) && in_array($key, $filters[$prop.id])}checked{/if}
                                                            name="pf[{$prop.id}][]"
                                                            value="{$key}"
                                                            class="cb checkbox"
                                                            id="cb_{$prop.id}_{$value@iteration}"
                                                    >
                                                    <label for="cb_{$prop.id}_{$value@iteration}">{$value}</label>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Год постройки'}
                            <div class="filter typeInterval">
                                <div class="input-header">Год постройки</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Состояние'}
                            <div class="filter typeMultiselect">
                                <div class="input-header">Состояние</div>
                                <div class="row">
                                    <div class="col">
                                        <div class="checkgroup">
                                            {$i = 1}
                                            {foreach $prop->getAllowedValues() as $key => $value}
                                                <div class="checkgroup-item">
                                                    <input
                                                            type="checkbox"
                                                            {if is_array($filters[$prop.id]) && in_array($key, $filters[$prop.id])}checked{/if}
                                                            name="pf[{$prop.id}][]"
                                                            value="{$key}"
                                                            class="cb checkbox"
                                                            id="cb_{$prop.id}_{$value@iteration}"
                                                    >
                                                    <label for="cb_{$prop.id}_{$value@iteration}">{$value}</label>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Площадь'}
                            <div class="filter typeInterval">
                                <div class="input-header">Площадь (м²)</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Площадь кухни'}
                            <div class="filter typeInterval">
                                <div class="input-header">Площадь кухни (м²)</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Площадь жилая'}
                            <div class="filter typeInterval">
                                <div class="input-header">Площадь жилая (м²)</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Площадь участка'}
                            <div class="filter typeInterval">
                                <div class="input-header">Площадь участка (сот.)</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Этаж'}
                            <div class="filter typeInterval">
                                <div class="input-header">Этаж</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Первый этаж'}
                            <div class="row">
                            <div class="col filter">
                                <div class="input-field">
                                    <input type="hidden" value="" name="pf[{$prop.id}]">
                                    <input
                                            type="checkbox"
                                            id="is_first"
                                            class="pseudo-checkbox yesno checkbox"
                                            data-select="select_isolated"
                                            name="pf[{$prop.id}]"
                                            value="1"
                                            {if $filters[$prop.id] == '1'}checked{/if}
                                    >
                                    <label for="is_first">Первый этаж</label>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Последний этаж'}
                            <div class="col filter">
                                <div class="input-field">
                                    <input type="hidden" value="" name="pf[{$prop.id}]">
                                    <input
                                            type="checkbox"
                                            id="is_last"
                                            class="pseudo-checkbox yesno checkbox"
                                            data-select="select_isolated"
                                            name="pf[{$prop.id}]"
                                            value="1"
                                            {if $filters[$prop.id] == '1'}checked{/if}
                                    >
                                    <label for="is_last">Последний этаж</label>
                                </div>
                            </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Этажность дома'}
                            <div class="filter typeInterval">
                                <div class="input-header">Этажность дома</div>
                                <input type="hidden" data-slider='{ "from":{$prop.interval_from}, "to":{$prop.interval_to}, "step": "{$prop.step}", "round": {$prop->getRound()}, "dimension": " {$prop.unit}", "heterogeneity": [{$prop->getHeterogeneity()}], "scale": [{$prop->getScale()}]  }' value="{$filters[$prop.id].from|default:$prop.interval_from};{$filters[$prop.id].to|default:$prop.interval_to}" class="pluginInput" data-start-value="{$prop.interval_from};{$prop.interval_to}">
                                <div class="row clear">
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][from]"
                                                    value="{$filters[$prop.id].from}"
                                                    {*                                            data-start-value="{$prop.interval_from}"*}
                                            >
                                            <label for="">От</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-field">
                                            <input
                                                    type="text"
                                                    min="{$prop.interval_from}"
                                                    max="{$prop.interval_to}"
                                                    class="textinp fromto"
                                                    name="pf[{$prop.id}][to]"
                                                    value="{$filters[$prop.id].to}"
                                                    {*                                            data-start-value="{$prop.interval_to}"*}
                                            >
                                            <label for="">До</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                {/foreach}
            </div>
            <div class="tab" id="payment">
                {foreach $prop_list as $item}
                    {foreach $item.properties as $prop}
                        {if $prop['title'] == 'Срочно'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="quickly"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="quickly">Срочно</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Закладка'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="mark"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="mark">Возможна закладка/плачу комиссию</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Только наличные'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="cash"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="cash">Только наличные</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Ипотеку рассматриваем'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="mortgage"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="mortgage">Ипотеку рассматриваем</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Нужна разбивка по сумме'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="break"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="break">Нужна разбивка по сумме</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Обременение банка'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="limit"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="limit">Обременение банка</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Несовершеннолетние дети/опека'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="child"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="child">Несовершеннолетние дети/опека</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Перепланировка'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="remodeling"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="remodeling">Перепланировка</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Перепланировка узаконена?'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="remod_law"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="remod_law">Перепланировка узаконена</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'Чистый эксклюзив'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="exclusive"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="exclusive">Чистый эксклюзив</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $prop['title'] == 'От себя рекламирую в интернете'}
                            <div class="filter">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" value="" name="pf[{$prop.id}]">
                                        <input
                                                type="checkbox"
                                                id="adv"
                                                class="pseudo-checkbox yesno checkbox"
                                                data-select="select_isolated"
                                                name="pf[{$prop.id}]"
                                                value="1"
                                                {if $filters[$prop.id] == '1'}checked{/if}
                                        >
                                        <label for="adv">От себя рекламирую в интернете</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                {/foreach}
            </div>
        </div>
{*        <div class="sidebar_menu">*}

{*            {if $param.show_cost_filter}*}
{*                <div class="filter filter-interval rs-type-interval {if $basefilters.cost || (is_array($param.expanded) && in_array('cost', $param.expanded))}open{/if}">*}
{*                    <a class="expand">*}
{*                        <span class="right-arrow"><i class="pe-2x pe-7s-angle-down-circle"></i></span>*}
{*                        <span>{t}Цена{/t}</span>*}
{*                    </a>*}
{*                    <div class="detail">*}
{*                        {if $catalog_config.price_like_slider && ($moneyArray.interval_to>$moneyArray.interval_from)}*}
{*                            <input type="hidden" data-slider='{ "from":{$moneyArray.interval_from}, "to":{$moneyArray.interval_to}, "step": "{$moneyArray.step}", "round": {$moneyArray.round}, "dimension": " {$moneyArray.unit}", "heterogeneity": [{$moneyArray.heterogeneity}]  }' value="{$basefilters.cost.from|default:$moneyArray.interval_from};{$basefilters.cost.to|default:$moneyArray.interval_to}" class="rs-plugin-input"/>*}
{*                        {/if}*}

{*                        <div class="filter-fromto">*}
{*                            <div class="input-wrapper">*}
{*                                <label>{t}от{/t}</label>*}
{*                                <input type="number" min="{$moneyArray.interval_from}" max="{$moneyArray.interval_to}" class="rs-filter-from" name="bfilter[cost][from]" value="{if !$catalog_config.price_like_slider}{$basefilters.cost.from}{else}{$basefilters.cost.from|default:$moneyArray.interval_from}{/if}" data-start-value="{if $catalog_config.price_like_slider}{$moneyArray.interval_from|floatval}{/if}">*}
{*                            </div>*}
{*                            <div class="input-wrapper">*}
{*                                <label>{t}до{/t}</label>*}
{*                                <input type="number" min="{$moneyArray.interval_from}" max="{$moneyArray.interval_to}" class="rs-filter-to" name="bfilter[cost][to]" value="{if !$catalog_config.price_like_slider}{$basefilters.cost.to}{else}{$basefilters.cost.to|default:$moneyArray.interval_to}{/if}" data-start-value="{if $catalog_config.price_like_slider}{$moneyArray.interval_to|floatval}{/if}">*}
{*                            </div>*}
{*                        </div>*}
{*                    </div>*}
{*                </div>*}
{*            {/if}*}

{*            {if $param.show_is_num}*}
{*                <div class="filter filter-radio {if $basefilters.isnum != '' || (is_array($param.expanded) && in_array('num', $param.expanded))}open{/if}">*}
{*                    <a class="expand">*}
{*                        <span class="right-arrow"><i class="pe-2x pe-7s-angle-down-circle"></i></span>*}
{*                        <span>{t}Наличие{/t}</span>*}
{*                    </a>*}
{*                    <div class="detail">*}
{*                        <ul class="filter-radio_content">*}
{*                            <li>*}
{*                                <input type="radio" {if !isset($basefilters.isnum)}checked{/if} name="bfilter[isnum]" value="" data-start-value id="rb_is_num_no">*}
{*                                <label for="rb_is_num_no">{t}Неважно{/t}</label>*}
{*                            </li>*}
{*                            <li>*}
{*                                <input type="radio" {if $basefilters.isnum == '1'}checked{/if} name="bfilter[isnum]" value="1" id="rb_is_num_1">*}
{*                                <label for="rb_is_num_1">{t}Есть{/t}</label>*}
{*                            </li>*}
{*                            <li>*}
{*                                <input type="radio" {if $basefilters.isnum == '0'}checked{/if} name="bfilter[isnum]" value="0" id="rb_is_num_0">*}
{*                                <label for="rb_is_num_0">{t}Нет{/t}</label>*}
{*                            </li>*}
{*                        </ul>*}
{*                    </div>*}
{*                </div>*}
{*            {/if}*}
{*            {if !is_null($brands)}*}
{*                {$is_brand = count($brands)}*}
{*            {else}*}
{*                {$is_brand = 0}*}
{*            {/if}*}
{*            {if $param.show_brand_filter && $is_brand>1}*}
{*                <div class="filter filter-checkbox rs-type-multiselect {if $basefilters.brand || (is_array($param.expanded) && in_array('brand', $param.expanded))}open{/if}">*}
{*                    <a class="expand">*}
{*                        <span class="right-arrow"><i class="pe-2x pe-7s-angle-down-circle"></i></span>*}
{*                        <span>{t}Бренд{/t} <span class="filter-remove rs-remove hidden" title="{t}Сбросить выбранные параметры{/t}"><i class="pe-va pe-7s-close-circle"></i></span></span>*}
{*                    </a>*}
{*                    <div class="detail">*}
{*                        <ul class="filter-checkbox_selected rs-selected hidden"></ul>*}
{*                        <div class="filter-checkbox_container">*}
{*                            <ul class="filter-checkbox_content rs-content">*}
{*                                {$i = 1}*}
{*                                {foreach $brands as $brand}*}
{*                                    <li style="order: {$i++};" {if isset($filters_allowed_sorted['brand'][$brand.id]) && ($filters_allowed_sorted['brand'][$brand.id] == false)}class="disabled-property"{/if}>*}
{*                                        <input type="checkbox" {if is_array($basefilters.brand) && in_array($brand.id, $basefilters.brand)}checked{/if} name="bfilter[brand][]" value="{$brand.id}" class="cb" id="cb_{$brand.id}_{$smarty.foreach.i.iteration}">*}
{*                                        <label for="cb_{$brand.id}_{$smarty.foreach.i.iteration}">{$brand.title}</label>*}
{*                                    </li>*}
{*                                {/foreach}*}
{*                            </ul>*}
{*                        </div>*}
{*                    </div>*}
{*                </div>*}
{*            {/if}*}

{*            {foreach $prop_list as $item}*}
{*                {foreach $item.properties as $prop}*}
{*                    {include file="%catalog%/blocks/sidefilters/type/{$prop.type}.tpl"}*}
{*                {/foreach}*}
{*            {/foreach}*}
{*        </div>*}

        <div class="sidebar_menu_buttons">
            <button type="submit" class="theme-btn_search rs-apply-filter btn accent">{t}Применить{/t}</button>
            <a href="{urlmake pf=null p=null bfilter=null filters=null escape=true}" class="btn accent theme-btn_reset rs-clean-filter{if empty($filters) && empty($basefilters)} hidden{/if}">{t}Сбросить фильтр{/t}</a>
        </div>
    </form>
</div>
