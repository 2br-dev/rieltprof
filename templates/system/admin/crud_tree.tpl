{if !$url->isAjax()}
<div class="crud-ajax-group">
    <div id="content-layout">
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
                        {if isset($elements.filter)}
                            <div class="beforetable-line">
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
                            {if isset($elements.treeFilter)}
                                {$elements.treeFilter->getPartsHtml()}
                            {/if}
                        </div>

                        {if isset($elements.tree)}
                            <form method="POST" enctype="multipart/form-data" action="{urlmake}" class="crud-list-form">
                                {$elements.tree->getView()}
                            </form>
                        {/if}

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
    </div> <!-- #content -->
</div>
{/if}