{*{extends file="%THEME%/wrapper.tpl"}*}
{$rieltprof_config = \RS\Config\Loader::ByModule('rieltprof')}
{block name="content"}
    {if $THEME_SETTINGS.enable_favorite}
        {addjs file="rs.favorite.js"}
    {/if}
    <div class="global-wrapper" id="main">
        {include file="%rieltprof%/sidebar.tpl"}
        <div class="content">
            <div class="top-block">
                <div class="row">
                    <div class="col">
                        {include file='%rieltprof%/add-object-menu.tpl' referer='/'}
                        {include file='%rieltprof%/search-object-menu.tpl'}
                    </div>
                    <div class="col right-align force">
                        <a href="/blacklist/" class="btn" id="check-contact"><span>Проверить контакт</span></a>
                        <a href="" class="btn modal-trigger" data-target-modal="abuse-contact"><span>Внести контакт</span></a>
                    </div>
                    <div href="" class="burger" data-target="profile-sidebar">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </div>
                </div>
            </div>
            <div class="main-block">
                <h1>Последние добавленные объекты</h1>
                {moduleinsert name="\Rieltprof\Controller\Block\Allads"}
            </div>
        </div>
        {include file='%rieltprof%/statusbar.tpl'}
        {include file='%rieltprof%/form/add-contact.tpl'}
    </div>






{*    <div class="index_wrapper">*}
{*        <a href="{$router->getAdminUrl('add', ['referer' => '/', 'sale_dir' => 3, 'rent_dir' => 13, 'object' => 'Квартира', 'action' => 'sale'], 'rieltprof-flatctrl')}" class="crud-add">Добавить квартиру</a>*}
{*        <a href="{$router->getAdminUrl('add', ['referer' => '/', 'sale' => 3, 'rent' => 13, 'object' => 'Гараж', 'action' => 'sale'], 'rieltprof-garagectrl')}" class="crud-add">Добавить гараж</a>*}
{*        <a href="{$router->getAdminUrl('add', ['referer' => '/', 'sale' => 3, 'rent' => 13, 'object' => 'Гараж', 'action' => 'rent'], 'rieltprof-garagectrl')}" class="crud-add">Аренда гараж</a>*}
{*        <a href="{$router->getAdminUrl('add', ['dir' => 1, 'referer' => $router->getUrl('users-front-profile')], 'gallerist-ctrl')}"*}
{*           class="crud-add">Добавить картину</a>*}
{*    </div>*}
{*    *}{* Баннеры *}
{*    {moduleinsert name="\Banners\Controller\Block\Slider" zone="fashion-center"}*}

{*    <div class="box mt40">*}
{*        *}{* Лидеры продаж *}
{*        {moduleinsert name="\Catalog\Controller\Block\TopProducts" dirs="samye-prodavaemye-veshchi" pageSize="5"}*}
{*             *}
{*        <div class="oh mt40">*}
{*            <div class="left">*}
{*                *}{* Новости *}
{*                {moduleinsert name="\Article\Controller\Block\LastNews" indexTemplate="blocks/lastnews/lastnews.tpl" category="2" pageSize="4"}*}
{*            </div>*}
{*            <div class="right">*}
{*                *}{* Оплата и возврат *}
{*                {moduleinsert name="\Article\Controller\Block\Article" indexTemplate="blocks/article/main_payment_block.tpl" article_id="molodezhnaya--glavnaya--ob-oplate"}*}
{*                *}
{*                *}{* Доставка *}
{*                {moduleinsert name="\Article\Controller\Block\Article" indexTemplate="blocks/article/main_delivery_block.tpl" article_id="molodezhnaya--glavnaya--o-dostavke"}*}
{*            </div>*}
{*        </div>*}
{*        *}{* Товары во вкладках *}
{*        {moduleinsert name="\Catalog\Controller\Block\ProductTabs" categories=["populyarnye-veshchi", "novye-postupleniya"] pageSize=6}*}
{*        *}
{*        *}{* Бренды *}
{*        {moduleinsert name="\Catalog\Controller\Block\BrandList"}*}

{*    </div>*}
{/block}
