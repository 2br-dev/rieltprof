{$app->autoloadScripsAjaxBefore()}
{if !$url->isAjax()}
<div class="crud-ajax-group">
            <div class="updatable" data-url="{urlmake}">
{/if}
                <div class="viewport">
                    <div class="top-toolbar">
                        <div class="c-head">
                            {$mainMenuIndex = $elements->getMainMenuIndex()}
                            <h2 class="title titlebox">
                                <span class="go-to-menu" {if $mainMenuIndex !== false}data-main-menu-index="{$mainMenuIndex}"{/if}>{$elements.formTitle}</span>
                                {if isset($elements.topHelp)}<a class="help-icon" data-toggle-class="open" data-target-closest=".top-toolbar">?</a>{/if}</h2>

                            {if $elements.topToolbar}
                                <div class="buttons xs-dropdown place-left">
                                    <a class="btn btn-default toggle visible-xs-inline-block" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false" id="clientHeadButtons" >
                                        <i class="zmdi zmdi-more-vert"></i>
                                    </a>
                                    <div class="xs-dropdown-menu" aria-labelledby="clientHeadButtons">
                                        {$elements.topToolbar->getView()}
                                    </div>
                                </div>
                            {/if}
                        </div>

                        <div class="c-help notice notice-warning">
                            {$elements.topHelp}
                        </div>
                    </div>

                    {$elements.headerHtml}
                </div>

                {$elements.form}

{if !$url->isAjax()}
            </div> <!-- .updatable -->
    
    {if $elements.bottomToolbar}
    <div class="footerspace"></div>
    <div class="bottom-toolbar fixed">
        <div class="viewport">
            <div class="common-column">
                    {$elements.bottomToolbar->getView()}
            </div>
        </div>
    </div>    
    {/if}    
</div>
{/if}
{$app->autoloadScripsAjaxAfter()}