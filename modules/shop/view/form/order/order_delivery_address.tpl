{hook name="shop-form-order-order_address_address:all" title="{t}Редактирование заказа - блок адреса в диалоге доставки:весь блок{/t}"}
    <tr>
        <td class="caption">{t}Страна:{/t}</td>
        <td>
            <select id="addressCountryIdSelect" name="address[country_id]" data-url="{adminUrl do=getCountryRegions}">
                {html_options options=$country_list selected=$address.country_id}
            </select>
            <div class="fieldhelp">{t}Например: Россия{/t}</div>
        </td>
    </tr>
    <tr>
        <td class="caption">{t}Область, Город:{/t}</td>
        <td class="clearfix">
            <div class="inline f">
                    <span {if count($region_list) == 0}style="display:none"{/if} id="region-select">
                        <select id="addressRegionIdSelect" name="address[region_id]">
                            {foreach $region_list as $region}
                            <option value="{$region.id}" {if $region.id==$address.region_id}selected{/if}>{$region.title}</option>
                            {/foreach}
                        </select>
                    </span>
                    
                    <span {if count($region_list) > 0}style="display:none"{/if} id="region-input">
                        <input type="text" value="{$address.region}" name="address[region]">
                    </span>
                    <div class="fieldhelp">{t}Область/Край{/t}</div>
                </div>
                <div class="inline">
                    <input id="addressCityInput" type="text" value="{$address.city}" name="address[city]">
                    <div class="fieldhelp">{t}Город{/t}</div>
                </div>
        </td>
    </tr>
    <tr>
        <td class="caption">{t}Адрес:{/t}</td>
        <td class="clearfix">
            <div class="inline">
                <input size="48" type="text" maxlength="255" value="{$address.address}" name="address[address]">
                <div class="fieldhelp">{t}Адрес. Например: ул. Красная, 100, офис 71{/t}</div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="caption">{t}Индекс, Улица:{/t}</td>
        <td class="clearfix">
            <div class="inline f">
                <input id="addressZipcodeInput" size="10" type="text" maxlength="20" value="{$address.zipcode}" name="address[zipcode]">
                <div class="fieldhelp">{t}Индекс{/t}</div>
            </div>
            <div class="inline f">
                <input id="addressZipcodeInput" size="29" type="text" maxlength="100" value="{$address.street}" name="address[street]">
                <div class="fieldhelp">{t}Улица{/t}</div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="caption">{t}Дом, Корпус, Квартира:{/t}</td>
        <td class="clearfix">
            <div class="inline f">
                <input size="10" type="text" maxlength="20" value="{$address.house}" name="address[house]">
                <div class="fieldhelp">{t}Дом{/t}</div>
            </div>
            <div class="inline">
                <input size="10" type="text" maxlength="255" value="{$address.block}" name="address[block]">
                <div class="fieldhelp">{t}Корпус{/t}</div>
            </div>
            <div class="inline">
                <input size="10" type="text" maxlength="255" value="{$address.apartment}" name="address[apartment]">
                <div class="fieldhelp">{t}Квартира{/t}</div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="caption">{t}Подъезд, Домофон, Этаж:{/t}</td>
        <td class="clearfix">
            <div class="inline f">
                <input size="10" type="text" maxlength="20" value="{$address.entrance}" name="address[entrance]">
                <div class="fieldhelp">{t}Подъезд{/t}</div>
            </div>
            <div class="inline">
                <input size="10" type="text" maxlength="20" value="{$address.entryphone}" name="address[entryphone]">
                <div class="fieldhelp">{t}Домофон{/t}</div>
            </div>
            <div class="inline">
                <input size="10"  type="text" maxlength="255" value="{$address.floor}" name="address[floor]">
                <div class="fieldhelp">{t}Этаж{/t}</div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="caption">{t}Станция метро:{/t}</td>
        <td class="clearfix">
            <input size="44" type="text" maxlength="255" value="{$address.subway}" name="address[subway]">
        </td>
    </tr>
{/hook}
{literal}
<script>
$(function() {
    $('select[name="address[country_id]"]').change(function() {
        var parent = $(this).val();
        var regions = $('select[name="address[region_id]"]').attr('disabled','disabled');
        
        $.ajaxQuery({
            url: $(this).data('url'),
            data: {
                parent: parent
            },
            success: function(response) {
                if (response.list.length>0) {
                    regions.html('');
                    for(i=0; i< response.list.length; i++) {
                        var item = $('<option value="'+response.list[i].key+'">'+response.list[i].value+'</option>');
                        regions.append(item);
                    }
                    regions.removeAttr('disabled');
                    $('#region-input').hide();
                    $('#region-select').show();
                } else {
                    $('#region-input [name="address[region]"]').val('');
                    $('#region-input').show();
                    $('#region-select').hide();
                }                
            }
        });
        
    });    
});
</script>
{/literal}