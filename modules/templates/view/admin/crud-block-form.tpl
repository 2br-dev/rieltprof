{$app->autoloadScripsAjaxBefore()}
<div class="crud-ajax-group">
    {if !$url->isAjax()}
    <div id="content-layout">
        <div class="viewport">
    {/if}
            <div class="contentbox">
                <span class="titlebox gray-around" data-dialog-options='{ "width":900, "height":500 }'>{$elements.formTitle}</span>

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

                    <div class="block-information inform-block" style="margin-top:10px;">
                        <table>
                            <tr>
                                <td align="right">{t}Контроллер:{/t}</td>
                                <td><strong>{$elements.block_controller}</strong></td>
                            </tr>
                        </table>
                    </div>

                    {if $params_loaded_from_db}
                        <div class="block-information notice notice-warning m-t-10">
                            {t}Параметры блока загружены из базы данных{/t}
                        </div>
                    {/if}

                    {$elements.form}

                    {if empty($elements.form)}
                        <div class="no_block_options">
                            {t}У блока нет настроек{/t}
                        </div>
                    {/if}

                </div>
            </div>
        {if !$url->isAjax()}
        </div> <!-- .viewport -->
    </div> <!-- .content -->
    {/if}

    <div class="footerspace"></div>
    <div class="bottom-toolbar fixed">
        <div class="viewport">
            <div class="common-column">
                {if isset($elements.bottomToolbar)}
                    {$elements.bottomToolbar->getView()}
                {/if}
            </div>
        </div>
    </div>

</div>
{$app->autoloadScripsAjaxAfter()}