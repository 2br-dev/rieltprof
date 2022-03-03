{addjs file="jquery.tablednd/jquery.tablednd.js" basepath="common"}
{addjs file="%catalog%/propertyblock.js"}
{addcss file="%catalog%/propertyblock.css"}

<div id="pvl-block" data-urls='{ "edit":"{$router->getAdminUrl(false, ['ido' => 'edit', 'prop_id' => $elem.id], 'catalog-block-propertyvaluesblock')}",
                                 "addSomeValues": "{$router->getAdminUrl(false, ['ido' => 'addsomevalues', 'prop_id' => $elem.id], 'catalog-block-propertyvaluesblock')}",
                                 "naturalSort":"{$router->getAdminUrl(false, ['ido' => 'naturalsort', 'prop_id' => $elem.id], 'catalog-block-propertyvaluesblock')}",
                                 "refresh":"{$router->getAdminUrl(false, ['prop_id' => $elem.id], 'catalog-block-propertyvaluesblock')}",
                                 "remove":"{$router->getAdminUrl(false, ['ido' => 'remove', 'prop_id' => $elem.id], 'catalog-block-propertyvaluesblock')}" }'>

    <div class="filter-line virtual-form" data-action="{adminUrl do=false prop_id=$prop_id pvl_page_size=$offer_page_size pvl_page=$pvl_page mod_controller="catalog-block-propertyvaluesblock"}">
        <div class="filter-top">
            <a class="openfilter va-m-c" onclick="$(this).closest('.filter-line').toggleClass('open'); return false;">
                <i class="zmdi zmdi-search f-18"></i>
                <span>{t}Фильтр{/t}</span>
            </a>
            {if count($filter_parts)>1}
            <span class="part clean_all"><a class="clean" data-href="{adminUrl do=false prop_id=$prop_id mod_controller="catalog-block-propertyvaluesblock"}"></a></span>            
            {/if}
            {foreach $filter_parts as $part}
                <span class="part">{$part.text}<a class="clean" data-href="{$part.clean_url}"></a></span>
            {/foreach}
        </div>
        <table class="filter-form">
            <tr>
                <td class="key">{t}Название{/t}</td>
                <td class="val"><input type="text" name="pvl_filter[value]" value="{$filter.value}"></td>
            </tr>
            <tr>
                <td></td>
                <td><span class="btn btn-primary virtual-submit">{t}Применить{/t}</span></td>
            </tr>
        </table>
    </div>

    <div class="tools-top">
        <a class="btn btn-default va-m-c add-item m-r-20"><i class="zmdi zmdi-plus m-r-5 f-18"></i>{t}Добавить значение{/t}</a>
        <a class="btn btn-default va-m-c add-list m-r-20"><i class="zmdi zmdi-collection-plus m-r-5 f-18"></i>{t}Добавить несколько значений{/t}</a>
        <a class="btn btn-default va-m-c natural-sort-list"><i class="zmdi zmdi-sort-amount-asc m-r-5 f-18"></i>{t}Сортировать по возрастанию{/t}</a>
    </div>

    <div class="table-mobile-wrapper">
        <table class="rs-table editable-table values-list localform" data-sort-request="{$router->getAdminUrl(false, ['ido' => 'move', 'prop_id' => $prop_id], 'catalog-block-propertyvaluesblock')}" data-refresh-url="{adminUrl do=false prop_id=$prop_id pvl_filter=$filter pvl_page=$paginator->page pvl_page_size=$pvl_page_size mod_controller="catalog-block-propertyvaluesblock"}">
            <thead>
                <tr>
                    <th class="chk" style="width:26px">
                        <div class="chkhead-block">
                            <input type="checkbox" data-name="value_items[]" class="chk_head select-page" title="{t}Отметить элементы на этой странице{/t}">
                            <div class="onover">
                                <input type="checkbox" class="select-all" value="on" name="selectAll" title="{t}Отметить элементы на всех страницах{/t}">
                            </div>
                        </div>
                    </th>
                    <th class="drag" width="20"><span class="sortable sortdot asc"><span></span></span></th>
                    <th class="title">{t}Название{/t}</th>
                    <th></th>
                    <th class="actions"></th>
                </tr>
            </thead>
            <tbody>
            {foreach $items as $key => $property_value}
                <tr class="item" data-id="{$property_value.id}">
                    <td class="chk"><input type="checkbox" name="value_items[]" value="{$property_value.id}"></td>
                    <td class="drag drag-handle">
                        <a class="sort dndsort" data-sortid="{$property_value.id}">
                            <i class="zmdi zmdi-unfold-more"></i>
                        </a>
                    </td>
                    <td class="title clickable">{$property_value.value}</td>
                    <td>{$property_value->getAdminItemView($prop_type)}</td>
                    <td class="actions">
                        <span class="loader"></span>
                        <span class="tools">
                            <a class="tool edit-button" title="{t}Редактировать{/t}"><i class="zmdi zmdi-edit"></i></a>
                            <a class="tool c-red remove-button" title="{t}удалить{/t}"><i class="zmdi zmdi-delete"></i></a>
                        </span>
                    </td>
                </tr>
            {/foreach}
            {if empty($items)}
                <tr class="empty-row no-hover">
                    <td colspan="5">{t}нет значений{/t}</td>
                </tr>
            {/if}
            </tbody>
        </table>
    </div>

    <div class="tools-bottom">
        <div class="paginator virtual-form" data-action="{adminUrl do=false prop_id=$prop_id pvl_filter=$filter mod_controller="catalog-block-propertyvaluesblock"}">
            {$paginator->getView(['is_virtual' => true])}
        </div>
    </div>

    <div class="group-toolbar">
        <span class="checked-offers">{t}Отмеченные<br> значения:{/t}</span> <a class="btn btn-danger delete">{t}Удалить{/t}</a>
    </div>
</div>