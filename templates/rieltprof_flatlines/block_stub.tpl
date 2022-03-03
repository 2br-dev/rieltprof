{$can_edit=$current_user->isAdmin()&&$do}
<div class="block-wizard {$class}{if $can_edit} can-edit{/if}">
    <div class="block-wizard__wrapper">
        <div class="block-wizard__title">
            <i class="pe-7s-plugin pe-2x pe-va"></i>
            <span class="pe-va">{$this_controller->getInfo('title')}</span>
        </div>
        {if $can_edit}
        <ol class="block-wizard__do">
            {foreach $do as $url => $data}
            <li>
                {if is_array($data)}
                    <a {foreach from=$data key=k item=val}{if $k!='title'}{$k}="{$val}" {/if}{/foreach}>{$data.title}</a>
                {else}
                    <a href="{$url}" class="crud-add">{$data}</a>
                {/if}
            </li>
            {/foreach}
        </ol>
        {/if}
    </div>
</div>