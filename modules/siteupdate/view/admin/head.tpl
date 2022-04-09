{addjs file="{$mod_js}siteupdate.js" basepath="root"}
{addcss file="{$mod_css}siteupdate.css" basepath="root"}

{if $success_text}
    <div class="viewport text-success">
        {t}Успех!{/t}
        {$success_text}
    </div>
{/if}
<br>
{if $currentStep=='1'}
    <ul class="viewport stepbystep" data-current-step="{$currentStep}">
        <li class="first{if $currentStep=='1'} act{/if}{if $currentStep>1} already{/if} step1">
            <a href="{adminUrl do=false}" class="btn btn-success btn-lg check-update {if !$canUpdate} disabled{/if}" data-change-text="{t}идет проверка обновлений...{/t}">{t}проверить обновления{/t}</a>
        </li>
    </ul>

    {if $canUpdate}
        <div class="clicktostart">
            <span class="hidden-xs">&larr;</span> {t}Нажмите, чтобы проверить наличие обновлений для Вашей системы.{/t}
        </div>
    {/if}
{else}
    <div class="stepbystep-wrapper">
        <div class="viewport">
            <ul class="stepbystep clearfix" data-current-step="{$currentStep}">
                <li class="first{if $currentStep=='1'} act{/if}{if $currentStep>1} already{/if} step1">
                    <a href="{adminUrl do=false}" class="check-update item" >{t}проверка обновлений{/t}</a>
                </li>
                <li class="{if $currentStep=='2'}act{/if}{if $currentStep>2} already{/if} step2">
                    {if is_array($data) && count($data.products)>1}
                        <a href="{adminUrl do='selectProduct'}" class="item">{if $currentStep == 3}{t}Продукт:{/t} {$data.updateProduct}{else}{t}выбор продукта{/t}{/if}</a>
                    {else}
                        <span class="item">{t}Продукт:{/t} {$data.updateProduct}</span>
                    {/if}
                </li>
                <li class="{if $currentStep=='3'}act{/if} step3">
                    <span class="item">{t}установка обновлений{/t}</span>
                </li>
            </ul>
        </div>
    </div>
{/if}

<div class="clear"></div>
<div class="error-block viewport">
{if !empty($errors)}
    <ul class="error-list">
        {foreach from=$errors item=data}
        <li>
            <div class="field">{$data.fieldname}<i class="cor"></i></div>
            <div class="text">
                {foreach $data.errors as $error}
                    {$error}
                {/foreach}
            </div>
        </li>
        {/foreach}
    </ul>
{/if}
</div>