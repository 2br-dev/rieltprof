{$app->autoloadScripsAjaxBefore()}
<div class="crud-ajax-group">
    {if !$url->isAjax()}
    <div id="content-layout">
        <div class="updatable" data-url="{adminUrl mod=$module_item->getName()}">
    {/if}
            <div class="viewport">
                <a class="titlebox gray-around va-m-c" data-side-panel="{adminUrl do="ajaxModuleList" mod_controller="modcontrol-control"}">
                    <i class="zmdi zmdi-tag-more f-20 m-r-10" title="{t}Все модули{/t}"></i>
                    <span>{$elements.formTitle}</span>
                </a>

                <div class="middlebox">
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

                    {if $module_item->getConfig()->installed}
                        <div class="columns">

                            <div class="form-column">
                                {$config=$module_item->getConfig()}
                                {if !$config->isMultisiteConfig()}
                                    <br><div class="notice-box">{t}Настройки данного модуля едины для всех сайтов в рамках мультисайтовости{/t}</div>
                                {/if}

                                {$level = ''}
                                {$license_text = $module_license_api->getLicenseDataText($module_item->getName(), $level)}
                                {if !$config.is_system && $license_text}
                                    <br><div class="notice m-b-10 text-{$level}">
                                        {$license_text}
                                    </div>
                                {/if}

                                {$elements.form}
                            </div>

                            <div class="tools-column">
                                <div class="controller_info">
                                    <h3>{t}Утилиты{/t}</h3>
                                    <a name="actions"></a>
                                    <ul class="list-with-help">
                                        {foreach from=$module_item->getTools() item=item}
                                            <li>
                                                <a {if isset($item.target)}target="{$item.target}"{/if} {if !empty($item.confirm)}data-confirm-text="{$item.confirm}"{/if} class="{if $item.class}{$item.class}{else}crud-get{/if}" href="{$item.url}" {if $item.attr}{foreach $item.attr as $key=>$value} {$key}="{$value}"{/foreach}{/if} style="text-decoration:underline">
                                                    {$item.title}
                                                </a>
                                                {if $item.description}<div class="tool-descr">{$item.description}</div>{/if}
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>

                        </div>
                    {else}
                        <div class="inform-block margvert10">
                            {t}Модуль не установлен.{/t} <a href="{adminUrl do=ajaxreinstall module=$module_item->getName()}" class="u-link crud-get">{t}Установить{/t}</a>
                        </div>
                    {/if}
                </div>
            </div>

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

    {if !$url->isAjax()}
            </div>
    </div> <!-- .content -->
    {/if}
</div>
{$app->autoloadScripsAjaxAfter()}