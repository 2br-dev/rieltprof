{if $self->isListType()}
    {if count($self->valuesArr()) > 20}
        {include file="%catalog%/property_val_big_list.tpl"}
    {else}
        {$values = $self->valuesArr()}
        <input type="hidden" name="prop[{$self.id}][value][]" value="" class="h-val">
        {foreach $values as $key => $oneitem}
            <span class="inline-item property-type-list">
                <input type="checkbox" name="prop[{$self.id}][value][]" class="h-val" {$disabled} id="ch_{$self.id}{$key}" value="{$key}" {if is_array($value) && in_array($key,  $value)}checked{/if}>
                <label for="ch_{$self.id}{$key}">{$oneitem}</label>
                <a class="p-remove-val">&times;</a>
            </span>
        {/foreach}
    {/if}
{elseif $self.type == 'bool'}
    <input type="hidden" name="prop[{$self.id}][value]" value="0" class="h-val">
    <input type="checkbox" value="1" {if !empty($value)}checked{/if} name="prop[{$self.id}][value]" class="h-val" {$disabled}>
{else}
    <input type="text" value="{$value}" name="prop[{$self.id}][value]" class="h-val" {$disabled}>
{/if}