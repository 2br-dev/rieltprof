{if $element['type'] == 'atom'}
    <div class='d-atom-instance d-atom-{$atom_type} d-atom-instance{$element.id}'>
{/if}
    {if !empty($wrapper_tag)}
        <{$wrapper_tag} {foreach $wrapper_attrs as $wkey=>$wvalue} {if $wkey==$wvalue}{$wvalue}{else}{$wkey}='{$wvalue}'{/if}{/foreach}>
    {/if}
        {if $tag == 'img' || $tag == 'input' || $tag == 'hr' || $tag == 'link'}
            <{$tag} {foreach $attrs as $key=>$value} {$key}='{$value}'{/foreach}/>
        {else}
            <{$tag} {foreach $attrs as $key=>$value} {if $key==$value}{$value}{else}{$key}='{$value}'{/if}{/foreach}>{$childs}</{$tag}>
        {/if}
    {if !empty($wrapper_tag)}
        </{$wrapper_tag}>
    {/if}
{if $element['type'] == 'atom'}
    </div>
{/if}