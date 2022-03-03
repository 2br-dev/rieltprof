{addjs file="jquery.formstyler.min.js" no_compress=true}
{addjs file="jquery.slider.min.js"}
{addjs file="{$mod_js}jquery.filter.js" basepath="root"}
{$catalog_config=ConfigLoader::byModule('catalog')}

<section class="filterSection" data-query-value="{$url->get('query', $smarty.const.TYPE_STRING)}">
    <div class="loadOverlay"></div>
    <a href="#" class="onemoreEmpty blackHover filterToggle rs-parent-switcher" data-cookie-id="sideFilter" data-on-text="{t}развернуть расширенный фильтр{/t}">{t}свернуть расширенный фильтр{/t}</a>
    <form method="GET" class="filters" action="{urlmake filters=null pf=null bfilter=null p=null}" autocomplete="off">
        {if $param.show_cost_filter}
            <div class="filter typeInterval">
                <h4>{t}Цена{/t}:</h4>
                {if $catalog_config.price_like_slider && ($moneyArray.interval_to>$moneyArray.interval_from)} {* Если нужно показать как слайдер*}
                    <input type="hidden" data-slider='{ "from":{$moneyArray.interval_from}, "to":{$moneyArray.interval_to}, "step": "{$moneyArray.step}", "round": {$moneyArray.round}, "dimension": " {$moneyArray.unit}", "heterogeneity": [{$moneyArray.heterogeneity}]  }' value="{$basefilters.cost.from|default:$moneyArray.interval_from};{$basefilters.cost.to|default:$moneyArray.interval_to}" class="pluginInput" data-closest=".fromToPrice" data-start-value="{$basefilters.cost.from|default:$moneyArray.interval_from};{$basefilters.cost.to|default:$moneyArray.interval_to}"/>
                {/if}
                <table class="fullwidth fromToLine">
                    <tr>
                        <td>{t}от{/t}</td>
                        <td class="p50"><input type="text" min="{$moneyArray.interval_from}" max="{$moneyArray.interval_to}" class="textinp fromto" name="bfilter[cost][from]" value="{if !$catalog_config.price_like_slider}{$basefilters.cost.from}{else}{$basefilters.cost.from|default:$moneyArray.interval_from}{/if}" data-start-value="{if $catalog_config.price_like_slider}{$moneyArray.interval_from|intval}{/if}"></td>
                        <td>{t}до{/t}</td>
                        <td class="p50"><input type="text" min="{$moneyArray.interval_from}" max="{$moneyArray.interval_to}" class="textinp fromto" name="bfilter[cost][to]" value="{if !$catalog_config.price_like_slider}{$basefilters.cost.to}{else}{$basefilters.cost.to|default:$moneyArray.interval_to}{/if}" data-start-value="{if $catalog_config.price_like_slider}{$moneyArray.interval_to|intval}{/if}"></td>
                        <td>{$prop.unit}</td>
                    </tr>
                </table>
            </div>
        {/if}
        {if $param.show_is_num}
            <div class="filter">
                <h4>{t}Наличие{/t}:</h4>
                <select class="yesno" name="bfilter[isnum]" data-start-value="">
                     <option value="">{t}Неважно{/t}</option>
                     <option value="1" {if $basefilters.isnum == '1'}selected{/if}>{t}Есть{/t}</option>
                     <option value="0" {if $basefilters.isnum == '0'}selected{/if}>{t}Нет{/t}</option>
                </select>
            </div>
        {/if}
        {if $param.show_brand_filter && count($brands)>1}
            <div class="filter typeMultiselect">
                <h4>{t}Бренд{/t}: <a class="removeBlockProps hidden" title="{t}Сбросить выбранные параметры{/t}"></a></h4>
                <ul class="propsContentSelected hidden"></ul>
                <div class="propsContainer">
                    <ul class="propsContent">
                        {$i = 1}
                        {foreach $brands as $brand}
                            <li style="order: {$i++};" {if isset($filters_allowed_sorted['brand'][$brand.id]) && ($filters_allowed_sorted['brand'][$brand.id] == false)}class="disabled-property"{/if}>
                                <input type="checkbox" {if is_array($basefilters.brand) && in_array($brand.id, $basefilters.brand)}checked{/if} name="bfilter[brand][]" value="{$brand.id}" class="cb" id="cb_{$brand.id}_{$smarty.foreach.i.iteration}">
                                <label for="cb_{$brand.id}_{$smarty.foreach.i.iteration}">{$brand.title}</label>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        {/if}
        {foreach from=$prop_list item=item}
            {foreach from=$item.properties item=prop}
            
                {* Подключаем фильтры по характеристикам *}
                {include file="%catalog%/blocks/sidefilters/type/{$prop.type}.tpl"}
                
            {/foreach}
        {/foreach}
        <input type="submit" value="{t}Применить{/t}" class="onemore submitFilter">
        <a href="{urlmake p=null pf=null bfilter=null}" class="onemore cleanFilter{if empty($filters) && empty($basefilters)} hidden{/if}">{t}очистить фильтр{/t}</a>
        
        <script type="text/javascript">
            $(function() {
                $('.filter .cb, .filter .yesno').styler();
                $('.typeInterval .pluginInput').each(function() {
                    var $this = $(this);
                    
                    var fromTo = $this.siblings('.fromToLine');
                    
                    $this.jslider( $.extend( $(this).data('slider'), { callback: function(value) {
                        var values = value.split(';');
                        $('input[name$="[from]"]', fromTo).val(values[0]);
                        $('input[name$="[to]"]', fromTo).val(values[1]);
                        $this.trigger('change');
                    }})
                    );
                    
                    $('input[name$="[from]"], input[name$="[to]"]', fromTo).change(function() {
                        var from = $('input[name$="[from]"]', fromTo).val();
                        var to = $('input[name$="[to]"]', fromTo).val();
                        $this.jslider('value', from, to);
                    });
                });
            });
        </script>
    </form>
</section>