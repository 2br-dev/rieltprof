{$sites_prepare = $field->cost_api->getUserSelectList()}
{$sites = $field->cost_api->fillUsersPriceList($elem, $sites_prepare)}

<table class="rs-space-table">
    {foreach from=$sites item=site}
          <tr>
             <td width="20%">
                {if count($sites)>1}{$site.title}{/if}
             </td>
             <td>
                {if !empty($site.prices)}
                  <select name="user_cost[{$site.id}]">
                    {foreach from=$site.prices item=price}
                        <option  value="{$price.id}" {if $price.selected}selected="selected"{/if}>{$price.title}</option>
                    {/foreach}
                  </select>
                {/if}
             </td>
          </tr>
           
    {/foreach}
</table>
