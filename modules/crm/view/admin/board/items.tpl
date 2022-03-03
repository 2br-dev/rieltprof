<div class="crm-column-items status-id-{$status_id}" data-status-id="{$status_id}">
    {foreach $items as $item}
        <div class="crm-column-item" data-id="{$item.id}">
            {include file=$current_object->getItemTemplate() item=$item}
        </div>
    {/foreach}

    {if $paginator->total_pages > $paginator->page}
        <a data-pagination-options='{ "appendElement":".crm-column-items.status-id-{$status.id}", "context":".crm-status-column", "appendElement": ".crm-column-items" }'
           data-href="{adminUrl do="ajaxGetElements"
           type=$current_object_type
           filter=$current_filter
           status_id=$status_id
           page=$paginator->page+1}"
           class="crm-one-more ajaxPaginator">{t}показать еще{/t}</a>
    {/if}
</div>
