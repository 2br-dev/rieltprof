{$config = \RS\Config\Loader::byModule('rieltprof')}
{$current_user = \RS\Application\Auth::getCurrentUser()}
{addjs file="rs.ajaxpagination.js"}
<table class="table-view" data-mode="list">
    <thead>
    <tr>
{*        <th>&nbsp;</th>*}
{*        <th>&nbsp;</th>*}
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th class="features"> </th>
        <th class="district">Район</th>
        <th class="type">Тип объекта</th>
        <th class="price">Стоимость</th>
        <th class="rooms">Комнат</th>
        <th class="square">Площадь</th>
        <th class="date">Дата</th>
{*        <th class="profile">Профиль</th>*}
        <th class="phone">Телефон</th>
    </tr>
    </thead>
    <tbody id="last-released-data" class="ads-list">
        {if $total}
            {$ad_list_html}
        {/if}
    </tbody>
</table>
{if $paginator->total_pages > $paginator->page}
    <div class="text-center more-wrapper">
        <a
            data-pagination-options='{ "appendElement":".ads-list" }'
            data-url="{$router->getUrl('rieltprof-block-allads', ['_block_id' => $_block_id, 'p' => $paginator->page+1, 'aid' => $aid])}"
            data-scroll-element=".main-block"
            class="rs-ajax-paginator"
        ></a>
    </div>
{/if}
