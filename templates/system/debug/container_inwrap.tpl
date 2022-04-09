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
{$wrapped_content}