{assign var=can_edit value=$current_user->isAdmin()&&$do}
<div class="blockWizard {$class}{if $can_edit} canEdit{/if}">
    <div class="back"></div>
    <div class="outer">
        <div class="middle">
            <div class="inner">
                <div class="title"><img src="{$THEME_IMG}/blockstub.png" alt="Blockstub"> <span>{$this_controller->getInfo('title')}</span></div>
                {if $can_edit}
                <ul class="do">
                    {foreach from=$do key=url item=data}
                    <li>
                        {if is_array($data)}
                            <a {foreach from=$data key=k item=val}{if $k!='title'}{$k}="{$val}" {/if}{/foreach}>{$data.title}</a>
                        {else}
                            <a href="{$url}" class="crud-add">{$data}</a>
                        {/if}
                    </li>
                    {/foreach}
                </ul>
                {/if}
            </div>
        </div>
    </div>
</div>