{$app->autoloadScripsAjaxBefore()}
<div class="crud-ajax-group">

    <div class="viewport contentbox{if !isset($elements.bottomToolbar)} no-bottom-toolbar{/if}">

        {if $elements.topToolbar || $elements.formTitle}
            <div class="headerbox">
                {if $elements.topToolbar}
                    <div class="buttons">
                        {$elements.topToolbar->getView()}
                    </div>
                {/if}

                    <span class="titlebox gray-around">{$elements.formTitle}</span>
            </div>
        {/if}

        {$elements.headerHtml}

        <div class="middlebox {$middleclass}">
            <div class="crud-form-error">
                {if count($elements.formErrors)}
                    <ul class="error-list">
                        {foreach from=$elements.formErrors item=data}
                            <li>
                                <div class="{$data.class|default:"field"}">{$data.fieldname}<i class="cor"></i></div>
                                <div class="text">
                                    {foreach from=$data.errors item=error}
                                    {$error}
                                    {/foreach}
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                {/if}
            </div>

            <div class="crud-form-success text-success"></div>

            {$elements.form}
        </div>
    </div> <!-- .viewport -->

    {if isset($elements.bottomToolbar)}
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
{$app->autoloadScripsAjaxAfter()}