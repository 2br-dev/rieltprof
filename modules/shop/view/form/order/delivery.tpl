{$type_object = $delivery->getTypeObject()}

<h3>
    {t}Доставка{/t}
    {if $elem.delivery>0}
        <a href="{adminUrl do=deliveryDialog order_id=$elem.id delivery=$elem.delivery user_id=$user_id}" class="crud-add m-l-10" id="editDelivery" title="{t}редактировать{/t}">
            <i class="zmdi zmdi-edit"></i>
        </a>
    {/if}
</h3>

{if $elem.delivery>0}
    <input type="hidden" name="delivery" value="{$elem.delivery}"/>
    {* Блок о доставке *}

    <table class="otable delivery-params">
        <tr>
            <td class="otitle">
                {t}Тип{/t}
            </td>
            <td class="d_title">{$delivery.title}</td>
        </tr>
        {if !empty($warehouse_list)}
            <tr>
                <td class="otitle">{t}Склад{/t}</td>
                <td class="d_warehouse">
                    <select name="warehouse">
                        <option value="0">{t}не выбран{/t}</option>
                        {foreach $warehouse_list as $warehouse}
                            <option value="{$warehouse.id}" {if $elem.warehouse == $warehouse.id}selected="selected"{/if}>{$warehouse.title}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
        {/if}
        {if $courier_list}
            <tr>
                <td class="otitle">{t}Курьер{/t}</td>
                <td>
                    <select name="courier_id">
                        <option value="0">{t}не выбран{/t}</option>
                        {foreach $courier_list as $courier_id => $courier}
                            <option value="{$courier_id}" {if $elem.courier_id == $courier_id}selected="selected"{/if}>{$courier}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
        {/if}

        {$order_delivery_fields}
    </table>

    {$type_object->getAdminDeliveryParamsHtml($elem)}

    {* @deprecated (21.01) для совместимости с устаревшими классами доставки *}
    {if $elem.delivery_new_query && method_exists($type_object, 'getAdminAddittionalHtml')}
        {$type_object->getAdminAddittionalHtml($elem)}
    {/if}
    {if $elem.id > 0 && $show_delivery_buttons && method_exists($type_object, 'getAdminHTML')}
        {$type_object->getAdminHTML($elem)}
    {/if}

{else}
    <p class="emptyOrderBlock">{t}Тип доставки не указан.{/t} <a href="{adminUrl do=deliveryDialog order_id=$elem.id user_id=$user_id}" class="crud-add u-link">{t}Указать доставку{/t}</a>.</p>
{/if}