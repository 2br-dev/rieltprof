{assign var=values value=$elem->tableDataUnserialized()}

{if !empty($values)} 
    <table class="feedback_result_form">
        {$m=0}
        {foreach $values as $product_info}
            {$m=$m+1}
            <tr>
                <th>
                    {$m}. <a href="{adminUrl do="edit" id=$product_info.id mod_controller="catalog-ctrl"}" target="_blank">{$product_info.title}</a>
                    <span> - {t}кол-во:{/t} {$product_info.offer_fields.amount|default:1}</span>
                </th>
            </tr>
            {* Комплектации *}
            {if !empty($product_info.offer_fields)}
                {if isset($product_info.offer_fields.offer)}
                    <tr>
                        <td class="feedback_result_value"> 
                           {t}Комплектация{/t}: {$product_info.offer_fields.offer}
                        </td>
                    </tr>
                {/if}
                {if isset($product_info.offer_fields.multioffer_val) && !empty($product_info.offer_fields.multioffer_val)}
                    {assign var=multioffers value=implode("<br/>", $product_info.offer_fields.multioffer_val)}
                    <tr>
                        <td class="feedback_result_value"> 
                           {$multioffers} 
                        </td>
                    </tr>
                {elseif isset($product_info.offer_fields.multioffer) && !empty($product_info.offer_fields.multioffer)}
                    {assign var=multioffers value=implode("<br/>", $product_info.offer_fields.multioffer)}
                    <tr>
                        <td class="feedback_result_value"> 
                           {$multioffers} 
                        </td>
                    </tr>
                {/if}
            {/if}
        {/foreach}
        
    </table>
{/if}