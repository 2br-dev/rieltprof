{$first_offer = true}
{foreach $table_data as $id => $row}
    <tr class="product-offer">
        <td class="l-w-space"></td>
        <td class="chk" style="width:26px;">
            <input type="checkbox" name="chk[]" value="offer_{$id}" class="firbid-miltiedit" {if $first_offer}disabled{/if}>
        </td>
        <td></td>
        {foreach $row as $cell}
            <td>{$cell}</td>
        {/foreach}
        <td style="width:44px;"></td>
        <td class="r-w-space"></td>
    </tr>
    {$first_offer = false}
{/foreach}