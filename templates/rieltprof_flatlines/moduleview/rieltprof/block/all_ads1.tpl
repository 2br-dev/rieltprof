{$config = \RS\Config\Loader::byModule('rieltprof')}
{$current_user = \RS\Application\Auth::getCurrentUser()}
{$categories = $config->getUniqCategoriesAllAds()}
<div class="tabs-wrapper" id="main-page-table">
    <div class="tabs-header">
        <a href="" class="tab-link active" data-target="sell">Продажа</a>
        <a href="" class="tab-link" data-target="rent">Аренда</a>
    </div>
    <div class="tabs">
        <div class="tab active" id="sell">
            <div class="category-anchor">
                {foreach $categories as $category}
                    {if $category['parent'] == 'Продажа'}
                        <a href="#{$category['id']}" class="anchor-item">{$category['name']}</a>
                    {/if}
                {/foreach}
            </div>
            {foreach $categories as $category}
                {if $category['parent'] == 'Продажа'}
                    {if $category['name'] == 'Квартира'}
                        {include file="%rieltprof%/mainTable/flat.tpl" category=$category config=$config}
                    {/if}
                    {if $category['name'] == 'Новостройка'}
                        {include file="%rieltprof%/mainTable/newBuilding.tpl" category=$category config=$config}
                    {/if}
                    {if $category['name'] == 'Комната'}
                        {include file="%rieltprof%/mainTable/room.tpl" category=$category config=$config}
                    {/if}
                    {if $category['name'] == 'Таунхаус'}
                        {include file="%rieltprof%/mainTable/townhouse.tpl" category=$category config=$config}
                    {/if}
                    {if $category['name'] == 'Дуплекс'}
                        {include file="%rieltprof%/mainTable/duplex.tpl" category=$category config=$config}
                    {/if}
                    {if $category['name'] == 'Участок'}
                        {include file="%rieltprof%/mainTable/plot.tpl" category=$category config=$config}
                    {/if}
                    {if $category['name'] == 'Дача'}
                        {include file="%rieltprof%/mainTable/countryHouse.tpl" category=$category config=$config}
                    {/if}
                    {if $category['name'] == 'Гараж'}
                        {include file="%rieltprof%/mainTable/garage.tpl" category=$category config=$config}
                    {/if}
                    {if $category['name'] == 'Коммерция'}
                        {include file="%rieltprof%/mainTable/commercial.tpl" category=$category config=$config}
                    {/if}
                {/if}
            {/foreach}
        </div>
    </div>
</div>
{*<table class="table-view" data-mode="list">*}
{*    <thead>*}
{*    <tr>*}
{*        <th>&nbsp;</th>*}
{*        <th>&nbsp;</th>*}
{*        <th>&nbsp;</th>*}
{*        <th>&nbsp;</th>*}
{*        <th class="features"> </th>*}
{*        <th class="district">Район</th>*}
{*        <th class="type">Тип объекта</th>*}
{*        <th class="price">Стоимость</th>*}
{*        <th class="rooms">Комнат</th>*}
{*        <th class="square">Площадь</th>*}
{*        <th class="date">Дата добавления</th>*}
{*        <th class="profile">Профиль</th>*}
{*        <th class="phone">Телефон</th>*}
{*    </tr>*}
{*    </thead>*}
{*    <tbody id="last-released-data">*}
{*        {if $ads}*}
{*            {foreach $ads as $ad}*}
{*                {$properties = $ad->fillProperty()}*}
{*                <tr*}
{*                    data-id="{$ad['id']}"*}
{*                    class="object-link"*}
{*                    data-user="{$ad['owner']}"*}
{*                    data-url="{$router->getAdminUrl('getOwnerPhone', [], 'rieltprof-tools')}"*}
{*                    data-product="{$ad['id']}"*}
{*                >*}
{*                    <td class=""><p class="expand-row"></p></td>*}
{*                    <td class="photo-holder">*}
{*                        <a href="{$ad->getUrl()}" class="photo lazy-image" data-src="{$ad->getMainImage()->getUrl('550', '330', 'xy')}"></a>*}
{*                    </td>*}
{*                    <td class="features">*}
{*                        <div class="features">*}
{*                            {include file="%catalog%/fitures-table.tpl" product=$ad}*}
{*                        </div>*}
{*                    </td>*}
{*                    <td class="district">{$ad->getProductPropValue($config['prop_district'], 'district')}</td>*}
{*                    <td class="type">{$ad['object']}</td>*}
{*                    <td class="price">*}
{*                        {if $ad['cost_product']}*}
{*                            {$config->formatCost($ad['cost_product'], ' ')} ₽*}
{*                        {else}*}
{*                            {$config->formatCost($ad['cost_rent'], ' ')} ₽/мес.*}
{*                        {/if}*}
{*                    </td>*}
{*                    <td class="rooms">*}
{*                        {if $ad['rooms']}*}
{*                            {$ad['rooms']}*}
{*                        {else}*}
{*                            {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') !== NULL}*}
{*                                {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') == 'Студия'}*}
{*                                    Студия*}
{*                                {else}*}
{*                                    {$ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list')}*}
{*                                {/if}*}
{*                            {else}*}
{*                                -*}
{*                            {/if}*}
{*                        {/if}*}
{*                    </td>*}
{*                    <td class="square">*}
{*                        {if $ad['object'] != "Участок"}*}
{*                            {if $ad['object'] == 'Дом' || $ad['object'] == 'Дача'}*}
{*                                {$ad['square']}/{$ad['land_area']}*}
{*                            {else}*}
{*                                {$ad['square']}/{$ad['square_living']}/{$ad['square_kitchen']}*}
{*                            {/if}*}
{*                        {else}*}
{*                            {$ad['land_area']} сот.*}
{*                        {/if}*}
{*                    </td>*}
{*                    <td class="date">{$ad->dateFormat('d.m.Y', 'dateof')}</td>*}
{*                    <td class="profile">*}
{*                        <a*}
{*                            {if $current_user['id'] != $ad['owner']}href="/owner-profile/{$ad->getOwner()->id}/" {else}href="/my/"{/if}*}
{*                        >*}
{*                            {$ad->getOwner()->surname} {$ad->getOwner()->name}*}
{*                        </a>*}
{*                    </td>*}
{*                    <td class="phone" id="phone-{$ad['id']}">*}
{*                        <a href="javascript:void(0);" class="phone">Телефон<span class="bubble"></span></a>*}
{*                        <a class="ticket-favorite rs-favorite {if $ad->inFavorite()}rs-in-favorite{/if}" data-title="В избранное" data-already-title="В избранном"></a>*}
{*                    </td>*}
{*                </tr>*}
{*                <tr class="object-data">*}
{*                    <td colspan="8">*}
{*                        <div class="object-card">*}
{*                            <div class="left">*}
{*                                <div class="photo lazy-image" data-src="{$ad->getMainImage()->getUrl('160', '180', 'axy')}"></div>*}
{*                            </div>*}
{*                            <div class="right">*}
{*                                <div class="labels">*}
{*                                    <div class="features">*}
{*                                        {include file='%catalog%/features-card.tpl' product=$ad}*}
{*                                    </div>*}
{*                                </div>*}
{*                                <div class="location">*}
{*                                    <div class="area">*}
{*                                        {$ad['city']},*}
{*                                        {if $ad['county'] != NULL}*}
{*                                            {$ad->getProductPropValue($config['prop_county'], 'county')} округ,*}
{*                                        {/if}*}
{*                                        {$ad->getProductPropValue($config['prop_district'], 'district')}</div>*}
{*                                    <div class="address">*}
{*                                        {if !empty($ad['street'])}*}
{*                                            ул. {$ad['street']},*}
{*                                        {/if}*}
{*                                        {if !empty({$ad['house']})}*}
{*                                            д. {$ad['house']},*}
{*                                        {/if}*}
{*                                        {if !empty({$ad['liter']})}*}
{*                                            литер {$ad['liter']}*}
{*                                        {/if}*}
{*                                     </div>*}
{*                                </div>*}
{*                                <div class="features">*}
{*                                    {if $ad['square']}*}
{*                                        <div class="feature">*}
{*                                            <div class="value">{$ad['square']}м²</div>*}
{*                                            <div class="key">Общая</div>*}
{*                                        </div>*}
{*                                    {/if}*}
{*                                    {if $ad['square_living']}*}
{*                                        <div class="feature">*}
{*                                            <div class="value">{$ad['square_living']}м²</div>*}
{*                                            <div class="key">Жилая</div>*}
{*                                        </div>*}
{*                                    {/if}*}
{*                                    {if $ad['square_kitchen']}*}
{*                                        <div class="feature">*}
{*                                            <div class="value">{$ad['square_kitchen']}м²</div>*}
{*                                            <div class="key">Кухня</div>*}
{*                                        </div>*}
{*                                    {/if}*}
{*                                    {if $ad['rooms']}*}
{*                                        <div class="feature">*}
{*                                            <div class="value">{$ad['rooms']}</div>*}
{*                                            <div class="key">Комнат</div>*}
{*                                        </div>*}
{*                                    {/if}*}
{*                                    {if $ad['flat']}*}
{*                                        <div class="feature">*}
{*                                            <div class="value">{$ad['flat']}</div>*}
{*                                            <div class="key">Этаж</div>*}
{*                                        </div>*}
{*                                    {/if}*}
{*                                    {if $ad['flat_house']}*}
{*                                        <div class="feature">*}
{*                                            <div class="value">{$ad['flat_house']}</div>*}
{*                                            <div class="key">Этажность</div>*}
{*                                        </div>*}
{*                                    {/if}*}
{*                                    {if $ad['land_area']}*}
{*                                        <div class="feature">*}
{*                                            <div class="value">{$ad['land_area']}сот.</div>*}
{*                                            <div class="key">Участок</div>*}
{*                                        </div>*}
{*                                    {/if}*}
{*                                </div>*}
{*                                <a href="{$ad->getUrl()}">Подробнее</a>*}
{*                            </div>*}
{*                        </div>*}
{*                    </td>*}
{*                </tr>*}
{*                {include file='%catalog%/product-tr.tpl' product=$ad}*}
{*            {/foreach}*}
{*        {/if}*}
{*    </tbody>*}
{*</table>*}

