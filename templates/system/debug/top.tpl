{addjs file="jquery.rs.admindebug.js" basepath="common" unshift=true}
{addjs file="jquery.ui/jquery-ui.min.js" basepath="common" unshift=true}
{addjs file="jquery.min.js" name="jquery" basepath="common" header=true unshift=true}
{addjs file="dialog-options/jquery.dialogoptions.js" basepath="common"}
{addjs file="bootstrap/bootstrap.min.js" name="bootstrap" basepath="common"}

{addjs file="lab/lab.min.js" basepath="common"}

{addjs file="jquery.datetimeaddon/jquery.datetimeaddon.min.js" basepath="common"}
{addjs file="jquery.rs.debug.js" basepath="common"}
{addjs file="jquery.rs.ormobject.js" basepath="common"}
{addjs file="jquery.cookie/jquery.cookie.js" basepath="common"}
{addjs file="jquery.form/jquery.form.js" basepath="common"}
{addjs file="jstour/jquery.tour.engine.js" basepath="common"}
{addjs file="jstour/jquery.tour.js" basepath="common"}

{addcss file="flatadmin/iconic-font/css/material-design-iconic-font.min.css" basepath="common"}
{addcss file="flatadmin/readyscript.ui/jquery-ui.css" basepath="common"}
{addcss file="flatadmin/app.css?v=2" basepath="common"}
{addcss file="common/animate.css" basepath="common"}
{addcss file="common/tour.css" basepath="common"}

{if $this_controller->getDebugGroup()}
    {addcss file="flatadmin/debug.css" basepath="common"}
    {addcss file="%templates%/moduleblocks.css"}
{/if}
{$has_debug_right = $current_user->checkModuleRight('main', Main\Config\ModuleRights::RIGHT_DEBUG_MODE)}
<div id="debug-top-block" class="admin-style">
    <header id="header">
        <ul class="header-inner">
            <li class="rs-logo debug">
                <a href="{$router->getRootUrl()}"></a>
            </li>

            <li class="header-panel">
                <div class="viewport">
                    <div class="fixed-tools">
                        <a href="{$router->getUrl('main.admin')}" class="to-admin">
                            <i class="rs-icon rs-icon-admin"></i><br>
                            <span>{t}управление{/t}</span>
                        </a>

                        <a href="{$router->getUrl('main.admin', ["Act" => "cleanCache"])}" class="rs-clean-cache">
                            <i class="rs-icon rs-icon-refresh"></i><br>
                            <span>{t}кэш{/t}</span>
                        </a>

                        {if $has_debug_right}
                            <div class="debug-mode-switcher">
                                <div data-url="{$router->getUrl('main.admin', ["Act" => 'ajaxToggleDebug'])}" class="toggle-switch rs-switch {if $this_controller->getDebugGroup()}on{/if}">
                                    <label class="ts-helper"></label>
                                </div>
                                <div class="debugmode-text"><span class="hidden-xs">{t}режим отладки{/t}</span><span class="visible-xs">{t}отладка{/t}</span></div>
                            </div>
                        {/if}

                        {if $this_controller->getDebugGroup()} {* Переключение режимов отладки *}
                            <div class="rs-toggle-debug-modes toggle-debug-modes bt dropdown">
                                <i class="rs-icon rs-icon-debug-{$this_controller->app->getDebugMode()}"></i><br>
                                <span>{t}править{/t}...</span>

                                <ul class="fxt-dropdown-menu">
                                    <li class="fxt-hover-node">
                                        <a href="{$router->getUrl('main.admin', ["Act" => "ajaxToggleDebugMode", "mode" => "blocks"])}" data-body-class="blocks">
                                            <i class="rs-icon rs-icon-modes rs-icon-mode-blocks"></i>
                                            <span>{t}Блоки{/t}</span>
                                        </a>
                                    </li>
                                    <li class="fxt-hover-node">
                                        <a href="{$router->getUrl('main.admin', ["Act" => "ajaxToggleDebugMode", "mode" => "sectionsandrows"])}" data-body-class="sectionsandrows">
                                            <i class="rs-icon rs-icon-modes rs-icon-mode-sectionsandrows"></i>
                                            <span>{t}Секции и строки{/t}</span>
                                        </a>
                                    </li>
                                    <li class="fxt-hover-node">
                                        <a href="{$router->getUrl('main.admin', ["Act" => "ajaxToggleDebugMode", "mode" => "containers"])}" data-body-class="containers">
                                            <i class="rs-icon rs-icon-modes rs-icon-mode-containers"></i>
                                            <span>{t}Контейнеры{/t}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        {/if}
                    </div>

                    <div class="float-tools">
                        <div class="dropdown">
                            <a class="toggle visible-xs-inline-block" data-toggle="dropdown" id="floatTools" aria-haspopup="true"><i class="zmdi zmdi-more-vert"></i></a>

                            <ul class="ft-dropdown-menu" aria-labelledby="floatTools">
                                {moduleinsert name="\Main\Controller\Admin\Block\HeaderPanel" public=true indexTemplate="%main%/adminblocks/headerpanel/header_public_panel_items.tpl"}
                                {if $has_debug_right && !$Smarty.const.CLOUD_UNIQ && $timing->isEnable()}
                                    <li>
                                        <a class="rs-open-performance-report" title="{t}Отчет{/t}" href="{$timing->getReportUrl()}" target="_blank">
                                            <i class="rs-icon rs-icon-performance"></i>
                                            <span>{t}Отчет{/t}</span>
                                        </a>
                                    </li>
                                {/if}
                                <li>
                                    <a class="hidden-xs action start-tour" data-tour-id="welcome" title="{t}Обучение{/t}">
                                        <i class="rs-icon rs-icon-tour"></i>
                                        <span>{t}Обучение{/t}</span>
                                    </a>
                                </li>
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
</div>
{$result_html}