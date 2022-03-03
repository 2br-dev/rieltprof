{addcss file="{$mod_css}tax.css" basepath="root"}
{addjs file="{$mod_js}jquery.tax.js" basepath="root"}
{addjs file="tmpl/tmpl.min.js" basepath="common"}

{assign var=tax_regions value=$elem->getTaxRegions()}
<div id="ratesBlock">
    <div class="toolsBlock">
        <div class="actions">
            <a class="add underline"><span>{t}Добавить ставку налога для местоположения{/t}</span></a>
            <span class="successText">{t}Ставка успешно добавлена{/t}</span>
        </div>
        
        <div class="form">
            <table width="100%" class="property-table some-property-table">
                <tr>
                    <td class="key">{t}Местоположение{/t}
                    <div class="fieldhelp">{t}Удерживая CTRL можно выбрать несколько элементов{/t}</div></td>
                    <td>
                    <input type="checkbox" id="onlyCountries"> <label for="onlyCountries">{t}Отображать только страны{/t}</label><br>
                    <select class="p-regions" size="20" style="height:250px;" multiple="multiple">
                        {foreach from=$tax_regions item=item}
                            <option value="{$item.id}" class="{if $item.parent_id>0}region{/if}">{$item.display_title}</option>
                        {/foreach}
                    </select></td>
                </tr>
                <tr>
                    <td class="key">{t}Ставка налога, %{/t}</td>
                    <td><input type="text" class="p-rate"></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <a class="addRate">{t}Добавить{/t}</a>
                        <a class="close">{t}свернуть{/t}</a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <table class="rs-table toolsContainer">
        <thead class="head">
            <tr>
                <td>{t}Местоположение{/t}</td>
                <td>{t}Ставка, %{/t}</td>
                <td class="item-tools">
            </tr>
        </thead>
        <tbody class="overable" id="rateItems">
            {foreach from=$elem.rates key=region_id item=rate}
            <tr data-id="{$region_id}" class="rateItem">
                <td>{$tax_regions[$region_id].display_title}</td>
                <td><input type="text" name="rates[{$region_id}]" value="{$rate}"></td>
                <td class="item-tools">
                    <div class="inline-tools">
                        <a class="tool p-del" title="{t}удалить{/t}"><i class="zmdi zmdi-delete c-red"></i></a>
                    </div>
                </td>
            </tr>            
            {/foreach}
        </tbody>
    </table>    
</div>

{literal}
<script type="text/x-tmpl" id="tmpl-rate-line">
    <tr data-id="{%=o.region_id%}" class="rateItem">
        <td>{%=o.region%}</td>
        <td><input type="text" name="rates[{%=o.region_id%}]" value="{%=o.rate%}"></td>
        <td class="item-tools">
            <div class="inline-tools">
                <a class="tool p-del" title="{/literal}{t}удалить{/t}{literal}"><i class="zmdi zmdi-delete c-red"></i></a>
            </div>
        </td>
    </tr>
</script>
{/literal}