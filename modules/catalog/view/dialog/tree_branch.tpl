 {if !empty($dirlist)}
 <ul {if $open}style="display:block"{/if}>
    {foreach from=$dirlist item=item name="dirlist"}
    <li class="{if $smarty.foreach.dirlist.last}end{if $item->getChildsCount()}plus{/if}{/if}{if !$smarty.foreach.dirlist.last}{if !$item->getChildsCount()}branch{else}plus{/if}{/if}" qid="{$item.fields.id}">
        <img src="{$Setup.IMG_PATH}/adminstyle/minitree/folder.png">
        <input type="checkbox" value="{$item.fields.id}" name="group[]" {if $hideGroupCheckbox}style="display:none"{/if}>
        <a class="{if $item.fields.is_virtual}virtual{/if} {if $item.fields.is_spec_dir == 'Y'}spec{/if}">{$item.fields.name}</a>
    </li>
    {/foreach}
</ul>
{/if}