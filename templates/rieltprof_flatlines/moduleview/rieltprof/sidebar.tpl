<div class="sidebar" id="profile-sidebar">
    {$user = \RS\Application\Auth::getCurrentUser()}
    <div class="profile-block">
        <div class="img">
            <img src="{$THEME_IMG}/logo_dark_mode.svg" alt="">
        </div>
        <a href="/my/"><div class="avatar lazy-image" data-src="{$user.__photo->getUrl('320', '320', 'xy')}"></div></a>
        <div class="text-data">
            <div class="name"><a href="/my/">{$user['surname']} {$user['name']} {if !empty($user['midname'])}{$user['midname']}{/if}</a></div>
            <div class="email">{$user['e_mail']}</div>
            {include file="%rieltprof%/rating_info.tpl" user=$user config=$rieltprof_config}
        </div>
    </div>
    <div class="navigation-block">
        <a href="/my/" class="waves-effect waves-light">
            <span class="link-text">Мои объявления</span>
            <span class="link-chip">{$user->getCountAds($user['id'])}</span>
        </a>
        <div class="favorite-data" data-favorite-url="{$router->getUrl('catalog-front-favorite')}">
            {$countFavorite = \Catalog\Model\FavoriteApi::getInstance()->getFavoriteCount()}
            <a href="/favorite/" class="waves-effect waves-light">
                <span class="link-text">Избранное</span>
                <span class="link-chip rs-favorite-items-count">{$countFavorite}</span>
            </a>
        </div>
        {*                <a href="javascript:void(0);" class="waves-effect waves-light">*}
        {*                    <span class="link-text">Сообщения</span>*}
        {*                    <span class="link-chip">1200</span>*}
        {*                </a>*}
        <a href="/my-review/" class="waves-effect waves-light">
            <span class="link-text">Отзывы</span>
            <span class="link-chip">{$user->getCountReviews()}</span>
        </a>
    </div>
</div>
