{strip}
{if $this_controller->getDebugGroup()}
    {if $layouts.grid_system != 'gs960'}
        {addcss file="%templates%/manager.css"}
    {/if}
    <div id="all-containers-wrapper" class="all-containers-wrapper" data-page-id="{$layouts.page_id}" data-clone-url="{$router->getAdminUrl('copyContainer', ['context' => $layouts.theme_data.blocks_context, 'ajax' => 1], 'templates-blockctrl')}" data-sort-url="{$router->getAdminUrl('ajaxMoveContainer', [], 'templates-blockctrl')}">
{/if}
{foreach from=$layouts['containers'] item=container}
    {include file="%system%/gs/container.tpl" container=$container assign=wrapped_content page_id=$layouts.page_id theme_context=$layouts.theme_data.blocks_context}
    {if $container.outside_template}
        {include file=$container.outside_template wrapped_content=$wrapped_content}
    {else}
        {$wrapped_content}
    {/if}    
{/foreach}
{if $this_controller->getDebugGroup()}
    </div>
    <div class="container-add-block">
        <a href="{$router->getAdminUrl('addModule', ['type' => $layouts.max_container_type+1, 'page_id' => $layouts.page_id, 'context' => $layouts.theme_data.blocks_context], 'templates-blockctrl')}"
           data-crud-options='{ "dialogId": "blockListDialog", "beforeCallback": "addConstructorModuleSectionId", "type": "{$layouts.max_container_type+1}", "pageId": "{$layouts.page_id}", "context": "{$layouts.theme_data.blocks_context}" }'
           class="crud-add btn btn-success" target="_blank">{t}добавить блок{/t}</a>
    </div>
    <div class="container-add-wrapper">
        <a href="{$router->getAdminUrl('addContainer', ['type' => $layouts.max_container_type+1, 'page_id' => $layouts.page_id, 'context' => $layouts.theme_data.blocks_context], 'templates-blockctrl')}" class="crud-add btn btn-success">{t}добавить контейнер{/t}</a>
        <a href="{$router->getAdminUrl('copyContainer', ['type' => $layouts.max_container_type+1, 'page_id' => $layouts.page_id, 'context' => $layouts.theme_data.blocks_context], 'templates-blockctrl')}" class="crud-add btn btn-default">{t}добавить контейнер клонированием{/t}</a>
    </div>
{/if}
{/strip}