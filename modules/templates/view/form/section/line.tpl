{addcss file="%templates%/manager.css"}
{addjs file="%templates%/devicehl.js"}
{$is_bootstrap45 = in_array($elem.grid_system, ['bootstrap4', 'bootstrap5'])}
<table class="bootstrap-multi-values">
    <tr>
        <td class="xs">{include file=$elem["__{$field}_xs"]->getRenderTemplate() field=$elem["__{$field}_xs"]}</td>
        <td class="sm">{include file=$elem["__{$field}_sm"]->getRenderTemplate() field=$elem["__{$field}_sm"]}</td>
        <td class="md">{include file=$elem["__{$field}"]->getRenderTemplate() field=$elem["__{$field}"]}</td>
        <td class="lg">{include file=$elem["__{$field}_lg"]->getRenderTemplate() field=$elem["__{$field}_lg"]}</td>
        {if $is_bootstrap45}
            <td class="xl">{include file=$elem["__{$field}_xl"]->getOriginalTemplate() field=$elem["__{$field}_xl"]}</td>
        {/if}
        {if $elem.grid_system == 'bootstrap5'}
            <td class="xxl">{include file=$elem["__{$field}_xxl"]->getRenderTemplate() field=$elem["__{$field}_xxl"]}</td>
        {/if}
    </tr>
    <tr class="bootstrap-subtext">
        <td class="xs">{if $is_bootstrap45}-{else}XS{/if}</td>
        <td class="sm">SM</td>
        <td class="md">MD</td>
        <td class="lg">LG</td>
        {if $is_bootstrap45}
            <td class="xl">XL</td>
        {/if}
        {if $elem.grid_system == 'bootstrap5'}
            <td class="xxl">XXL</td>
        {/if}
    </tr>
</table>