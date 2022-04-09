{* Шаблон распределения товаров по коробкам *}
{addcss file="%shop%/ordercargo.css"}
{addjs file="%shop%/rs.ordercargo.js"}
<form class="crud-form m-t-15" method="POST" action="{urlmake}">
    <div class="order-cargo" data-add-cargo-url="{adminUrl do="getCargoForm"}">
        <div {if !$cargos}hidden{/if} class="cargo-list">
            <div class="cargo-list_head">
                <div>{t}Грузоместо{/t}</div>
                <div>
                    <div class="btn-group">
                        <a class="split-group btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{t}Добавить{/t} <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li {if $presets}class="cargo-border-bottom"{/if}>
                                <a class="cargo-add" data-preset-id="0">{t}Произвольное грузоместо{/t}</a>
                            </li>
                            {foreach $presets as $preset}
                                <li>
                                    <a class="cargo-add" data-preset-id="{$preset.id}">{$preset.title}</a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                    <a href="{adminUrl mod_controller="shop-cargopresetctrl"}"
                       title="{t}Перейти к справочнику упаковок{/t}"
                       target="_blank" class="btn btn-default"><i class="zmdi zmdi-layers"></i></a>
                </div>
            </div>
            <ul class="cargo-list-body">
                {foreach $cargos as $cargo}
                    {include file="%shop%/admin/cargo/cargo_item.tpl" cargo=$cargo}
                {/foreach}
            </ul>
        </div>
        <div {if !$cargos}hidden{/if} class="cargo-detail">
            <div class="cargo-forms">
                <div class="cargo-form">
                    <h3 class="m-t-0 m-b-15">{t}Параметры упаковки{/t}</h3>
                    <div class="cargo-form-container">
                    {foreach $cargos as $cargo}
                        {include file="%shop%/admin/cargo/cargo_form.tpl" cargo=$cargo}
                    {/foreach}
                    </div>
                </div>
            </div>

            <div class="cargo-products">
                <h3>{t}Товары в этой упаковке{/t}</h3>
                <p class="m-b-20">{t}Установите положительное количесто товарам, которые должны попасть в данную упаковку{/t}</p>
                {$product_items = $order->getCart()->getProductItems()}
                <div class="table-mobile-wrapper">
                    <table class="table">
                    <thead>
                        <tr>
                            <td width="60">{t}Картинка{/t}</td>
                            <td>{t}Наименование{/t}</td>
                            <td>{t}Артикул{/t}</td>
                            <td>{t}Штрихкод{/t}</td>
                            <td>{t}Количество{/t} <a class="btn btn-primary btn-small cargo-put-all" title="{t}Разместить все оставшиеся товары в этой коробке{/t}"><i class="zmdi zmdi-check-all"></i></a></td>
                        </tr>
                    </thead>
                    <tbody class="cargo-product-container">
                        {function productItem uit=null }
                            {$uit_id = $uit.id|default:"0"}
                            <tr class="cargo-product-item">
                                <td align="center">
                                    <img src="{$product->getOfferMainImage($cart_item.offer, 40, 40, 'xy')}">
                                </td>
                                <td>{$cart_item.title}
                                    {if !empty($cart_item.model)}{t}Модель{/t}: {$cart_item.model}{/if}

                                    {if $uit}
                                        <br><div class="cargo-uit">
                                            <span>{t}Маркировка{/t}: </span>
                                            <span class="c-gray">gtin:</span>: <strong>{$uit.gtin}</strong>
                                            <span class="c-gray">serial:</span>: <strong>{$uit.serial}</strong>
                                        </div>
                                    {/if}

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
                                </td>
                                <td>
                                    {if $cart_item.barcode}{$cart_item.barcode}{else}&mdash;{/if}
                                </td>
                                <td>
                                    {if $cart_item.sku}{$cart_item.sku}{else}&mdash;{/if}
                                </td>
                                <td>
                                    <div class="cargo-amount-wrapper">
                                        <div class="cargo-amount-container"
                                             data-max-amount="{$max_amount}"
                                             data-cartitem-key="{$key}"
                                             data-uit-id="{$uit.id}"
                                             data-amount-step="{$product->getAmountStep()}">

                                            {foreach $cargos as $cargo}
                                                <div class="cargo-amount" data-cargo-id="{$cargo.id}">
                                                    <input type="number" min="0" size="10" step="{$product->getAmountStep()}" value="{$cargo->getProductAmount($key, $uit_id)}" name="cargo[{$cargo.id}][products][{$key}][{$uit_id}][amount]">
                                                </div>
                                            {/foreach}
                                        </div>
                                        <div class="cargo-max-amount-column">
                                            {t}из{/t} <span class="cargo-max-amount">-</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        {/function}

                        {foreach $product_items as $key => $item}
                            {$cart_item = $item.cartitem}
                            {$product = $cart_item->getEntity()}
                            {$editable_amount = $cart_item.amount}
                            {if $product.marked_class && isset($shipped_uits[$key])}
                                {* Отображение товаров, которые промаркированы *}
                                {foreach $shipped_uits[$key] as $uit}
                                    {productItem product=$product cart_item=$cart_item max_amount=1 uit=$uit}
                                {/foreach}
                                {$editable_amount = $cart_item.amount - count($shipped_uits[$key])}
                            {/if}

                            {* Отображение оставшихся товаров, на которые нет маркировок *}
                            {if $editable_amount>0}
                                {productItem product=$product cart_item=$cart_item max_amount=$editable_amount}
                            {/if}
                        {/foreach}
                    </tbody>
                    <tbody class="cargo-empty-container" hidden>
                        <tr>
                            <td colspan="5">{t}Нет ни одного нераспределенного товара{/t}</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="cargo-empty" {if $cargos}hidden{/if}>
            <img src="{$Setup.IMG_PATH}/adminstyle/empty.svg">

            <p>{t}Не создано ни одного грузового места для данного заказа{/t}</p>
            <div class="btn-group">
                <a class="split-group btn btn-default dropdown-toggle " data-toggle="dropdown">{t}Добавить грузовое место{/t} <span class="caret"></span></a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a class="cargo-add" data-preset-id="0">{t}Произвольное грузоместо{/t}</a>
                    </li>
                    {foreach $presets as $preset}
                        <li>
                            <a class="cargo-add" data-preset-id="{$preset.id}">{$preset.title}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>
            <a href="{adminUrl mod_controller="shop-cargopresetctrl"}"
               title="{t}Перейти к справочнику упаковок{/t}"
               target="_blank" class="btn btn-default"><i class="zmdi zmdi-layers"></i></a>
        </div>
    </div>
</form>