<table id="costTable" class="otable">
    {$elem->calculateUserCost()}
    {foreach from=$elem->getCostList() item=onecost}
        <tr>
            <td class="otitle">{$onecost.title}</td>
            <td>
                {if $onecost.type=='manual'}
                    <label><input class="new_price_check" type="checkbox" name="excost[{$onecost.id}][edit_multi]" value="1"/> {t}Вычислить от другой цены{/t}</label><br>
                {/if}

                <div class="price_row">
                    <input id="cost_values" data-id="{$onecost.id}" type="text" name="excost[{$onecost.id}][cost_original_val]" value="{$elem.excost[$onecost.id]['cost_original_val']}" {if $onecost.type=='auto'}disabled{/if}>
                </div>
                {if $onecost.type=='auto'}<span class="help-icon" title="{t}Автовычесляемое поле, будет расчитано после сохранения{/t}">?</span>{/if}
                {if $onecost.type=='manual'}
                    <div class="price_row">
                        <select name="excost[{$onecost.id}][cost_original_currency]">
                            {foreach from=$elem->getCurrencies() key=id item=curr}
                            <option value="{$id}" {if $elem.excost[$onecost.id].cost_original_currency == $id}selected{/if}>{$curr}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div class="new_price_row">
                 
                       <select name="excost[{$onecost.id}][way]" disabled="disabled">
                            <option value="0" >+</option>
                            <option value="1" >-</option>
                       </select>
                       <input type="text" name="excost[{$onecost.id}][plus_value]" maxlength="10" size="10" disabled="disabled"/>
                       <select name="excost[{$onecost.id}][plus_type]" disabled="disabled">
                            <option value="0" >%</option>
                            <option value="1" >{t}ед.{/t}</option>
                       </select>
                       {t}от{/t}
                       <select name="excost[{$onecost.id}][from_price]" disabled="disabled">
                           {foreach from=$elem->getCostList() item=from_cost}
                               <option value="{$from_cost.id}" >{$from_cost.title}</option>
                           {/foreach}  
                       </select>

                    </div>
                {/if}
                
            </td>
        </tr>
    {/foreach}
</table>

<script type="text/javascript">
    $.allReady(function(){
        /**
        * Показ расширенного редактирования цены
        */
        $("body").off('click.editPrice').on('click.editPrice','.new_price_check',function(){
            $(".new_price_row",$(this).closest('tr')).toggle(); 
            $(".price_row",$(this).closest('tr')).toggle();
            if ($(this).prop('checked')){
               $(".new_price_row input",$(this).closest('tr')).prop('disabled',false); 
               $(".new_price_row select",$(this).closest('tr')).prop('disabled',false); 
            }else{
               $(".new_price_row input",$(this).closest('tr')).prop('disabled',true); 
               $(".new_price_row select",$(this).closest('tr')).prop('disabled',true); 
            }
        }); 
    });
</script>