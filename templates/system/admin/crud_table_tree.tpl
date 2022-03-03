{if !$url->isAjax()}
<div class="crud-ajax-group crud-view-table-tree {if $smarty.cookies.viewas == 'table'}left-up{/if}">
            <div class="updatable" data-url="{urlmake}">
{/if}
                <div class="top-toolbar viewport">
                    <div class="c-head">
                        {$mainMenuIndex = $elements->getMainMenuIndex()}
                        <h2 class="title">
                            <span class="go-to-menu" {if $mainMenuIndex !== false}data-main-menu-index="{$mainMenuIndex}"{/if}>{$elements.formTitle}</span> {if isset($elements.topHelp)}<a class="help-icon" data-toggle-class="open" data-target-closest=".top-toolbar">?</a>{/if}</h2>

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

                {if $elements.tree}
                    <div class="collapsed viewport">
                            <div class="path">
                                <i class="zmdi zmdi-folder-outline icon" data-toggle-class="left-open" data-target-closest=".crud-view-table-tree"></i>
                                {$elements.tree->getPathView()}
                            </div>
                            <div class="buttons">
                                <a class="select-tree btn btn-default" data-toggle-class="left-open" data-target-closest=".crud-view-table-tree">
                                    <span class="tree-show"><i class="zmdi zmdi-chevron-right"></i></span>
                                    <span class="tree-hide"><i class="zmdi zmdi-close"></i></span>
                                </a>
                            </div>
                    </div>
                {/if}

                <div class="columns">
                    <div class="column left-column">
                        <div class="filter-back" data-filter-placement="left">
                            {if isset($elements.treeFilter)}
                                {$elements.treeFilter->getView()}
                            {/if}
                        </div>

                        <div class="viewport">
                            {if isset($elements.treeFilter)}
                                {$elements.treeFilter->getPartsHtml()}
                            {/if}
                        </div>

                        <form method="POST" enctype="multipart/form-data" action="{urlmake}" id="tree-form" class="twisted-left">
                            {if isset($elements.tree)}
                                {$local_options = []}
                                {$forced_open_nodes = false}
                                {if isset($elements.treeFilter)}
                                    {$local_options.filter = $elements.treeFilter}

                                    {if $elements.treeFilter->getKeyVal()}
                                        {$local_options.forced_open_nodes = true}
                                    {/if}
                                {/if}
                                {if $url->isAjax()}
                                    {$local_options.render_opened_nodes = true}
                                {/if}

                                {$elements.tree->getView($local_options)}
                            {/if}

                            {if $elements.treeBottomToolbar}
                            <div class="bottom-toolbar">
                                <div class="viewport">
                                    {$elements.treeBottomToolbar->getView()}
                                </div>
                            </div>
                            {/if}
                        </form>
                    </div> <!-- .left-column -->

                    <div class="column right-column">
                        <div class="beforetable-line">
                            <div class="view-control">
                                {if isset($elements.paginator)}
                                    {$elements.paginator->getView(['short' => true])}
                                {/if}
                                <div class="view-switcher">
                                    <a class="view-as-tree-table" data-remove-class="left-up"
                                                                  data-target-closest=".crud-view-table-tree"
                                                                  data-set-cookie="viewas"
                                                                  data-set-cookie-value="table-tree"
                                                                  data-set-cookie-path="."
                                                                  title="{t}Категории слева{/t}"></a>

                                    <a class="view-as-table" data-add-class="left-up"
                                                             data-target-closest=".crud-view-table-tree"
                                                             data-set-cookie="viewas"
                                                             data-set-cookie-value="table"
                                                             data-set-cookie-path=""
                                                             title="{t}Категории сверху{/t}"></a>
                                </div>
                            </div>
                            <div class="filter-control" data-filter-placement="right">
                                <div class="filter-block">
                                    {if isset($elements.filter)}
                                        {$elements.filter->getView()}
                                    {/if}

                                    {if isset($elements.filterContent)}
                                        {$elements.filterContent}
                                    {/if}
                                </div>
                            </div>


                        </div>

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
                                <div class="viewport">
                                    {$elements.bottomToolbar->getView()}
                                </div>
                            </div>
                        {/if}
                    </div> <!-- .right-column -->
                </div> <!-- .columns -->
{if !$url->isAjax()}
            </div> <!-- .updatable -->
</div>
{/if}