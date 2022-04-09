{* Общий шаблон одного контейнера *}
{capture name="container" assign="content"}
    {if $container.wrap_element}<{$container.wrap_element} class="{$container.wrap_css_class}">{/if}
    {if !$container.invisible}
        <div class="{$container->renderElementClass($layouts.grid_system)}{if $this_controller->getDebugGroup()} container-wrapper{/if}" {if $this_controller->getDebugGroup() && empty($container.wrap_element)}data-container-id="{$container.id}" data-page-id="{$container.page_id}" data-section-id="-{$container.type}"{/if}>
    {/if}
        {$wrapped_content=""}

        {if $layouts.sections[$container.id]}
            {include file="%system%/gs/{$layouts.grid_system}/sections.tpl" item=$layouts.sections[$container.id] assign=wrapped_content}
        {/if}

        {if $container.inside_template}
            {include file=$container.inside_template wrapped_content=$wrapped_content}
        {/if}

        {if $this_controller->getDebugGroup()}
            {include file='%system%/debug/container_inwrap.tpl' wrapped_content=$wrapped_content assign=wrapped_content}
        {/if}

        {$wrapped_content}

    {if !$container.invisible}
        </div>
    {/if}
    {if $container.wrap_element}</{$container.wrap_element}>{/if}
{/capture}
{if $this_controller->getDebugGroup()}
    {* Добавляем разметку для перетаскивания контейнера в режиме отладки *}
    {include file='%system%/debug/container_wrap.tpl' wrapped_content=$content}
{else}
    {$content}
{/if}