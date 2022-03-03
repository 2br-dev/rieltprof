<tbody class="group-body" data-gid="{$group.group.id|default:"0"}">
    <tr class="property-group noover">
        <td colspan="6"><div class="back">{if $group.group.id>0}{$group.group.title}{else}{t}Без группы{/t}{/if}</div></td>
    </tr>
</tbody>
<tbody data-group-id="{$group.group.id|default:"0"}">
    {if !empty($group.properties)}
    {include file="property_product.tpl" properties=$group.properties}
    {/if}
</tbody>