{addjs file="jquery.rs.autotranslit.js"}
{addjs file="jquery.rs.messenger.js"}
{addjs file="jstour/jquery.tour.engine.js" basepath="common"}
{addjs file="jstour/jquery.tour.js" basepath="common"}
{addjs file="%main%/jquery.rsnews.js"}
{addjs file="jquery.rs.admindebug.js"}
{addjs file="jquery.rs.barcode.js"}

{addcss file="flatadmin/readyscript.ui/jquery-ui.css" basepath="common"}
{addcss file="flatadmin/app.css?v=2" basepath="common" no_compress=true}
{addcss file="flatadmin/iconic-font/css/material-design-iconic-font.min.css" basepath="common"}
{addcss file="common/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css" basepath="common"}
{addcss file="common/tour.css" basepath="common"}
{addcss file="common/animate.css" basepath="common"}


{addjs file="jquery.min.js" name="jquery" basepath="common"}
{addjs file="dialog-options/jquery.dialogoptions.js" basepath="common"}
{addjs file="bootstrap/bootstrap.min.js" basepath="common"}
{addjs file="malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js" basepath="common"}
{addjs file="webpjs/rs.webpcheck.js"}
{addjs file="%crm%/jquery.rs.telephony.js"}

{if strstr($smarty.server.HTTP_USER_AGENT, 'iPad')}
    {addmeta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"}
{else}
    {addmeta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=0.75"}
{/if}

{$app->setBodyClass('admin-body admin-style')}
<header id="header" class="clearfix" data-spy="affix" data-offset-top="65">
    <ul class="header-inner">
        <li class="rs-logo">
            <a href="{adminUrl mod_controller=false do=false}" title="{t}на&nbsp;главную{/t}" data-placement="right"></a>
            <div id="menu-trigger"><i class="zmdi zmdi-menu"></i></div>
        </li>
        
        <li class="header-panel">
            <div class="viewport">
                <div class="fixed-tools">
                    <a href="{adminUrl mod_controller=false do=false}" class="to-main">
                        <i class="rs-icon rs-black-logo"></i><br>
                        <span>{t}главная{/t}</span>
                    </a>

                    <a href="{$site_root_url}" class="to-site">
                        <i class="rs-icon rs-icon-view"></i><br>
                        <span>{t}на сайт{/t}</span>
                    </a>

                    <a href="{$router->getUrl('main.admin', ["Act" => "cleanCache"])}" class="rs-clean-cache">
                        <i class="rs-icon rs-icon-refresh"></i><br>
                        <span>{t}кэш{/t}</span>
                    </a>
                </div>

                <div class="float-tools">
                    <div class="dropdown rs-meter-group">
                        <a class="toggle visible-xs-inline-block" data-toggle="dropdown" id="floatTools" aria-haspopup="true">
                            <i class="zmdi zmdi-more-vert">{meter}</i>
                        </a>
                        <ul class="ft-dropdown-menu" aria-labelledby="floatTools">
                            {moduleinsert name="\Main\Controller\Admin\Block\HeaderPanel" indexTemplate="%main%/adminblocks/headerpanel/header_panel_items.tpl"}
                            {moduleinsert name="\Main\Controller\Admin\Block\RsAlerts"}
                            {moduleinsert name="\Main\Controller\Admin\Block\RsNews"}

                            <li class="ft-hover-node">
                                <a href="{adminUrl mod_controller="users-ctrl" do="edit" id=$current_user.id}">
                                    <i class="rs-icon rs-icon-user"></i>
                                    <span>{$current_user->getFio()}</span>
                                </a>

                                <ul class="ft-sub">
                                    <li>
                                        <a href="{$router->getUrl('main.admin', [Act => 'logout'])}">
                                            <i class="rs-icon zmdi zmdi-power"></i>
                                            <span>{t}Выход{/t}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</header>

<aside id="sidebar">
    {if $smarty.cookies.rsAdminSideMenu}{$app->setBodyClass('closed', true)}{/if}
    {modulegetvars name="\Site\Controller\Admin\BlockSelectSite" var="sites"}

    <ul class="side-menu rs-site-manager">
        <li class="sm-node">
            <a class="current">
                {foreach $sites.sites as $site}
                    {if $site.id == $sites.current.id}
                        <span class="number">{$site@iteration}</span>
                        <span class="domain">{$sites.current.title|teaser:"27"}</span>
                    {/if}
                {/foreach}
                <span class="caret"></span>
            </a>
            <div class="sm">
                <div class="sm-head">
                    <a class="menu-close"><i class="zmdi zmdi-close"></i></a>
                    {t}Выберите сайт{/t}
                </div>
                <div class="sm-body">
                    <ul>
                        {foreach $sites.sites as $site}
                        <li>
                            <li {if $sites.current.id == $site.id}class="active"{/if}>
                                <a href="{$router->getUrl('main.admin', ['Act' => 'changeSite', 'site' => $site.id])}">{$site@iteration}. {$site.title}</a>
                            </li>
                        </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        </li>
    </ul>
    
    <div class="side-scroll">
        {moduleinsert name="\Menu\Controller\Admin\View"}

        {if ModuleManager::staticModuleExists('marketplace')}
        <ul class="side-menu side-utilites">
            <li>
                <a href="{$router->getAdminUrl(false, [], 'marketplace-ctrl')}">
                    <i class="rs-icon rs-icon-marketplace"></i>
                    <span class="title">{t alias="маркетплейс"}Маркет<span class="visible-open">плейс</span>{/t}</span>
                </a>
            </li>
        </ul>
        {/if}
    </div>

    <a class="side-collapse" data-toggle-class="closed" data-target="body" data-toggle-cookie="rsAdminSideMenu">
        <i class="rs-icon rs-icon-back"></i>
        <span class="text">{t}Свернуть меню{/t}</span>
    </a>
</aside>

<section id="content">
    {moduleinsert name="\Main\Controller\Admin\Block\RsVisibleAlerts"}
    {$app->blocks->getMainContent()}
</section>