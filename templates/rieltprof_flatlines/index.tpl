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
{/block}
