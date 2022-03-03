<div class="bodyWrap">
    <div class="global-wrapper{if $query == ""} sidebar-shown{/if}" id="search">
        <div class="categories-sidebar collapsed">
            {include file="%catalog%/sidebar-catalog.tpl"}
        </div>
        {block name="content"}{/block}
        {include file='%rieltprof%/statusbar.tpl'}
    </div>
{*    <header>*}
{*        <div class="viewport">*}
{*            <div class="topZone">*}
{*                {if ModuleManager::staticModuleExists('affiliate')}*}
{*                    <div class="topCity">*}
{*                        {moduleinsert name="\Affiliate\Controller\Block\SelectAffiliate"}*}
{*                        {moduleinsert name="\Affiliate\Controller\Block\ShortInfo"}*}
{*                    </div>*}
{*                {/if}*}
{*            *}
{*                *}{* Меню *}
{*                {moduleinsert name="\Menu\Controller\Block\Menu"}*}
{*            </div>*}
{*            *}
{*            *}{* Логотип *}
{*            {moduleinsert name="\Main\Controller\Block\Logo" width="200" height="75"}*}
{*            *}
{*            <div class="userBlock">*}
{*                {if ModuleManager::staticModuleExists('shop')}*}
{*                    *}{* Блок авторизации *}
{*                    {moduleinsert name="\Users\Controller\Block\AuthBlock"}*}
{*                {/if}*}
{*                *}
{*                *}{* Поисковая строка *}
{*                {moduleinsert name="\Catalog\Controller\Block\SearchLine"}               *}
{*            </div>*}
{*        </div>*}
{*    </header>*}
{*    <div class="hotLinks viewport">*}
{*        <div class="links">*}
{*            <a href="/payment/" class="howToPay">{t}Как оплатить{/t}</a>*}
{*            <a href="/delivery/" class="howToShip">{t}Как получить{/t}</a>*}
{*        </div>*}
{*    </div>*}
{*    <div class="viewport mainContent">*}
{*        *}{* Список категорий товаров *}
{*        {moduleinsert name="\Catalog\Controller\Block\Category"}*}
{*        *}
{*        *}{* Данный блок будет переопределен у наследников данного шаблона *}
{*        {block name="content"}{/block}*}
{*      *}
{*        <footer>*}
{*            <div class="footzone">*}
{*                {moduleinsert name="\Menu\Controller\Block\Menu" indexTemplate='blocks/menu/foot_menu.tpl' root='0'}*}
{*                {if $CONFIG.facebook_group || $CONFIG.vkontakte_group || $CONFIG.twitter_group}*}
{*                <div class="social">*}
{*                    {if $CONFIG.facebook_group}*}
{*                        <a href="{$CONFIG.facebook_group}" class="facebook"></a>*}
{*                    {/if}*}
{*                    {if $CONFIG.vkontakte_group}*}
{*                        <a href="{$CONFIG.vkontakte_group}" class="vk"></a>*}
{*                    {/if}*}
{*                    {if $CONFIG.twitter_group}*}
{*                        <a href="{$CONFIG.twitter_group}" class="twitter"></a>*}
{*                    {/if}*}
{*                    {if $CONFIG.instagram_group}*}
{*                        <a href="{$CONFIG.instagram_group}" class="instagram"></a>*}
{*                    {/if}*}
{*                    {if $CONFIG.youtube_group}*}
{*                        <a href="{$CONFIG.youtube_group}" class="youtube"></a>*}
{*                    {/if}*}
{*                </div>*}
{*                {/if}*}
{*            </div>*}
{*            <div class="copyline">*}
{*                <a href="http://readyscript.ru" target="_blank" class="developer">{t}Работает на{/t} <span>ReadyScript</span></a>*}
{*                <span class="copy">&copy; {"now"|dateformat:"Y"} {t}Все права защищены{/t}</span>*}
{*            </div>*}
{*        </footer>*}
{*    </div>*}
{*    *}
{*    {block name="fixedCart"}  *}{* Разрешаем перезаписывать данный блок у наследников *}
{*    <div class="fixedCart">*}
{*        <div class="viewport">*}
{*            <a href="#" class="up" id="up" title="{t}наверх{/t}"></a>*}
{*            *}{* Кнопка обратная связь *}
{*            {moduleinsert name="\Feedback\Controller\Block\Button" form_id="1"}*}
{*            *}
{*            {if ModuleManager::staticModuleExists('shop')}*}
{*                *}{* Корзина *}
{*                {moduleinsert name="\Shop\Controller\Block\Cart"}*}
{*            {/if}*}
{*            *}
{*            {if $THEME_SETTINGS.enable_compare}*}
{*                *}{* Сравнить товары *}
{*                {moduleinsert name="\Catalog\Controller\Block\Compare" indexTemplate="blocks/compare/cart_compare.tpl"}*}
{*            {/if}*}
{*            *}
{*            {if $THEME_SETTINGS.enable_favorite}*}
{*                *}{* Избранное *}
{*                {moduleinsert name="\Catalog\Controller\Block\Favorite"}            *}
{*            {/if}*}
{*        </div>*}
{*    </div>*}
{*    {/block}*}

</div> <!-- .bodyWrap -->
