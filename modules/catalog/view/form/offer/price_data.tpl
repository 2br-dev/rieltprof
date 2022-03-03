{* Форма редактирования цен у комплектации *}
<div class="offer-price-data">
    {$product=$elem->getProduct()}
    {$currencies=$product->getCurrencies()}
    {$pricedata_arr=$elem.pricedata_arr}
    
    <input type="checkbox" name="pricedata_arr[oneprice][use]" class="oneprice" value="1" id="op_{$elem.id}" {if $pricedata_arr.oneprice.use}checked{/if}>
    <label for="op_{$elem.id}">{t}Для всех типов цен{/t}</label><br>

    <table class="otable vtable" {if $pricedata_arr.oneprice.use}style="display:none"{/if}>
        {foreach from=$product->getCostList() item=onecost}
        {if $onecost.type != 'auto'}
        <tr>
            <td class="otitle">{$onecost.title}</td>
            <td>
            <select name="pricedata_arr[price][{$onecost.id}][znak]">
                <option {if $pricedata_arr.price[$onecost.id].znak=='+'}selected{/if}>+</option>
                <option {if $pricedata_arr.price[$onecost.id].znak=='='}selected{/if}>=</option>
                
            </select>&nbsp;
            <input name="pricedata_arr[price][{$onecost.id}][original_value]" type="text" size="7" value="{$pricedata_arr.price[$onecost.id].original_value}">&nbsp;
            <select name="pricedata_arr[price][{$onecost.id}][unit]">
                {foreach from=$currencies key=curr_id item=curr}
                    <option value="{$curr_id}" {if $pricedata_arr.price[$onecost.id].unit == $curr_id}selected{/if}>{$curr}</option>
                {/foreach}
                <option value="%" {if $pricedata_arr.price[$onecost.id].unit=='%'}selected{/if}>%</option>
            </select>
            </td>
        </tr>
        {/if}
        {/foreach}
    </table>

    <div class="oneprice-data" {if !$pricedata_arr.oneprice.use}style="display:none"{/if}>
        <select name="pricedata_arr[oneprice][znak]">
            <option {if $pricedata_arr.oneprice.znak == '+'}selected{/if}>+</option>                                
            <option {if $pricedata_arr.oneprice.znak == '='}selected{/if}>=</option>
        </select> 
        <input name="pricedata_arr[oneprice][original_value]" type="text" size="7" value="{$pricedata_arr.oneprice.original_value}"> 
        <select name="pricedata_arr[oneprice][unit]">
            {foreach from=$currencies key=curr_id item=curr}
                <option value="{$curr_id}" {if $pricedata_arr.oneprice.unit == $curr_id}selected{/if}>{$curr}</option>
            {/foreach}                                
            <option value="%" {if $pricedata_arr.oneprice.unit == '%'}selected{/if}>%</option>
        </select>
    </div>
</div>