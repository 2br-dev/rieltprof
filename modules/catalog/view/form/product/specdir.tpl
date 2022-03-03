<div>
    {foreach from=$elem->getSpecDirs() item=spec}
    <input type="checkbox" name="xspec[{$spec.id}]" value="{$spec.id}" id="spec_{$spec.id}" {if is_array($elem.xspec) && in_array($spec.id, $elem.xspec)}checked{/if}>
    <label for="spec_{$spec.id}">{$spec.name}</label><br>
    {/foreach}
</div>