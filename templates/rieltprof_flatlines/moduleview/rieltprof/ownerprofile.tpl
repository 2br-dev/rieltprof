{$config = \RS\Config\Loader::byModule('rieltprof')}
{$categories = $user->getUniqCategoryAds()}
{$current_user = \RS\Application\Auth::getCurrentUser()}
<div class="global-wrapper" id="account">
    <div class="sidebar" id="profile-sidebar">
        <div class="profile-block">
            <div class="img">
                <a href="/"><img src="{$THEME_IMG}/logo_dark_mode.svg" alt=""></a>
            </div>
            <div class="avatar lazy-image" data-src="{$user.__photo->getUrl('320', '320', 'xy')}"></div>
            <div class="duration">
                <p>на rieltprof.ru c {$config->dateFormat($user['dateofreg'], 'd.m.Y')}</p>
            </div>
            {include file="%rieltprof%/rating_info.tpl" user=$user config=$config}
        </div>
        <form method="POST">
            {csrf}
            {$this_controller->myBlockIdInput()}
            <input type="hidden" name="referer" value="{$referer}">
            <input type="hidden" name="is_company" value="0"/>
            <div class="main-data">
                <div class="input-field no-available">
                    {$user->getPropertyView('surname', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">Фамилия</label>
                    <div class="formFieldError">{$user->getErrorsByForm('surname', ',')}</div>
                </div>
                <div class="input-field no-available">
                    {$user->getPropertyView('name', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">Имя</label>
                    <div class="formFieldError">{$user->getErrorsByForm('name', ',')}</div>
                </div>
                <div class="input-field no-available">
                    {$user->getPropertyView('midname', ['class' => 'nempty'])}
                    <label for="">Отчество</label>
                </div>
                <div class="input-field no-available">
{*                    {$user->getPropertyView('phone', ['class' => 'nempty phone_mask'], [form => true, errors => false])}*}
                    <input type="text" name="phone" value="{$user['phone']}">
                    <label for="">Телефон</label>
                    <div class="formFieldError">{$user->getErrorsByForm('phone', ',')}</div>
                </div>
                <div class="input-field w100 no-available">
                    {$user->getPropertyView('e_mail', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">E-mail</label>
                    <div class="formFieldError">{$user->getErrorsByForm('e_mail', ',')}</div>
                </div>
            </div>
        </form>
    </div>
    <div class="content">
        <div class="top-block">
            <div class="row">
                <div class="">
                    {if $current_user->canSendReview($current_user['id'], $user['id'])}
                        <a href="javascript:void(0);" class="feedback" data-target-modal="feedback-modal">Оставить отзыв</a>
                    {/if}
                </div>
                <div class="separator"></div>
                <div class="switch-district">
                    <div class="segmented-radio">
{*                        {if $smarty.server.REQUEST_URI == "/owner-profile/"{$user['id']}}*}
{*                            <p class="sale-category-link active">Мои объект</p>*}
{*                        {else}*}
                            <a href="/owner-profile/{$user['id']}/" class="sale-category-link">Объявления</a>
{*                        {/if}*}
{*                        {if $smarty.server.REQUEST_URI == "/my-review/"}*}
{*                            <p class="rent-category-link active">Мои отзывы</p>*}
{*                        {else}*}
                            <a href="/owner-review/{$user['id']}" class="rent-category-link">Отзывы</a>
{*                        {/if}*}
                    </div>
                </div>
            </div>
        </div>
        <div class="main-block">
            <div class="crumbs-wrapper">
                <div class="crumbs-rest-wrapper">
                    <div class="crumbs-rest">
                        <a href="/" class="crumb ">Главная</a>
                        <div class="separator">›</div>
                        <a class="crumb ">Профиль риеэлтора</a>
                    </div>
                </div>
            </div>
            <div class="title">
                <span>Все объявления ({$user->getCountAds($user['id'])})</span>
            </div>
            <div class="tabs-wrapper">
                <div class="tabs-header">
                    <a href="" class="tab-link active" data-target="sell">Продажа</a>
                    <a href="" class="tab-link" data-target="rent">Аренда</a>
                </div>
                <div class="tabs">
                    <div class="tab active" id="sell">
                        {foreach $categories as $category}
                            {if $category['parent'] == 'Продажа'}
                                <div class="group-header">{$category['name']}</div>
                                {$ads = $user->getAdsFromCategoryById($category['id'])}
                                {if $ads}
                                    <table class="table-view" data-mode="list">
                                        <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th class="features">&nbsp;</th>
                                            <th class="district">Район</th>
                                            <th class="price">Стоимость</th>
                                            <th class="rooms">Комнат</th>
                                            <th class="square">Площадь</th>
                                            <th class="date">Дата</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody class="my-objects-data">
                                        {foreach $ads as $ad}
                                            <tr data-id="{$ad['id']}" class="object-link">
                                                {*                                                    <td class="expand-wrapper"><a href="javascript:void(0);" class="expand-row"></a></td>*}
                                                <td class="photo-holder"><a href="" class="photo lazy-image" data-src="{$ad->getMainImage()->getUrl('360', '215', 'xy')}"></a></td>
                                                <td class="features">
                                                    <div class="features">
                                                        {include file="%catalog%/fitures-table.tpl" product=$ad}
                                                    </div>
                                                </td>
                                                <td class="district">{$ad->getProductPropValue($config['prop_district'], 'district')}</td>
                                                <td class="price">{if $ad['cost_product']}
                                                        {$config->formatCost($ad['cost_product'], ' ')} ₽
                                                    {else}
                                                        {$config->formatCost($ad['cost_rent'], ' ')} ₽/мес.
                                                    {/if}</td>
                                                <td class="rooms">
                                                    {if $ad['rooms']}
                                                        {$ad['rooms']}
                                                    {else}
                                                        {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') !== NULL}
                                                            {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') == 'Студия'}
                                                                Студия
                                                            {else}
                                                                {$ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list')}
                                                            {/if}
                                                        {else}
                                                            -
                                                        {/if}
                                                    {/if}
                                                </td>
                                                <td class="square">
                                                    {if $ad['object'] != "Участок"}
                                                        {if $ad['object'] == 'Дом' || $ad['object'] == 'Дача'}
                                                            {$ad['square']}м²/{$ad['land_area']}сот.
                                                        {else}
                                                            {if $ad['object'] == 'Гараж' || $ad['object'] == 'Комната' || $ad['object'] == 'Коммерция'}
                                                                {$ad['square']}м²
                                                            {else}
                                                                {$ad['square']}м²
                                                                {if $ad['square_living'] && $ad['square_living'] != '0'}
                                                                    /{$ad['square_living']}м²
                                                                {/if}
                                                                {if $ad['square_kitchen'] && $ad['square_kitchen'] != '0'}
                                                                    /{$ad['square_kitchen']}м²
                                                                {/if}
                                                            {/if}
                                                        {/if}
                                                    {else}
                                                        {$ad['land_area']}сот.
                                                    {/if}
                                                </td>
                                                <td class="date">
                                                    {$ad->dateFormat('d.m.Y', 'dateof')}
                                                </td>
                                                <td><a href="{$ad->getUrl()}">Показать</a></td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                {/if}
                            {/if}
                        {/foreach}
                    </div>
                    <div class="tab" id="rent">
                        {foreach $categories as $category}
                            {if $category['parent'] == 'Аренда'}
                                <div class="group-header">{$category['name']}</div>
                                {$ads = $user->getAdsFromCategoryById($category['id'])}
                                {if $ads}
                                    <table class="table-view" data-mode="list">
                                        <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                            <th class="features">&nbsp;</th>
                                            <th class="district">Район</th>
                                            <th class="price">Стоимость</th>
                                            <th class="rooms">Комнат</th>
                                            <th class="square">Площадь</th>
                                            <th class="date">Дата</th>
                                        </tr>
                                        </thead>
                                        <tbody class="my-objects-data">
                                        {foreach $ads as $ad}
                                            <tr data-id="{$ad['id']}" class="object-link">
                                                <td class="expand-wrapper"><a href="javascript:void(0);" class="expand-row"></a></td>
                                                <td class="photo-holder"><a href="" class="photo lazy-image" data-src="{$ad->getMainImage()->getUrl('160', '180', 'axy')}"></a></td>
                                                <td class="features">
                                                    <div class="features">
                                                        {include file="%catalog%/fitures-table.tpl" product=$ad}
                                                    </div>
                                                </td>
                                                <td class="district">{$ad->getProductPropValue($config['prop_district'], 'district')}</td>
                                                <td class="price">{if $ad['cost_product']}
                                                        {$config->formatCost($ad['cost_product'], ' ')} ₽
                                                    {else}
                                                        {$config->formatCost($ad['cost_rent'], ' ')} ₽/мес.
                                                    {/if}</td>
                                                <td class="rooms">
                                                    {if $ad['rooms']}
                                                        {$ad['rooms']}
                                                    {else}
                                                        {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') !== NULL}
                                                            {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') == 'Студия'}
                                                                Студия
                                                            {else}
                                                                {$ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list')}
                                                            {/if}
                                                        {else}
                                                            -
                                                        {/if}
                                                    {/if}
                                                </td>
                                                <td class="square">
                                                    {if $ad['object'] != "Участок"}
                                                        {if $ad['object'] == 'Дом' || $ad['object'] == 'Дача'}
                                                            {$ad['square']}м²/{$ad['land_area']}сот.
                                                        {else}
                                                            {if $ad['object'] == 'Гараж' || $ad['object'] == 'Комната' || $ad['object'] == 'Коммерция'}
                                                                {$ad['square']}м²
                                                            {else}
                                                                {$ad['square']}м²
                                                                {if $ad['square_living'] && $ad['square_living'] != '0'}
                                                                    /{$ad['square_living']}м²
                                                                {/if}
                                                                {if $ad['square_kitchen'] && $ad['square_kitchen'] != '0'}
                                                                    /{$ad['square_kitchen']}м²
                                                                {/if}
                                                            {/if}
                                                        {/if}
                                                    {else}
                                                        {$ad['land_area']}сот.
                                                    {/if}
                                                </td>
                                                <td class="date">
                                                    {$ad->dateFormat('d.m.Y', 'dateof')}
                                                </td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                {/if}
                            {/if}
                        {/foreach}

                    </div>
                </div>
            </div>
        </div>
    </div>
    {include file='%rieltprof%/statusbar.tpl'}
</div>
{include file='%rieltprof%/review-form.tpl' from=$current_user to=$user}
