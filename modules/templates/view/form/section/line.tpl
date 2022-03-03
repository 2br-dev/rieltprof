{addjs file="%templates%/devicehl.js"}
<table class="bootstrap-multi-values">
    <tr>
        <td class="xs">{include file=$elem["__{$field}_xs"]->getRenderTemplate() field=$elem["__{$field}_xs"]}</td>
        <td class="sm">{include file=$elem["__{$field}_sm"]->getRenderTemplate() field=$elem["__{$field}_sm"]}</td>
        <td class="md">{include file=$elem["__{$field}"]->getRenderTemplate() field=$elem["__{$field}"]}</td>
        <td class="lg">{include file=$elem["__{$field}_lg"]->getRenderTemplate() field=$elem["__{$field}_lg"]}</td>
        {if $elem.grid_system == 'bootstrap4'}
            <td class="xl">{include file=$elem["__{$field}_xl"]->getOriginalTemplate() field=$elem["__{$field}_xl"]}</td>
        {/if}
    </tr>
    <tr class="bootstrap-subtext">
        <td class="xs">{if $elem.grid_system == 'bootstrap4'}-{else}XS{/if}</td>
        <td class="sm">SM</td>
        <td class="md">MD</td>
        <td class="lg">LG</td>
        {if $elem.grid_system == 'bootstrap4'}
            <td class="xl">XL</td>
        {/if}
    </tr>
</table>