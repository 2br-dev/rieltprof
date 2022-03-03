{* Типовая страница с таблицей данных в административной панели *}
{if !$url->isAjax()}
<div class="crud-ajax-group crud-view-table">
            <div class="updatable" data-url="{urlmake}">
{/if}
                <div class="top-toolbar viewport">
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

                <div class="columns">
                    <div class="column common-column">

                        <div class="viewport">
                            {$elements.beforeTableContent}
                        </div>

                        {if isset($elements.paginator) || isset($elements.filter)}
                        <div class="beforetable-line{if isset($elements.filter) && !isset($elements.paginator)} flex-d-row{/if}">
                            {if isset($elements.paginator)}
                                {$elements.paginator->getView(['short' => true])}
                            {/if}                        

                            <div class="filter-block">
                                {if isset($elements.filter)}
                                    {$elements.filter->getView()}
                                {/if}

                                {if isset($elements.filterContent)}
                                    {$elements.filterContent}
                                {/if}
                            </div>
                        </div>
                        {/if}

                        <div class="viewport">
                            {if isset($elements.filter)}
                                {$elements.filter->getPartsHtml()}
                            {/if}
                        </div>

                        {if isset($elements.table)}
                            <form method="POST" enctype="multipart/form-data" action="{urlmake}" class="crud-list-form">
                                {foreach from=$elements.hiddenFields key=key item=item}
                                <input type="hidden" name="{$key}" value="{$item}">
                                {/foreach} 
                                {$elements.table->getView()}
                            </form>
                        {/if}

                        <div class="viewport">
                            {if isset($elements.paginator)}
                                {$elements.paginator->getView()}
                            {/if}
                        </div>

                        {if $elements.bottomToolbar}
                            <div class="bottom-toolbar">
                                <div class="common-column viewport">
                                    {$elements.bottomToolbar->getView()}
                                </div>
                            </div>
                        {/if}
                    </div>
                </div> <!-- .columns -->

{if !$url->isAjax()}
            </div> <!-- .updatable -->
</div>
{/if}