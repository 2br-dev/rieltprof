{* Список товаров, который может быть в 2х видов *}
{$config = \RS\Config\Loader::byModule('rieltprof')}
<div class="object-list">
    {moduleinsert name="\Main\Controller\Block\BreadCrumbs"}
    {if $query == ""}
        <div class="title">
            <span>{$category['name']}</span>
            <span class="title-count-object"> ({$total})</span>
        </div>
    {else}
        <div class="title">
            <span>Результаты поиска: {$query}</span>
        </div>
    {/if}

    <div class="table-wrapper">
        <table
            class="table-view"
            {if isset($smarty.cookies.view_mode) && $smarty.cookies.view_mode == 'list'}
                data-mode="list"
                id="table-list"
            {else}
                data-mode="cards"
                id="table-cards"
            {/if}
        >
            <thead>
            <tr>
                <th> </th>
                <th>&nbsp;</th>
                <th class="features">&nbsp;</th>
                {if $query != ""}
                    <th class="type">Тип объекта</th>
                {/if}
                <th class="district">Район</th>
                <th class="price">Стоимость</th>
                <th class="rooms">Комнат</th>
                <th class="square">Площадь</th>
                <th class="date">Дата</th>
{*                <th class="profile">Автор</th>*}
                <th class="phone">Телефон</th>
            </tr>
            </thead>
            <tbody id="last-released-data" class="ads-list">
            {foreach $list as $product}
                {include file="%catalog%/product-tr.tpl" product=$product}
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
