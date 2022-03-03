{addjs file="{$tfolders.mod_js}tplmanager.js" basepath="root"}
{addjs file="{$tfolders.mod_js}selecttemplate.js" basepath="root"}
{addjs file="%alerts%/notice-config.js"}

<div class="viewport">
    {if !$cfg->sms_sender_login && !$cfg->sms_sender_pass}
        <div class="notice-box">
            {t}Внимание! Отправка SMS невозможна, так как не настроен модуль "Уведомления". Укажите логин и пароль аккаунта для отправки SMS{/t}
        </div>
    {/if}
    <div class="crud-form-success text-success"></div>
    <div class="formbox">
        <form method="post" class="crud-form">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th>{t}Описание{/t}</th>
                        <th>{t}E-Mail{/t}</th>
                        <th>{t}SMS{/t}</th>
                        <th>{t}ПК{/t}</th>
                        <th width="44"></th>
                    </tr>
                </thead>
                <tbody>
                {foreach $alerts as $item}
                    {$tpls = $item->getDefaultTemplates()}
                    <tr>
                        <td class="title">{$item.description}</td>
                        <td>
                            {if $item->hasEmail()}
                                <input type="hidden" name="enable_email[{$item.id}]" value="0">
                                <div class="toggle-switch">
                                    <input hidden="hidden" type="checkbox" name="enable_email[{$item.id}]" value="1" {if $item.enable_email}checked="checked"{/if} id="cb-email-{$item.id}">
                                    <label for="cb-email-{$item.id}" class="ts-helper"></label>
                                </div>
                            {/if}
                        </td>
                        <td>
                            {if $item->hasSms()}
                                <input type="hidden" name="enable_sms[{$item.id}]" value="0">
                                <div class="toggle-switch">
                                    <input hidden="hidden" type="checkbox" name="enable_sms[{$item.id}]" value="1" {if $item.enable_sms}checked="checked"{/if} id="cb-sms-{$item.id}">
                                    <label for="cb-sms-{$item.id}" class="ts-helper"></label>
                                </div>
                            {/if}
                        </td>
                        <td>
                            {if $item->hasDesktop()}
                                <input type="hidden" name="enable_desktop[{$item.id}]" value="0">
                                <div class="toggle-switch">
                                    <input hidden="hidden" type="checkbox" name="enable_desktop[{$item.id}]" value="1" {if $item.enable_desktop}checked="checked"{/if} id="cb-desktop-{$item.id}">
                                    <label for="cb-desktop-{$item.id}" class="ts-helper"></label>
                                </div>
                            {/if}
                        </td>
                        <td>
                            <div class="tools">
                                <a data-id="{$item.id}" class="tool edit edit-tpl" title="{t}редактировать шаблоны{/t}"><i class="zmdi zmdi-edit"></i></a>
                            </div>
                            <input type="hidden" class="tpl-email" name="template_email[{$item.id}]" data-default="{$tpls.email}" value="{$item.template_email}" size="40" {if !$item->hasEmail()}disabled="disabled"{/if}>
                            <input type="hidden" class="tpl-sms" name="template_sms[{$item.id}]" data-default="{$tpls.sms}" value="{$item.template_sms}"  size="40" {if !$item->hasSms()}disabled="disabled"{/if}>
                            <input type="hidden" class="tpl-desktop" name="template_desktop[{$item.id}]" data-default="{$tpls.desktop}" value="{$item.template_desktop}"  size="40" {if !$item->hasDesktop()}disabled="disabled"{/if}>
                            <input type="hidden" class="add-recipients" name="additional_recipients[{$item.id}]" data-default="" value="{$item.additional_recipients}" size="40">
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </form>
    </div>

    <div id="notice-tpl-dialog" style="display:none" data-dialog-url='{adminUrl mod_controller="templates-selecttemplate" do=false only_themes=0}'>
        <div class="formbox">
            <table class="otable">
                <tbody>
                    <tr>
                        <td class="otitle">{t}Шаблон Email сообщений{/t}:</td>
                        <td>
                            <div class="input-group">
                                <input name="template" value="%THEME%/index.tpl" maxlength="255" size="50" type="text" id="tpl-email">
                                <span class="input-group-addon"><a class="zmdi zmdi-collection-text selectTemplate" title="{t}Выбрать шаблон{/t}"></a></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="otitle">{t}Шаблон SMS сообщений{/t}:</td>
                        <td>
                            <div class="input-group">
                                <input name="template" value="%THEME%/index.tpl" maxlength="255" size="50" type="text" id="tpl-sms">
                                <span class="input-group-addon"><a class="zmdi zmdi-collection-text selectTemplate" title="{t}Выбрать шаблон{/t}"></a></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="otitle">{t}Шаблон ПК сообщений{/t}:</td>
                        <td>
                            <div class="input-group">
                                <input name="template" value="%THEME%/index.tpl" maxlength="255" size="50" type="text" id="tpl-desktop">
                                <span class="input-group-addon"><a class="zmdi zmdi-collection-text selectTemplate" title="{t}Выбрать шаблон{/t}"></a></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="otitle">{t}Дополнительные e-mail получателей{/t}:</td>
                        <td>
                            <div class="input-group">
                                <input name="template" value="" maxlength="255" size="50" type="text" id="add-recipients">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>