{if !$refresh_mode}
    {addcss file="{$mod_css}orderview.css?v=2" basepath="root"}
    {addcss file="{$catalog_folders.mod_css}selectproduct.css" basepath="root"}

    {addcss file="common/lightgallery/css/lightgallery.min.css" basepath="common"}
    {addjs file="lightgallery/lightgallery-all.min.js" basepath="common"}
    {addjs file="jquery.ns-autogrow/jquery.ns-autogrow.min.js"}

    {addjs file="{$catalog_folders.mod_js}selectproduct.js" basepath="root"}
    {addjs file="jquery.rs.objectselect.js" basepath="common"}
    {addjs file="{$mod_js}jquery.orderedit.js" basepath="root"}
    <form id="orderForm" class="crud-form" method="post" action="{urlmake}">
{/if}

{$catalog_config = ConfigLoader::byModule('catalog')}
{$delivery = $elem->getDelivery()}
{$address = $elem->getAddress()}
{$cart = $elem->getCart()}
{$order_data = $cart->getOrderData(true, false)}
{$products = $cart->getProductItems()}

    <input type="hidden" name="order_id" value="{$elem.id}">
    <input type="hidden" name="user_id" value="{$elem.user_id|default:0}">
    <input type="hidden" name="delivery" value="{$elem.delivery|default:0}">
    <input type="hidden" name="use_addr" value="{$elem.use_addr|default:0}">
    <input type="hidden" name="address[zipcode]" value="{$address.zipcode}">
    <input type="hidden" name="address[country]" value="{$address.country}">
    <input type="hidden" name="address[region]" value="{$address.region}">
    <input type="hidden" name="address[city]" value="{$address.city}">
    <input type="hidden" name="address[address]" value="{$address.address}">

    <input type="hidden" name="address[street]" value="{$address.street}">
    <input type="hidden" name="address[house]" value="{$address.house}">
    <input type="hidden" name="address[block]" value="{$address.block}">
    <input type="hidden" name="address[apartment]" value="{$address.apartment}">
    <input type="hidden" name="address[entrance]" value="{$address.entrance}">
    <input type="hidden" name="address[entryphone]" value="{$address.entryphone}">
    <input type="hidden" name="address[floor]" value="{$address.floor}">
    <input type="hidden" name="address[subway]" value="{$address.subway}">

    <input type="hidden" name="address[region_id]" value="{$address.region_id}">
    <input type="hidden" name="address[country_id]" value="{$address.country_id}">
    <input type="hidden" name="user_delivery_cost" value="{$elem.user_delivery_cost}">
    <input type="hidden" name="payment" value="{$elem.payment|default:0}">
    <input type="hidden" name="status" id="status" value="{$elem.status}" data-type="{$elem->getStatus()->type}">
    {if $elem.id>0}
        <input type="hidden" name="show_delivery_buttons" id="showDeliveryButtons" value="{$show_delivery_buttons|default:1}"/>
    {/if}

    <div class="order-header">
        {hook name="shop-orderview:header" title=t('Редактирование заказа(админ. панель):Верх')}
            <h2 class="title">
                {if $elem.id>0}
                    <a data-side-panel="{adminUrl do="ajaxQuickShowOrders" exclude_id=$elem.id}" title="{t}Показать другие заказы{/t}"><i class="zmdi zmdi-tag-more c-black"></i></a>
                    <span>{t num=$elem.order_num}Редактировать заказ №%num{/t}</span>
                {else}
                    {t}Создание заказа{/t}
                {/if}
            </h2>

            {if $elem.id>0}
            <div class="status dropdown">
                {$status = $elem->getStatus()}
                <div class="change-status-text" style="background-color:{$status->bgcolor}" data-toggle="dropdown">
                    <span class="value">{$status->title}</span>
                </div>
                <ul class="dropdown-menu dropdown-menu-right">
                    {foreach $status_list as $item}
                        <li {if $item->getChildsCount()}class="node"{/if}>
                            <a data-id="{$item.fields.id}" data-type="{$item.fields.type}">
                                <i class="status-color vertMiddle" style="background:{$item.fields.bgcolor}"></i> {$item.fields.title}
                            </a>
                            {if $item->getChildsCount()}
                                <i class="zmdi"></i>
                                <ul class="dropdown-submenu">
                                    {foreach $item->getChilds() as $subitem}
                                        <li>
                                            <a data-id="{$subitem.fields.id}" data-type="{$subitem.fields.type}">
                                                <i class="status-color vertMiddle" style="background:{$subitem.fields.bgcolor}"></i> {$subitem.fields.title}
                                            </a>
                                        </li>
                                    {/foreach}
                                </ul>
                            {/if}
                        </li>
                    {/foreach}
                </ul>
            </div>
            {/if}
        {/hook}
    </div>

    <div class="admin-comment{if $elem.admin_comments != ''} filled{/if}">
        <textarea placeholder="{t}Комментарий администратора (не отображается у покупателя){/t}" name="admin_comments" class="admin-comment-ta">{$elem.admin_comments}</textarea>
    </div>

    <div class="order-columns" style="margin-bottom:20px">
        <div class="o-leftcol">

            {hook name="shop-orderview:leftcolumn-top" router=$router user=$user order=$elem title=t('Левая колонка верх')}{/hook}

            <div id="additionalBlockWrapper" class="hidden">
                <div class="bordered">
                    <h3>{t}Дополнительные параметры{/t}</h3>
                    {hook name="shop-orderview:additional-params" title=t('Редактирование заказа(админ. панель):Дополнительные параметры')}
                        {$order_depend_fields}
                    {/hook}
                </div>
            </div>

            <div class="bordered userBlock">
                <h3>{t}Покупатель{/t}</h3>
                <div id="userBlockWrapper">
                    {hook name="shop-orderview:user" title=t('Редактирование заказа(админ. панель):Блок информации о пользователе') user=$user router=$router order=$elem user_num_of_order=$user_num_of_order}
                        {$user = $elem->getUser()}
                        {include file="%shop%/form/order/user.tpl" user=$user router=$router order=$elem user_num_of_order=$user_num_of_order}
                    {/hook}
                </div>
                {$order_user_fields}
            </div>

            {if $elem.id>0}
                <div class="bordered">
                    <h3>{t}Информация о заказе{/t}</h3>
                    {hook name="shop-orderview:info" title=t('Редактирование заказа(админ. панель):Блок с информацией')}
                        <table class="otable">
                            <tr>
                                <td class="otitle">
                                    {t}Номер{/t}
                                </td>
                                <td>{$elem.order_num}</td>
                            </tr>
                            <tr>
                                <td class="otitle">
                                    {t}Дата оформления{/t}
                                </td>
                                <td>{$elem.dateof|dateformat:"@date @time:@sec"}</td>
                            </tr>
                            <tr>
                                <td class="otitle">
                                    {t}Последнее обновление{/t}
                                </td>
                                <td>{$elem.dateofupdate|dateformat:"@date @time:@sec"}</td>
                            </tr>
                            <tr>
                                <td class="otitle">
                                    {t}IP пользователя{/t}
                                </td>
                                <td>{$elem.ip}</td>
                            </tr>
                            <tr>
                                <td class="otitle">
                                    {t}Статус:{/t}
                                </td>
                                <td height="20"><strong id="status_text">{$elem->getStatus()->title}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="otitle">
                                    {t}Заказ оформлен в валюте:{/t}
                                </td>
                                <td>{$elem.currency}</td>
                            </tr>
                            <tr>
                                <td class="otitle">
                                    {t}Комментарий к заказу:{/t}
                                </td>
                                <td>{$elem.comments}</td>
                            </tr>                        
                            {foreach from=$elem->getExtraInfo() item=item}
                            <tr>
                                <td class="otitle">
                                    {$item.title}:
                                </td>
                                <td>{$item.value}</td>
                            </tr>                         
                            {/foreach}                        
                            {$fm = $elem->getFieldsManager()}
                           
                            {foreach $fm->getStructure() as $item}
                                <tr>
                                    <td class="otitle">
                                        {$item.title}
                                    </td>
                                    <td > {$fm->getForm($item.alias)} </td>
                                </tr>
                            {/foreach}
                            {$url=$elem->getTrackUrl()}
                            {if !empty($url)}
                                <tr>
                                    <td class="otitle">{t}Ссылка для отслеживания пользователю{/t}</td>
                                    <td><a href="{$url}" target="_blank">{t}Перейти{/t}</a></td>
                                </tr>     
                            {/if}
                            <tr>
                                <td class="otitle">{t}Менеджер заказа{/t}</td>
                                <td>{$elem.__manager_user_id->formView()}</td>
                            </tr>
                            {if $catalog_config.inventory_control_enable && $elem.id}
                                {$links = $elem->getLinkedDocuments()}
                                {$inventory_api = $elem->getInventoryApi()}
                                <tr>
                                    <td class="otitle">{t}Связанный документ{/t}</td>
                                    <td>
                                        {if $links}
                                            {foreach $links as $link}
                                                {$data = $link->getData()}
                                                <a href="{$data.link}" class="crud-edit">{$data.title} №{$link.document_id}</a>
                                                <br>
                                            {/foreach}
                                        {else}
                                            Нет. <a href="{$router->getAdminUrl('CreateFromOrder', ['order_id' => $elem.id], 'catalog-inventoryreservationctrl')}" class="crud-get createReserve">{t}Создать резервирование{/t}</a>
                                        {/if}
                                    </td>
                                </tr>
                            {/if}
                            {$shipments = $elem->getShipments()}
                            {if $shipments}
                                <tr>
                                    <td class="otitle">{t}Отгрузки заказа{/t}</td>
                                    <td>
                                        {foreach $shipments as $shipment}
                                            <a href="{$router->getAdminUrl('edit', ['id' => $shipment.id], 'shop-ordershipmentctrl')}" class="crud-add">
                                                {t}Отгрузка{/t} №{$shipment.id} (от {$shipment.date|dateformat:"@date @time:@sec"})
                                            </a><br>
                                        {/foreach}
                                    </td>
                                </tr>
                            {/if}
                            {$order_info_fields}
                        </table>   
                    {/hook}
                </div>    
            {/if}

            {hook name="shop-orderview:leftcolumn" router=$router user=$user order=$elem title=t('Левая колонка')}{/hook}

            <input type="checkbox" name="notify_user" value="1" id="notify_user" checked data-no-trigger-change>&nbsp;
            <label for="notify_user">{t}Уведомить пользователя об изменениях в заказе.{/t}</label>
            <br>
            <input type="checkbox" name="trigger_cart_change" value="1" id="trigger_cart_change" data-no-trigger-change>&nbsp;
            <label for="trigger_cart_change">{t}Применить обработчики "изменений в корзине"{/t}</label>

        </div> <!-- leftcol -->

        <div class="o-rightcol">
            <div id="documentsBlockWrapper">
                {if $elem.id>0}
                    <div class="bordered">
                        {hook name="shop-orderview:documents" title=t('Редактирование заказа(админ. панель):Блок документы') delivery=$delivery address=$address elem=$elem warehouse_list=$warehouse_list}
                        <h3>{t}Документы{/t}</h3>

                        <ul class="order-documents">
                            {foreach $elem->getPrintForms() as $id => $print_form}
                                <li>
                                    <input type="checkbox" id="op_{$id}" value="{adminUrl do=printForm order_id=$elem.id type=$id}" class="printdoc" data-no-trigger-change>&nbsp;
                                    <label for="op_{$id}">{$print_form->getTitle()}</label>
                                </li>
                            {/foreach}
                        </ul>
                        <div class="input-group">
                            <button type="button" id="printOrder" class="btn btn-default"><i class="zmdi zmdi-print m-r-5"></i> {t}Печать{/t}</button>
                        </div>
                        {/hook}
                    </div>
                {/if}
            </div>

            <div id="addressBlockWrapper" class="bordered">
                {hook name="shop-orderview:address" title=t('Редактирование заказа(админ. панель):Блок адреса') delivery=$delivery address=$address elem=$elem warehouse_list=$warehouse_list}
                    {include file="%shop%/form/order/address.tpl" delivery=$delivery address=$address elem=$elem warehouse_list=$warehouse_list}
                {/hook}
            </div>

            <div id="deliveryBlockWrapper" class="bordered">
                {hook name="shop-orderview:delivery" title=t('Редактирование заказа(админ. панель):Блок доставки') delivery=$delivery address=$address elem=$elem warehouse_list=$warehouse_list}
                    {include file="%shop%/form/order/delivery.tpl" delivery=$delivery address=$address elem=$elem warehouse_list=$warehouse_list}
                {/hook}
            </div>
            
            <div id="paymentBlockWrapper" class="bordered">
                {$pay = $elem->getPayment()}
                {hook name="shop-orderview:payment" title=t('Редактирование заказа(админ. панель):Блок оплаты') pay=$pay elem=$elem}
                    {include file="%shop%/form/order/payment.tpl" pay=$pay elem=$elem}
                {/hook}
            </div>

            {hook name="shop-orderview:rightcolumn" title=t('Правая колонка')}{/hook}
        </div> <!-- right col -->
        
    </div> <!-- -->

    {include file="%shop%/form/order/order_cart.tpl" catalog_config=$catalog_config user=$user router=$router order=$elem order_data=$order_data delivery=$delivery pay=$pay}

    {hook name="shop-orderview:footer" title=t('Редактирование заказа(админ. панель):Подвал')}
        <div class="collapse-block{if $elem.user_text} open{/if}">
            <div class="collapse-title">
                <i class="zmdi zmdi-chevron-right"></i><!--
                --><h3>{t}Текст для покупателя{/t}</h3><!--
                --><span class="help-text">{t}(будет виден покупателю на странице просмотра заказа){/t}</span>
            </div>
            <div class="collapse-content">

                {hook name="shop-orderview:usertext" title=t('Редактирование заказа(админ. панель):Текст для пользователя') catalog_config=$catalog_config user=$user router=$router order=$elem order_data=$order_data delivery=$delivery pay=$pay}
                    {$elem.__user_text->formView()}
                {/hook}

            </div>
        </div>

        {* Здесь отображаются поля с контекстом видимости footer *}
        {$order_footer_fields}
    {/hook}
{if !$refresh_mode}
</form>
{/if}