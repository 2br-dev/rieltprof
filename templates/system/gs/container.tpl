{* Общий шаблон одного контейнера *}
{strip}
{if $layouts.grid_system == 'gs960'}{$container_grid_class="container_{$container.columns}"}
{elseif $layouts.grid_system == 'bootstrap' || $layouts.grid_system == 'bootstrap4'}{$container_grid_class="container{if $container.is_fluid}-fluid{/if}"}{/if}
{if $container.wrap_element}<{$container.wrap_element} class="{$container.wrap_css_class} container-wrapper" data-container-id="{$container.id}" data-page-id="{$container.page_id}" data-section-id="-{$container.type}">{/if}
<div class="{$container_grid_class} {$container.css_class}{if $this_controller->getDebugGroup() && empty($container.wrap_element)} container-wrapper{/if}" {if $this_controller->getDebugGroup() && empty($container.wrap_element)}data-container-id="{$container.id}" data-page-id="{$container.page_id}" data-section-id="-{$container.type}"{/if}>
    {if $layouts.sections[$container.id]}
        {include file="%system%/gs/{$layouts.grid_system}/sections.tpl" item=$layouts.sections[$container.id] assign=wrapped_content}
    {else}
        {$wrapped_content=""}
    {/if}

    {if $this_controller->getDebugGroup()}
        <span class="drag-container-handler drag-all-block-handler">{* Блок перетаскивания строки *}</span>
        <span class="drag-shadow-layer">{t}Контейнер будет перемещен сюда{/t}</span>
        <div class="container-tools debug-tools">
            <div class="dragblock">&nbsp;</div>
            <a href="{$router->getAdminUrl('addSection', ['parent_id' => -$container.type, 'page_id' => $container.page_id, 'element_type' => 'row'], 'templates-blockctrl')}" title="{t}Добавление строки{/t}" class="debug-icon crud-add debug-icon-create" target="_blank"></a>
            <a href="{$router->getAdminUrl('editContainer', ['id' => $container.id], 'templates-blockctrl')}" title="{t}Настройки контейнера{/t}" class="debug-icon crud-add debug-icon-blockoptions" target="_blank"></a>
            <a class="debug-icon debug-icon-delete crud-remove-one" href="{$router->getAdminUrl('removeContainer', ['id' => $container.id], 'templates-blockctrl')}" title="{t}удалить контейнер{/t}"></a>
        </div>
        {$empty_class=""}
        {if empty(trim($wrapped_content))}
            {$empty_class="is-empty"}
        {/if}
        {if $layouts.grid_system == 'gs960'} {* Специально для сетки gs960 *}
            {$empty_row_class=""}
            {if empty(trim($wrapped_content))}
                {$empty_row_class="row-empty"}
            {/if}
            {$wrapped_content="<div class='section-row-content "|cat:$empty_row_class|cat:"' data-section-id='-"|cat:$container.type|cat:"' data-sort-url='"|cat:{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}|cat:"'>"|cat:$wrapped_content|cat:"</div>"}
        {/if}
        {$wrapped_content="<div class='container-rows-content "|cat:$empty_class|cat:"' data-container-id='"|cat:$container.id|cat:"' data-section-id='-"|cat:$container.type|cat:"'>"|cat:$wrapped_content|cat:"</div>"}
    {/if}
    
    {if $container.inside_template}
        {include file=$container.inside_template wrapped_content=$wrapped_content}
    {else}
        {$wrapped_content}
    {/if}
</div>
{if $container.wrap_element}</{$container.wrap_element}>{/if}
{/strip}