<div>
    <div class="deleteSpecDir">
        <label><input type="checkbox" name="xspec[delbefore]" value="1"> {t}Удалять связь с установленными раннее спецкатегориями{/t}</label>
    </div>
    {foreach from=$elem->getSpecDirs() item=spec}
    <input type="checkbox" name="xspec[{$spec.id}]" value="{$spec.id}" id="spec_{$spec.id}" {if is_array($elem.xspec) && in_array($spec.id, $elem.xspec)}checked{/if}>
    <label for="spec_{$spec.id}">{$spec.name}</label><br>
    {/foreach}    
</div>