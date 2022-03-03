<div class="menu-sub-borders mblock">
    <ul>
        {foreach from=$level item=item3}
            <li class="{if !empty($item3.child)}node{/if}{if isset($sel_id) && $sel_id==$item3.fields.id} act{/if}">
                <a href="{$item3.fields->getHref()}">{$item3.fields.title}</a>

                {if !empty($item3.child)}
                    {include file="user_branch.tpl" level=$item3.child}
                {/if}
            </li>
        {/foreach}
    </ul>
</div>