{$currencies=$elem->getCurrencies()}

<div class="filter-line virtual-form" data-action="{adminUrl do=false product_id=$product_id offer_page_size=$offer_page_size mod_controller="catalog-block-offerblock"}">
    <div class="filter-top">
        <a class="openfilter va-m-c" onclick="$(this).closest('.filter-line').toggleClass('open'); return false;">
            <i class="zmdi zmdi-search f-18"></i>
            <span>{t}Фильтр{/t}</span>
        </a>
        {if count($filter_parts)>1}
        <span class="part clean_all"><a class="clean" data-href="{adminUrl do=false product_id=$product_id mod_controller="catalog-block-offerblock"}"></a></span>            
        {/if}
        {foreach $filter_parts as $part}
            <span class="part">{$part.text}<a class="clean" data-href="{$part.clean_url}"></a></span>
        {/foreach}
    </div>
    <table class="filter-form">
        <tr>
            <td class="key">{t}Название{/t}</td>
            <td class="val"><input type="text" name="offer_filter[title]" value="{$filter.title}"></td>
        </tr>
        <tr>
            <td class="key">{t}Артикул{/t}</td>
            <td class="val"><input type="text" name="offer_filter[barcode]" value="{$filter.barcode}"></td>
        </tr>
        <tr>
            <td class="key">{t}Общий остаток{/t}</td>
            <td class="val"><select name="offer_filter[cmp_num]">
                <option {if $filter.cmp_num == '='}selected{/if}>=</option>
                <option {if $filter.cmp_num == '&lt;'}selected{/if}>&lt;</option>
                <option {if $filter.cmp_num == '&gt;'}selected{/if}>&gt;</option>
            </select>
            <input type="text" name="offer_filter[num]" value="{$filter.num}">
            </td>
        </tr>
        <tr>
            <td></td>
            <td><span class="btn btn-primary virtual-submit">{t}Применить{/t}</span></td>
        </tr>
    </table>
</div>

<div class="tools-top">
    <a class="btn btn-success add-offer va-m-c">
        <i class="zmdi zmdi-plus m-r-5 f-18"></i>
        <span>{t}Добавить комплектацию{/t}</span>
    </a>
</div>

<div class="table-mobile-wrapper">
    <table class="rs-table editable-table offer-list localform" data-sort-request="{$router->getAdminUrl(false, ['odo' => 'offerMove', 'product_id' => $product_id], 'catalog-block-offerblock')}" data-refresh-url="{adminUrl do=false product_id=$product_id offer_filter=$filter offer_page=$paginator->page offer_page_size=$offer_page_size mod_controller="catalog-block-offerblock"}">
        <thead>
            <tr>
                <th class="chk" style="width:26px">
                    <div class="chkhead-block">
                        <input type="checkbox" data-name="offers[]" class="chk_head select-page" title="{t}Отметить элементы на этой странице{/t}">
                        <div class="onover">
                            <input type="checkbox" class="select-all" value="on" name="selectAll" title="{t}Отметить элементы на всех страницах{/t}">
                        </div>
                    </div>
                </th>
                <th class="drag" width="20"><span class="sortable sortdot asc"><span></span></span></th>
                <th class="title">{t}Название{/t}</th>
                <th class="barcode">{t}Артикул{/t}</th>
                <th class="amount">{t}Остаток{/t}</th>
                <th class="price">{t}Цена{/t}</th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
        {foreach $offers as $key => $offer}
            <tr class="item" data-id="{$offer.id}">
                <td class="chk"><input type="checkbox" name="offers[]" value="{$offer.id}"></td>
                <td class="drag drag-handle"><a data-sortid="{$offer.id}" class="sort dndsort">
                        <i class="zmdi zmdi-unfold-more"></i>
                    </a></td>
                <td class="title clickable">{$offer.title}</td>
                <td class="barcode clickable">{$offer.barcode}</td>
                <td class="amount">{$offer.num}</td>
                <td class="price clickable">
                    {if $offer.pricedata_arr.oneprice.use}
                        {$offer.pricedata_arr.oneprice.znak} {$offer.pricedata_arr.oneprice.original_value|default:"0"}
                        {if $offer.pricedata_arr.oneprice.unit == '%'}%{else}{$currencies[$offer.pricedata_arr.oneprice.unit]}{/if}
                    {else}
                        {foreach $elem->getCostList() as $onecost}
                            {if $onecost.type != 'auto'}
                                <div class="one-price">
                                    {$offer.pricedata_arr.price[$onecost.id].znak|default:"+"}
                                    {$offer.pricedata_arr.price[$onecost.id].original_value|default:"0"}
                                    {$unit=$offer.pricedata_arr.price[$onecost.id].unit}
                                    {if $unit == '%'}%{elseif $unit>0}{$currencies[$unit]}{else}{$currencies[$default_currency.id]}{/if}
                                     {$onecost.title}
                                </div>
                            {/if}
                        {/foreach}
                    {/if}
                </td>
                <td class="actions">
                    <span class="loader"></span>
                    <div class="inline-tools">
                        <a class="tool offer-edit" title="{t}Редактировать{/t}"><i class="zmdi zmdi-edit"></i></a>
                        <a class="tool offer-change-with-main" title="{t}сделать основной{/t}"><i class="zmdi zmdi-upload"></i></a>
                        <a class="tool offer-del" title="{t}удалить{/t}"><i class="zmdi zmdi-delete c-red"></i></a>
                    </div>
                </td>
            </tr>
        {/foreach}
        {if empty($offers)}
            <tr class="empty-row no-hover">
                <td colspan="7">{t}нет дополнительных комплектаций{/t}</td>
            </tr>
        {/if}
        </tbody>
    </table>
</div>

<div class="tools-bottom">
    <div class="paginator virtual-form" data-action="{adminUrl do=false product_id=$product_id offer_filter=$filter mod_controller="catalog-block-offerblock"}">
        {$paginator->getView(['is_virtual' => true])}
    </div>
</div>

<div class="group-toolbar">
    <span class="checked-offers">{t}Отмеченные<br> значения:{/t}</span>
    <a class="btn btn-default edit">{t}Редактировать{/t}</a>
    <a class="btn btn-danger delete">{t}Удалить{/t}</a>
</div>