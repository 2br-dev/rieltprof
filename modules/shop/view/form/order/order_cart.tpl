<h3>{t}Состав заказа{/t}</h3>

{if $order.id>0 && !$order->canEdit()}
    <div class="notice-box notice-danger">
        {t}Редактирование списка товаров невозможно, так как были удалены некоторые элементы заказа{/t}
    </div>
{/if}

<div class="order-beforetable-tools">
    <a class="btn btn-alt btn-primary va-m-c m-r-10 {if ($order.id>0) && !$order->canEdit()}disabled{/if} addproduct">
        <i class="zmdi zmdi-plus f-21"></i>
        <span class="m-l-5 hidden-xs">{t}Добавить товар{/t}</span>
    </a>
    <a class="btn btn-alt btn-primary va-m-c m-r-10 {if ($order.id>0) && !$order->canEdit()}disabled{/if} addcoupon">
        <i class="zmdi zmdi-labels f-21"></i>
        <span class="m-l-5 hidden-xs">{t}Добавить купон на скидку{/t}</span>
    </a>
    <a class="btn btn-alt btn-primary va-m-c m-r-10 {if ($order.id>0) && !$order->canEdit()}disabled{/if} addorderdiscount">
        <i class="zmdi zmdi-money-off f-18"></i>
        <span class="m-l-5 hidden-xs">{t}Добавить скидку на заказ{/t}</span>
    </a>
    <input class="barcode-scanner" type="text" placeholder="Добавить по штрихкоду" data-no-trigger-change data-href="{$router->getAdminUrl('getProductBySku', array(), 'catalog-ctrl')}">
    <a class="help-icon" data-placement="right" data-original-title="Введите штрихкод товара/комплектации и нажмите Enter. Вы можете использовать сканер, в этом случае переводить курсор в поле не обязательно." title="">?</a>
</div>

<div class="anti-viewport">
    <div class="table-mobile-wrapper">
        {hook name="shop-orderview:cart" title=t('Редактирование заказа(админ. панель): Корзина') order_data=$order_data products=$products catalog_config=$catalog_config}
            <table class="pr-table">
                <thead>
                <tr>
                    <th class="l-w-space"></th>
                    <th class="chk" style="text-align:center" width="20">
                        <input type="checkbox" data-name="chk[]" class="chk_head select-page" title="{t}Выбрать все товары{/t}">
                    </th>
                    <th></th>
                    <th>{t}Наименование{/t}</th>
                    <th>{t}Код{/t}</th>
                    <th>{t}Вес{/t} ({$catalog_config->getShortWeightUnit()})</th>
                    <th>{t}Цена{/t}</th>
                    <th>{t}Кол-во{/t}</th>
                    <th>{t}Стоимость{/t}</th>
                    <th class="r-w-space"></th>
                </tr>
                </thead>
                <tbody id="orderEditCartItems" class="ordersEdit">
                    {if !empty($order_data.items)}
                        {foreach $order_data.items as $n => $item}
                            {$product = $products[$n].product}
                            {include file="%shop%/form/order/order_cart_item.tpl" catalog_config=$catalog_config user=$user router=$router order=$order delivery=$delivery pay=$pay}
                        {/foreach}
                    {else}
                        <tr>
                            <td class="l-w-space"></td>
                            <td colspan="8" align="center">{t}Добавьте товары к заказу{/t}</td>
                            <td class="r-w-space"></td>
                        </tr>
                    {/if}
                </tbody>

                <tbody class="additems">

                {foreach $order_data.other as $key => $item}
                    <tr>
                        <td class="l-w-space"></td>
                        {if $item.cartitem.type=='coupon'}
                            <td class="chk">
                                <input type="checkbox" name="chk[]" value="{$key}" {if !$order->canEdit()}disabled{/if}>
                                <input type="hidden" name="items[{$key}][uniq]" value="{$key}" class="coupon">
                                <input type="hidden" name="items[{$key}][type]" value="coupon">
                                <input type="hidden" name="items[{$key}][entity_id]" value="{$item.cartitem.entity_id}">
                                <input type="hidden" name="items[{$key}][title]" value="{$item.cartitem.title}">
                            </td>
                        {/if}
                        {if $item.cartitem.type=='order_discount'}
                            <td class="chk">
                                <input type="checkbox" name="chk[]" value="{$key}" {if !$order->canEdit()}disabled{/if}>
                                <input type="hidden" name="items[{$key}][uniq]" value="{$key}" class="order_discount">
                                <input type="hidden" name="items[{$key}][type]" value="order_discount">
                                <input type="hidden" name="items[{$key}][entity_id]" value="{$item.cartitem.entity_id}">
                                <input type="hidden" name="items[{$key}][title]" value="{$item.cartitem.title}">
                                <input type="hidden" name="items[{$key}][price]" value="{$item.cartitem.price}">
                                <input type="hidden" name="items[{$key}][discount]" value="{$item.cartitem.discount}">
                            </td>

                        {/if}
                        <td colspan="{if $item.cartitem.type=='coupon' || $item.cartitem.type=='order_discount'}6{else}7{/if}">
                            {if $item.cartitem.type !='coupon' && $item.cartitem.type != 'order_discount'}
                                <input type="hidden" name="items[{$key}][uniq]" value="{$key}">
                                <input type="hidden" name="items[{$key}][type]" value="{$item.cartitem.type}">
                                <input type="hidden" name="items[{$key}][entity_id]" value="{$item.cartitem.entity_id}">
                            {/if}
                            {$item.cartitem.title}
                        </td>
                        <td>{if $item.total>0}{$item.total}{/if}</td>
                        <td class="r-w-space"></td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/hook}
    </div>
</div>

<div class="order-footer">
    <div>
        <a class="btn btn-danger btn-alt va-m-c removeproduct {if ($order.id>0) && !$order->canEdit()}disabled{/if}">
            <i class="zmdi zmdi-delete f-21"></i>
            <span class="m-l-5 hidden-xs">{t}Удалить выбранное{/t}</span>
        </a>
    </div>
    <div>
        <span class="weight m-r-15">
            {t}Вес:{/t} <span class="total_weight">{$order_data.total_weight}</span> ({$catalog_config->getShortWeightUnit()})
        </span>
        <span class="total-price">
            {t}Итого:{/t} <span class="summary">{$order_data.total_cost}</span>
            <a class="btn btn-warning refresh" onclick="$.orderEdit('refresh')">{t}пересчитать{/t}</a>
        </span>
    </div>
</div>

{if !is_null($returned_items)}
    {$counted_returned_items = count($returned_items)}
{else}
    {$counted_returned_items = 0}
{/if}

{if $counted_returned_items > 0}
    <h3>{t}Возвращенные товары{/t}</h3>
    <div class="table-mobile-wrapper">
        <table class="rs-table">
            <thead>
            <tr>
                <th>{t}Название{/t}</th>
                <th>{t}Количество{/t}</th>
                <th>{t}Номер возврата{/t}</th>
            </tr>
            </thead>
            <tbody class="ordersEdit">
            {foreach $returned_items as $item}
                <tr class="item">
                    <td class="l-w-space">{$item.title}</td>
                    <td class="l-w-space">{$item.amount}</td>
                    <td class="l-w-space">{$item->getReturn()->return_num}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/if}
{* Сюда будут вставлены элементы через "Добавить купон" и "Добавить товар" *}
<div class="added-items"></div>

{*  Блок-контейнер для инициализации диалога добавления товара  *}

<div class="product-group-container hide-group-cb hidden" data-urls='{ "getChild": "{adminUrl mod_controller="catalog-dialog" do="getChildCategory" site_id_context=$order.site_id}",
                                                                       "getProducts": "{adminUrl mod_controller="catalog-dialog" do="getProducts" site_id_context=$order.site_id}",
                                                                       "getDialog": "{adminUrl mod_controller="catalog-dialog" do=false site_id_context=$order.site_id}" }'>
    <a href="JavaScript:;" class="select-button"></a><br>
    <div class="input-container"></div>
</div>
<br><br>

{literal}
    <script type="text/javascript">
        // $('.barcode-scanner').codeScanner();
    </script>
{/literal}