{$current_user = \RS\Application\Auth::getCurrentUser()}
<tr
        data-id="{$product['id']}"
        class="object-link"
        data-user="{$product['owner']}"
        data-url="{$router->getAdminUrl('getOwnerPhone', [], 'rieltprof-tools')}"
        data-product="{$product['id']}"
>
    <td class="">
        <p class="expand-row"></p>
    </td>
    <td class="photo-holder">
        <a href="{$product->getUrl()}">
            <div class="photo lazy-image" data-src="{$product->getMainImage()->getUrl('360', '215', 'xy')}"></div>
        </a>
        <a
                class="ticket-favorite rs-favorite favorite-card {if $product->inFavorite()}rs-in-favorite{/if}"
                data-title="В избранное" data-already-title="В избранном" title="В избранное"
        ></a>
    </td>
    <td class="features">
        <div class="features">
            {include file="%catalog%/fitures-table.tpl" product=$product}
        </div>
    </td>
    {if $query != ""}
        <td class="type">{$product['object']}</td>
    {/if}
    <td class="district">{$product->getProductPropValue($config['prop_district'], 'district')}</td>
    <td class="price">
        {if $product['cost_product']}
            {$config->formatCost($product['cost_product'], ' ')} ₽
        {else}
            {$config->formatCost($product['cost_rent'], ' ')} ₽/мес.
        {/if}
    </td>
    {if $product['rooms']}
        <td class="rooms">{$product['rooms']}</td>
    {else}
        {if $product->getProductPropValue($config['prop_rooms_list'], 'rooms_list') !== NULL}
            {if $product->getProductPropValue($config['prop_rooms_list'], 'rooms_list') == 'Студия'}
                <td class="rooms">Студия</td>
            {else}
                <td class="rooms">{$product->getProductPropValue($config['prop_rooms_list'], 'rooms_list')}</td>
            {/if}
        {else}
            <td class="rooms">-</td>
        {/if}
    {/if}
    <td class="square">
        {if $product['object'] != "Участок"}
            {if $product['object'] == 'Дом' || $product['object'] == 'Дача'}
                {$product['square']}м²/{$product['land_area']}сот.
            {else}
                {if $product['object'] == 'Гараж' || $product['object'] == 'Комната' || $product['object'] == 'Коммерция'}
                    {$product['square']}м²
                {else}
                    {$product['square']}м²
                    {if $product['square_living'] && $product['square_living'] != '0'}
                        /{$product['square_living']}м²
                    {/if}
                    {if $product['square_kitchen']}
                        /{$product['square_kitchen']}м²
                    {/if}
                {/if}
            {/if}
        {else}
            {$product['land_area']}сот.
        {/if}
    </td>
    <td class="date">
        {$product->dateFormat('d.m.Y', 'actual_on_date')}
    </td>
{*    <td class="profile">*}
{*        <a*}
{*            {if $current_user['id'] != $product->getOwner()->id}href="/owner-profile/{$product->getOwner()->id}/" {else}href="/my/"{/if}*}
{*        >*}
{*            {$product->getOwner()->surname} {$product->getOwner()->name}*}
{*        </a>*}
{*    </td>*}
    <td id="phone-{$product['id']}" class="phone">
        <a href="javascript:void(0);" class="phone" id="phone-link-{$product['id']}">
            <span class="phone-sign">Телефон</span>
            <span class="bubble"></span>
        </a>
        <a class="ticket-favorite rs-favorite favorite-list {if $product->inFavorite()}rs-in-favorite{/if}" data-title="В избранное" data-already-title="В избранном"></a>
    </td>
</tr>
<tr class="object-data">
    <td colspan="8">
        <div class="object-card">
            <div class="left">
                <div class="photo lazy-image" data-src="{$product->getMainImage('200', '220', 'xy')}" style="background-image: url(&quot;/img/room.png&quot;);"></div>
            </div>
            <div class="right">
                <div class="labels">
                    <div class="features">
                        {include file="%catalog%/features-card.tpl" product=$product}
                    </div>
                </div>
                <div class="location">
                    <div class="area">
                        {$product['city']},
                        {if $product['county'] != NULL}
                            {$product->getProductPropValue($config['prop_county'], 'county')} округ,
                        {/if}
                        {$product->getProductPropValue($config['prop_district'], 'district')}
                    </div>
                    <div class="address">
                        {if !empty({$product['street']})}
                            ул. {$product['street']},
                        {/if}
                        {if !empty({$product['house']})}
                            д. {$product['house']},
                        {/if}
                        {if !empty($product['liter'])}
                            литер {$product['liter']}
                        {/if}
                    </div>
                </div>
                <div class="features">
                    {if $ad['square']}
                        <div class="feature">
                            <div class="value">{$ad['square']}м²</div>
                            <div class="key">Общая</div>
                        </div>
                    {/if}
                    {if $ad['square_living']}
                        <div class="feature">
                            <div class="value">{$ad['square_living']}м²</div>
                            <div class="key">Жилая</div>
                        </div>
                    {/if}
                    {if $ad['square_kitchen']}
                        <div class="feature">
                            <div class="value">{$ad['square_kitchen']}м²</div>
                            <div class="key">Кухня</div>
                        </div>
                    {/if}
                    {if $ad['rooms']}
                        <div class="feature">
                            <div class="value">{$ad['rooms']}</div>
                            <div class="key">Комнат</div>
                        </div>
                    {/if}
                    {if $ad['flat']}
                        <div class="feature">
                            <div class="value">{$ad['flat']}</div>
                            <div class="key">Этаж</div>
                        </div>
                    {/if}
                    {if $ad['flat_house']}
                        <div class="feature">
                            <div class="value">{$ad['flat_house']}</div>
                            <div class="key">Этажность</div>
                        </div>
                    {/if}
                    {if $ad['land_area']}
                        <div class="feature">
                            <div class="value">{$ad['land_area']}сот.</div>
                            <div class="key">Участок</div>
                        </div>
                    {/if}
                </div>
                <div class="obj-footer">
                    <a href="{$product->getUrl()}">Подробнее</a>
                    <div class="author">
                        <span>Автор: </span><a
                                {if $current_user['id'] != $product['owner']}href="/owner-profile/{$product->getOwner()->id}/" {else}href="/my/"{/if}
                        >
                            {$product->getOwner()->surname} {$product->getOwner()->name}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
