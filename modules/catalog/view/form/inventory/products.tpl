{addjs file="%catalog%/selectproduct.js?v=8" basepath="root"}
{addjs file="%catalog%/jquery.inventory.js?v=4" basepath="root"}
{addcss file="%catalog%/selectproduct.css" basepath="root"}

{$api = $elem->getApi()}
{$movement_api = $api->getMovementApi()}
{$is_inventory = $field->getName() == 'inventory_products'}
{$type = $router->getCurrentRoute()->getExtra('type', 'writeoff')}
{$types = $api->getDocumentTypes()}

<div class="document" data-id="{$elem->id}">
    <div class="table-mobile-wrapper">
        <div class="order-beforetable-tools" style="margin: 20px 0">
            <a class="btn btn-alt btn-primary va-m-c m-r-10 addproduct" {if $elem.archived || $elem.inventory_disabled || $elem.has_links} disabled {/if}>
                <i class="zmdi zmdi-plus f-21"></i>
                <span class="m-l-5 hidden-xs">{t}Добавить товар{/t}</span>
            </a>
            <a class="btn btn-alt btn-primary va-m-c m-r-10 loadCsv" data-url="{$router->getAdminUrl('loadProductsCsv', ["type" => $type], 'catalog-inventoryctrl')}"
               data-crud-dialog-height="300" data-crud-dialog-width="500" {if $elem.archived || $elem.inventory_disabled || $elem.has_links} disabled {/if}>
                <i class="zmdi zmdi-upload f-21"></i>
                <span class="m-l-5 hidden-xs">{t}Импорт из CSV{/t}</span>
            </a>
            {if $elem.id}
                <a class="btn btn-alt btn-primary va-m-c m-r-10 getCsv" target="_blank" href="{$router->getAdminUrl('getProductsCsv', ["id" => $elem.id, "type" => $type], 'catalog-inventoryctrl')}">
                    <i class="zmdi zmdi-download f-21"></i>
                    <span class="m-l-5 hidden-xs">{t}Экспорт в CSV{/t}</span>
                </a>
                <a class="btn btn-alt btn-primary va-m-c m-r-10 getCsv" target="_blank" href="{$router->getAdminUrl('GetDocumentPrintForm', ["document_id" => $elem.id, "document_type" => $type], 'catalog-inventoryctrl')}">
                    <i class="zmdi zmdi-download f-21"></i>
                    <span class="m-l-5 hidden-xs">{t}Скачать форму для печати{/t}</span>
                </a>
            {/if}
            <a class="multi_delete btn btn-danger btn-alt va-m-c removeproduct " {if $elem.archived || $elem.inventory_disabled || $elem.has_links} disabled {/if}>
                <i class="zmdi zmdi-delete f-21"></i>
                <span class="m-l-5 hidden-xs" >{t}Удалить выбранное{/t}</span>
            </a>
            {if $elem.type == $types.reserve}
                <a class="btn btn-alt btn-primary va-m-c m-r-10 crud-get" {if $elem.archived || $elem.inventory_disabled || $elem.has_links} disabled {/if}
                   href="{$router->getAdminUrl('makeWriteOff', ['id' => $elem.id], 'catalog-inventoryreservationctrl')}"  style="margin-left: 10px">
                    <i class="zmdi zmdi-redo f-21"></i>
                    <span class="m-l-5 hidden-xs">{t}Перевести в списания{/t}</span>
                </a>
            {elseif $elem.type == $types.waiting}
                <a class="btn btn-alt btn-primary va-m-c m-r-10 crud-get" {if $elem.archived || $elem.inventory_disabled} disabled {/if}
                   href="{$router->getAdminUrl('MakeArrival', ['id' => $elem.id], 'catalog-inventorywaitingsctrl')}" style="margin-left: 10px">
                    <i class="zmdi zmdi-redo f-21"></i>
                    <span class="m-l-5 hidden-xs">{t}Перевести в оприходование{/t}</span>
                </a>
            {/if}
            <input class="barcode-scanner" data-href="{$router->getAdminUrl('getProductBySku', array(), 'catalog-ctrl')}" type="text" placeholder="Добавить по штрихкоду">
            <a class="help-icon" data-placement="right" data-original-title="Введите штрихкод товара/комплектации и нажмите Enter. Вы можете использовать сканер, в этом случае переводить курсор в поле не обязательно." title="">?</a>
        </div>
        <table class="pr-table rs-space-table" data-getnum="{$router->getAdminUrl('GetNum', null, 'catalog-inventorizationctrl')}">
            <thead>
            <tr style="border-bottom: solid 1px #dddddd">
                <td class="chk" style="width: 50px;">
                    <input type="checkbox" class="chk_head select-page" title="{t}Выбрать все товары{/t}">
                </td>
                <td>{t}Наименование{/t}</td>
                <td>{t}Комплектация{/t}</td>
                {if $is_inventory}
                    <td style="width: 100px;">{t}Расчетное кол-во{/t}</td>
                    <td style="width: 100px;">{t}Фактическое кол-во{/t}</td>
                    <td style="width: 100px;">{t}Разница{/t}</td>
                {else}
                <td style="width: 100px;">{t}Кол-во{/t}</td>
                {/if}
                <td style="width: 50px;"></td>
            </tr>
            </thead>
            <tbody class="ordersEdit">
                {$disable_edit = ($elem.archived || $elem.inventory_disabled || $elem.has_links)}
                {$api->getProductsTable($elem.id, $type, null, $elem.archived, $disable_edit)}
            </tbody>
        </table>
    </div>
    <div class="added-items">
        {$api->getAddedItems($elem.id, $type, null, $elem.archived)}
    </div>
    <div class="product-group-container hide-group-cb hidden" data-urls='{ "getChild": "{adminUrl mod_controller="catalog-dialog" do="getChildCategory"}", "getProducts": "{adminUrl mod_controller="catalog-dialog" do="getProducts"}", "getDialog": "{adminUrl mod_controller="catalog-dialog" do=false}" }'>
        <a href="JavaScript:;" class="select-button"></a><br>
        <div class="input-container"></div>
    </div>
    <input type="hidden" class="item-id" value="{$elem.id}">
    <input type="hidden" name="type" value="{$type}">
    {$products_to_add = $api->getProductsToAdd()}
    {foreach $products_to_add as $id}
        <input type="hidden" class="hidden_id_to_add" value="{$id}">
    {/foreach}
</div>

<script>
    $.allReady(function() {
        $('.addproduct').inventory();
    });
</script>
