<h3>
    {t}Адрес{/t}
    {if $elem.use_addr > 0}
        <a href="{adminUrl do=addressDialog order_id=$elem.id user_id=$user_id use_addr=$elem.use_addr}" class="crud-add editAddressButton m-l-10" id="editAddress" title="{t}редактировать{/t}">
            <i class="zmdi zmdi-edit"></i>
        </a>
    {/if}
</h3>

{if $elem.use_addr > 0}
    {$city = $elem->getAddress()->getCity()}
    <input type="hidden" name="use_addr" value="{$elem.use_addr}" data-city-id="{$city.id}">
    {* Блок адреса *}

    <table class="otable address-params">
        <tr>
            <td class="otitle">
                {t}Индекс{/t}
            </td>
            <td class="d_zipcode">{$address.zipcode|default:"{t}- не указано -{/t}"}</td>
        </tr>
        <tr>
            <td class="otitle">{t}Страна{/t}</td>
            <td class="d_country">{$address.country|default:"{t}- не указано -{/t}"}</td>
        </tr>
        <tr>
            <td class="otitle">{t}Край/область{/t}</td>
            <td class="d_region">{$address.region|default:"{t}- не указано -{/t}"}</td>
        </tr>
        <tr>
            <td class="otitle">{t}Город{/t}</td>
            <td class="d_city">{$address.city|default:"{t}- не указано -{/t}"}</td>
        </tr>
        <tr>
            <td class="otitle">{t}Адрес{/t}</td>
            <td class="d_address">{$address->getLineView(false)|default:"{t}- не указано -{/t}"}</td>
        </tr>
        {if $address.subway}
            <tr>
                <td class="otitle">{t}Станция метро{/t}</td>
                <td class="d_address">{$address.subway}</td>
            </tr>
        {/if}
        <tr>
            <td class="otitle">{t}Контактное лицо{/t}</td>
            <td class="d_contact_person"><input type="text" name="contact_person" value="{$elem.contact_person}" class="maxWidth"></td>
        </tr>

        {$order_address_fields}
    </table>
{else}
    <p class="emptyOrderBlock">{t}Адрес не указан.{/t} <a href="{adminUrl do=addressDialog order_id=$elem.id user_id=$user_id}" class="crud-add editAddressButton u-link">{t}Указать адрес{/t}</a>.</p>
{/if}