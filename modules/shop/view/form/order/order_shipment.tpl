{addjs file="{$mod_js}rs.ordershipment.js" basepath="root"}
{addcss file="{$mod_css}ordershipment.css" basepath="root"}
{$shop_config = ConfigLoader::byModule('shop')}

{$action = $router->getAdminUrl('shipment', [order_id => $order.id])}
{$url_make_shipment = $router->getAdminUrl('makeShipment', [order_id => $order.id])}
<div class="rs-order-shipment"
     data-order-id="{$order.id}"
     data-url-parse-code="{adminUrl do='parseCode'}"
     data-url-make-shipment="{$url_make_shipment}"
     data-order-shipment-options='{ "checkConformityUitToBarcode":"{$shop_config.check_conformity_uit_to_barcode}" }'>

    <ul class="blank-uit-list-item hidden">
        <li class="uit-list-item" data-id="%gtin%serial">
            <input type="checkbox" name="shipment[%cart_item_uniq][uit][]" value="%gtin%serial" class="hidden" checked>
            <input name="uit[%cart_item_uniq][%gtin%serial][01]" type="hidden" value="%gtin">
            <input name="uit[%cart_item_uniq][%gtin%serial][21]" type="hidden" value="%serial">
            <span class="uit-list-item-field">
                <span class="uit-list-item-field-title">gtin:</span>
                <span>%gtin</span>
            </span>
            <span class="uit-list-item-field">
                <span class="uit-list-item-field-title">serial:</span>
                <span>%serial</span>
            </span>
            <i class="uit-list-item-remove rs-icon-cross"></i>
        </li>
    </ul>

    <form method="post" action="{$action}" class="crud-form order-shipment-form">
        <div class="order-shipment-error-float-head empty"></div>

        <div class="order-shipment-table">
            <div class="order-shipment-table-head">
                <div class="column-image"></div>
                <div class="column-name">{t}Наименование{/t}</div>
                <div class="column-article">{t}Артикул{/t}</div>
                <div class="column-barcode">{t}Штрихкод{/t}</div>
                <div class="column-amount">{t}Кол-во{/t}</div>
                <div class="column-shipped-amount">{t}Отгружено{/t}</div>
                <div class="column-uit">{t}Серийные номера{/t}</div>
            </div>

            {foreach $product_items as $item}
                {$cart_item = $item.cartitem}
                {$product = $cart_item->getEntity()}
                {if !empty($shipped_amount[$cart_item.uniq])}
                    {$already_shipped = $shipped_amount[$cart_item.uniq]}
                {else}
                    {$already_shipped = 0}
                {/if}

                <div class="order-shipment-table-row order-shipment-item"
                     data-id="{$cart_item.uniq}"
                     data-product-id="{$product.id}"
                     data-barcode="{$cart_item.sku}"
                     data-is-marked="{(bool)$product.marked_class}"
                     data-already-shipped-amount="{$already_shipped}"
                     data-total-amount="{$cart_item.amount}">

                    <div class="column-image"><img src="{$product->getMainImage(40, 40)}" alt=""></div>
                    <div class="column-name">
                        {$cart_item.title}<br>
                        {if !empty($cart_item.model)}{t}Модель{/t}: {$cart_item.model}{/if}

                        {if $product.multioffers.use}
                            {$multioffers_values = unserialize($item.cartitem.multioffers)}
                            <div>
                                {foreach $product.multioffers.levels as $level}
                                    {foreach $level.values as $value}
                                        {if $value.val_str == $multioffers_values[$level.prop_id].value}
                                            <div class="offer_subinfo">
                                                {if $level.title}{$level.title}{else}{$level.prop_title}{/if} : {$value.val_str}
                                            </div>
                                        {/if}
                                    {/foreach}
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                    <div class="column-article">
                        <span class="mobile-column-title">{t}Артикул{/t}:</span>
                        {if $cart_item.barcode}{$cart_item.barcode}{else}&mdash;{/if}
                    </div>
                    <div class="column-barcode">
                        <span class="mobile-column-title">{t}Штрихкод{/t}:</span>
                        {if $cart_item.sku}{$cart_item.sku}{else}&mdash;{/if}
                    </div>
                    <div class="column-amount"><span class="mobile-column-title">{t}Кол-во{/t}:</span> {$cart_item.amount}</div>
                    <div class="column-shipped-amount">
                        {if !$product.marked_class}
                            <input type="hidden" name="shipment[{$cart_item.uniq}][amount]" value="{$cart_item.amount - $already_shipped}">
                        {/if}
                        <span class="mobile-column-title">{t}Отгружено{/t}:</span> {$already_shipped}
                    </div>
                    <div class="column-uit order-shipment-item-uit">
                        {if $product.marked_class && $cart_item.sku}
                            <ul class="order-shipment-item-uit-list">
                                {foreach $cart_item->getUITs() as $uit}
                                    {$uit_id = $uit.gtin|cat:$uit.serial}
                                    {$is_uit_shipped = in_array($uit_id, $shipped_uits)}

                                    <li class="uit-list-item" data-id="{$uit_id}">
                                        {if !$is_uit_shipped}
                                            <input type="checkbox" name="shipment[{$cart_item.uniq}][uit][]" value="{$uit_id}" class="hidden" checked>
                                        {/if}
                                        <input name="uit[{$cart_item.uniq}][{$uit_id}][01]" type="hidden" value="{$uit.gtin}">
                                        <input name="uit[{$cart_item.uniq}][{$uit_id}][21]" type="hidden" value="{$uit.serial}">
                                        <span class="uit-list-item-field">
                                        <span class="uit-list-item-field-title">gtin:</span>
                                        <span>{$uit.gtin}</span>
                                    </span>
                                        <span class="uit-list-item-field">
                                        <span class="uit-list-item-field-title">serial:</span>
                                        <span>{$uit.serial}</span>
                                    </span>
                                        {if $is_uit_shipped}
                                            <span class="uit-hint">{t}отгружен{/t}</span>
                                        {else}
                                            <i class="uit-list-item-remove rs-icon-cross"></i>
                                        {/if}
                                    </li>
                                {/foreach}
                            </ul>
                            <div class="order-shipment-item-uit-actions">
                                <input type="text" class="order-shipment-item-uit-input">
                                <div class="order-shipment-item-btn-add-uit btn btn-primary">{t}добавить{/t}</div>
                            </div>
                        {else}
                            {if !$product.marked_class}
                                <span class="uit-hint">{t}Товар не подлежит маркировке{/t}</span>
                            {elseif !$cart_item.sku}
                                <span class="uit-hint">{t}Не указан штрихкод{/t}</span>
                            {/if}
                        {/if}
                    </div>
                </div>
            {/foreach}
            <label class="order-shipment-table-row order-shipment-item">
                <div class="column-image"><input type="checkbox" name="add_delivery"></div>
                <div class="column-name">{t}Добавить в чек отгрузки позицию доставки{/t}</div>
            </label>
            <label class="order-shipment-table-row order-shipment-item">
                <div class="column-image"><input type="checkbox" name="create_receipt" {if $shop_config.create_receipt_upon_shipment}checked disabled{/if}></div>
                <div class="column-name">{t}Отправить чек отгрузки{/t}</div>
            </label>
            <div class="order-shipment-table-info"></div>
        </div>
    </form>
</div>
