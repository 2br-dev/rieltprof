{addcss file="%crm%/board.css"}
{addjs file="%crm%/jquery.rs.ajaxpagination.js"}
{addjs file="%crm%/jquery.rs.board.js"}

<div class="viewport crm-filter-line">
    <div class="dropdown">
        <a id="task-filter-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{$objects_types[$current_object_type]->getTitle()} <i class="zmdi zmdi-chevron-down"></i></a>
        <ul class="dropdown-menu" aria-labelledby="task-filter-switcher">
            {foreach $objects_types as $key => $object}
                <li{if $current_object_type == $key} class="act"{/if}>
                    <a href="{urlmake type=$key filter=null}" class="call-update">{$object->getTitle()}</a>
                </li>
            {/foreach}
        </ul>
    </div>

    {foreach $filters as $key => $filter}
        <div class="dropdown">
            <a id="task-filter-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{$filters[$key][$current_filter[$key]]} <i class="zmdi zmdi-chevron-down"></i></a>
            <ul class="dropdown-menu" aria-labelledby="task-filter-switcher">
                {foreach $filter as $value => $title}
                    <li{if $current_filter[$key] == $value} class="act"{/if}>
                        {$new_filter=$current_filter}
                        {$new_filter[$key]=$value}
                        <a href="{urlmake filter=$new_filter}" class="call-update">{$title}</a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/foreach}
</div>

<div class="table-mobile-wrapper no-bottom-border">
    <div class="viewport">
        <div class="crm-status-columns" id="crm-board" data-sort-url="{adminUrl do="ajaxSortElement" type=$current_object_type}">
        {foreach $statuses as $status}
            <div class="crm-status-column" data-status-id="{$status.id}">
                <div class="crm-column-title" style="border-color: {$status.color}">
                    {$status.title}
                </div>

                {foreach $items_html[$status.id] as $item_html}
                    {$item_html}
                {/foreach}
            </div>
        {/foreach}
        </div>
    </div>
</div>

<script>
    $.allReady(function() {
        $('#crm-board').board();
        $('.ajaxPaginator', this).ajaxPagination();
    });
</script>