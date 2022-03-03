{addjs file="{$mod_js}jquery.blockeditor.js" basepath="root"}
{addcss file="{$mod_css}manager.css" basepath="root"}
{addcss file="{$mod_css}moduleblocks.css" basepath="root"}
{addcss file="common/960gs/960.fluid.css" basepath="common"}
{addcss file="%templates%/bootstrap.css"}
{addcss file="%templates%/bootstrap4.css"}
{if !$url->isAjax()}
<div class="crud-ajax-group">
        <div class="viewport">
            <div class="updatable default-updatable" data-url="{adminUrl context=$context page_id=$page_id}">
{/if}
                <div class="top-toolbar">
                    <div class="c-head">
                        {$mainMenuIndex = $elements->getMainMenuIndex()}
                        <h2 class="title">
                            <span class="go-to-menu" {if $mainMenuIndex !== false}data-main-menu-index="{$mainMenuIndex}"{/if}>{$elements.formTitle}</span>
                            {if isset($elements.topHelp)}<a class="help-icon" data-toggle-class="open" data-target-closest=".top-toolbar">?</a>{/if}</h2>

                        <div class="buttons xs-dropdown place-left">
                            <a class="btn btn-default toggle visible-xs-inline-block" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false" id="clientHeadButtons" >
                                <i class="zmdi zmdi-more-vert"></i>
                            </a>
                            <div class="xs-dropdown-menu" aria-labelledby="clientHeadButtons">
                                {if $elements.topToolbar}
                                    {$elements.topToolbar->getView()}
                                {/if}
                            </div>
                        </div>
                    </div>

                    <div class="c-help notice notice-warning">
                        {$elements.topHelp}
                    </div>

                    {$elements.headerHtml}
                </div>

                <div class="context-setup">
                    {t}Контекст{/t} <span class="help-icon" title="{t}В разном контексте могут быть различные настройки страниц, секций, блоков. Контексты могут привносить в систему различные модули, например, партнерский модуль. Так, для каждого партнера можно задать собственную разметку.{/t}">?</span>:
                    <span class="dropdown">
                        <span class="btn btn-default" data-toggle="dropdown">
                            {$context_list[$context].title}
                            <span class="caret"></span>
                        </span>
                        {if count($context_list)>1}
                        <ul class="dropdown-menu">
                            {foreach $context_list as $key => $item}
                                {if $context!=$key}
                                <li>
                                    <a href="{adminUrl context=$key}" class="block-link-item">{$item.title}</a>
                                </li>
                                {/if}
                            {/foreach}
                        </ul>
                        {/if}
                    </span>

                    <a href="{adminUrl do="contextOptions" context=$context}" class="crud-edit btn btn-default">
                        <i class="zmdi zmdi-settings m-r-5"></i>
                        <span>{t}Настройки темы оформления{/t}</span>
                    </a>
                </div>


                <div class="columns">
                    <div class="common-column">

                        <div class="rs-tabs" role="tabpanel">
                            <ul class="tab-nav resizable-column" role="tablist" data-min-width="280">
                                <li {if $currentPage.route_id=='default'}class="active"{/if}>
                                    <span class="item">
                                        <a class="call-update" href="{adminUrl context=$context}">{t}По умолчанию{/t}</a>
                                        <a class="crud-edit tool zmdi zmdi-settings" href="{adminUrl do="editPage" context=$context}" title="{t}Настройки страницы{/t}"></a>
                                        <a class="crud-edit tool zmdi zmdi-search-for" href="{adminUrl mod_controller="pageseo-ctrl" do="edit" id="default" context=$context create=1}" title="{t}Настройки SEO{/t}"></a>
                                    </span>
                                </li>

                                {foreach from=$pages item=page}
                                    {if $page.route_id != 'default'}
                                        <li {if $currentPage.id==$page.id}class="active"{/if}>
                                            <span class="item">
                                                <a class="call-update" href="{adminUrl page_id=$page.id context=$context}">{if $page->getRoute() !== null}{$page->getRoute()->getDescription()}{else}{t}Маршрут не найден <span class="help-icon" title="{$page.route_id}">?</span>{/t}{/if}</a>
                                                <a class="crud-edit tool zmdi zmdi-settings" href="{adminUrl do="editPage" id=$page.id}" title="{t}Настройки страницы{/t}"></a>
                                                <a class="crud-edit tool zmdi zmdi-search-for" href="{adminUrl mod_controller="pageseo-ctrl" do="edit" id=$page.route_id create=1}" title="{t}Настройки SEO{/t}"></a>
                                                <a class="crud-remove-one tool zmdi zmdi-close c-red" href="{adminUrl do="delPage" id=$page.id}" title="{t}удалить{/t}"></a>
                                            </span>
                                        </li>
                                    {/if}
                                {/foreach}
                            </ul>

                            <div class="tab-content depend-resizable-column">
                                {include file="%templates%/gs/{$grid_system}/pageview.tpl"}
                            </div>
                        </div>
                    </div>
                </div> <!-- .columns -->

{if !$url->isAjax()}
            </div> <!-- .updatable -->
        </div> <!-- .viewport -->
</div>

<script>
    $.contentReady(function() {
        $('.pageview').blockEditor({
            sortContainerUrl: '{adminUrl do="ajaxMoveContainer" ajax=1}',
            sortSectionUrl: '{adminUrl do="ajaxMoveSection" ajax=1}',
            sortBlockUrl: '{adminUrl do="ajaxMoveBlock" ajax=1}',
            toggleViewBlock: '{adminUrl do="ajaxToggleViewModule" ajax=1}',
            gridSystem: '{$grid_system}'
        });
    });
</script>
{/if}