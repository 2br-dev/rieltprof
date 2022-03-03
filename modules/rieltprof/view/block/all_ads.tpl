{$config = \RS\Config\Loader::byModule('rieltprof')}
<table class="table-view" data-mode="list">
    <thead>
    <tr>
{*        <th>&nbsp;</th>*}
{*        <th>&nbsp;</th>*}
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th class="features">&nbsp;</th>
        <th class="district">Район</th>
        <th class="type">Тип объекта</th>
        <th class="price">Стоимость</th>
        <th class="rooms">Комнат</th>
        <th class="date">Дата добавления</th>
        <th class="profile">Профиль</th>
        <th class="phone">Телефон</th>
    </tr>
    </thead>
    <tbody id="last-released-data">
        {if $ads}
            {foreach $ads as $ad}
                {$properties = $ad->fillProperty()}
                <tr
                    data-id="{$ad['id']}"
                    class="object-link"
                    data-user="{$ad['owner']}"
                    data-url="{$router->getAdminUrl('getOwnerPhone', [], 'rieltprof-tools')}"
                    data-product="{$ad['id']}"
                >
                    <td class=""><a href="javascript:void(0);" class="expand-row"></td>
                    <td class="features">
                        <div class="features">
                            {if $ad['quickly']}
                                <div class="feature urgent" title="Срочно">С<span class="ext">рочно</span></div>
                            {/if}
                            {if $ad['exclusive']}
                                <div class="feature exclusive" title="Эксклюзив">Э<span class="ext">ксклюзив</span></div>
                            {/if}
                            {if $ad['mortgage']}
                                <div class="feature mortgage" title="Ипотека">И<span class="ext">потека</span></div>
                            {/if}
                            {if $ad['mark']}
                                <div class="feature stowage" title="Закладка">З<span class="ext">акладка</span></div>
                            {/if}
                        </div>
                    </td>
                    <td class="district">{$ad->getProductPropValue($config['prop_district'], 'district')}</td>
                    <td class="type">{$ad['object']}</td>
                    <td class="price">
                        {if $ad['cost_product']}
                            {$config->formatCost($ad['cost_product'], ' ')} ₽
                        {else}
                            {$config->formatCost($ad['cost_rent'], ' ')} ₽/мес.
                        {/if}
                    </td>
                    <td class="rooms">
                        {if $ad['rooms']}
                            {$ad['rooms']}
                        {else}
                            —
                        {/if}
                    </td>
                    <td class="date">{$ad->dateFormat('d.m.Y', 'dateof')}</td>
                    <td class="profile"><a href="/owner-profile/{$ad->getOwner()->id}/">{$ad->getOwner()->surname} {$ad->getOwner()->name}</a></td>
                    <td class="phone" id="phone-{$ad['id']}">
                        <a href="javascript:void(0);" class="phone">Телефон<span class="bubble"></span></a>
                        <a class="ticket-favorite rs-favorite {if $ad->inFavorite()}rs-in-favorite{/if}" data-title="В избранное" data-already-title="В избранном"></a>
                    </td>
                </tr>
                <tr class="object-data">
                    <td colspan="8">
                        <div class="object-card">
                            <div class="left">
                                <div class="photo lazy-image" data-src="{$ad->getMainImage()->getUrl('160', '180', 'axy')}"></div>
                            </div>
                            <div class="right">
                                <div class="labels">
                                    <div class="features">
                                        {if $ad['quickly']}
                                            <div class="feature urgent" title="Срочно">С<span class="ext">рочно</span></div>
                                        {/if}
                                        {if $ad['exclusive']}
                                            <div class="feature exclusive" title="Эксклюзив">Э<span class="ext">ксклюзив</span></div>
                                        {/if}
                                        {if $ad['mortgage']}
                                            <div class="feature mortgage" title="Ипотека">И<span class="ext">потека</span></div>
                                        {/if}
                                        {if $ad['mark']}
                                            <div class="feature stowage" title="Закладка">З<span class="ext">акладка</span></div>
                                        {/if}
                                    </div>
                                </div>
                                <div class="location">
                                    <div class="area">{$ad['city']}, {$ad->getProductPropValue($config['prop_county'], 'county')} округ, {$ad->getProductPropValue($config['prop_district'], 'district')}</div>
                                    <div class="address">
                                        {if !empty($ad['street'])}
                                            ул. {$ad['street']},
                                        {/if}
                                        {if !empty({$ad['house']})}
                                            д. {$ad['house']},
                                        {/if}
                                        {if !empty({$ad['liter']})}
                                            литер {$ad['liter']}
                                        {/if}
                                     </div>
                                </div>
                                <div class="features">
                                    <div class="feature">
                                        <div class="value">{$ad['square']}м²</div>
                                        <div class="key">Общая</div>
                                    </div>
                                    <div class="feature">
                                        <div class="value">{$ad['square_living']}м²</div>
                                        <div class="key">Жилая</div>
                                    </div>
                                    <div class="feature">
                                        <div class="value">{$ad['square_kitchen']}м²</div>
                                        <div class="key">Кухня</div>
                                    </div>
                                    <div class="feature">
                                        <div class="value">{$ad['rooms']}</div>
                                        <div class="key">Комнат</div>
                                    </div>
                                    <div class="feature">
                                        <div class="value">{$ad['flat']}</div>
                                        <div class="key">Этаж</div>
                                    </div>
                                    <div class="feature">
                                        <div class="value">{$ad['flat_house']}</div>
                                        <div class="key">Этажность</div>
                                    </div>
                                </div>
                                <link rel="stylesheet" href="{$ad->getUrl()}"><a href="{$ad->getUrl()}">Подробнее</a>
                            </div>
                        </div>
                    </td>
                </tr>
            {/foreach}
        {/if}
    </tbody>
</table>

