<div class="dashed-stub">
    <div class="dashed-stub__wrapper">
        <h4 class="dashed-stub__title">{$name}</h4>
        {if $current_user->isAdmin() && !$this_controller->getDebugGroup()}
            <p class="dashed-stub__help">{t}Чтобы настроить блок, нужно:{/t}</p>
            <ol class="dashed-stub__actions">
                {foreach $do as $action}
                    <li>
                        {if $action.href}
                            <a href="{$action.href}" {if $action.class}class="{$action.class}"{/if}>{$action.title}</a>
                        {else}
                            {$action.title}
                        {/if}
                    </li>
                {/foreach}
            </ol>
        {/if}
    </div>
</div>