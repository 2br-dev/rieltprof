{addcss file="%main%/widget_module_license.css"}
<div class="m-l-20 m-r-20 no-space m-b-20 widget-module-license" data-url="{$router->getAdminUrl('index', ['context' => 'widget'], 'main-widget-modulelicenses')}">
    {if !empty($modules) || !empty($themes)}
        <table class="wtable overable module-license-table">
            <thead>
                <tr>
                    <th>{t}Модуль{/t}</th>
                    <th>{t}Статус{/t}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $modules as $module_name}
                    {$module_config=ConfigLoader::byModule($module_name)}
                    {$license = $licenses.$module_name}
                    {$status = null}
                    {$status_text = $module_license_api->getLicenseDataText($module_name, $status)}

                    {if $license.type != 'none' || !empty($license.error)}
                        <tr>
                            <td class="c-black">{$module_config.name}</td>
                            <td class="module-license-status-column">
                                {if $status == 'information'}
                                    <i class="zmdi zmdi-check c-green"></i>
                                {elseif $status == 'warning'}
                                    <i class="zmdi zmdi-info-outline c-orange"></i>
                                {elseif $status == 'danger'}
                                    <i class="zmdi zmdi-alert-circle-o c-red"></i>
                                {/if}
                                <div>
                                    {$status_text|nl2br}
                                    {if !isset($license.error) && $license.update_expire > 0}
                                        <br><span>
                                            {t}Автопродление{/t}
                                            <a target="_blank" class="c-gray u-link" href="{$my_apps_cabinet_url}">{if $license.auto_prolongation}{t}включено.{/t}{else}{t}выключено.{/t}{/if}</a>
                                        </span>
                                    {/if}
                                </div>
                            </td>
                        </tr>
                    {/if}
                {/foreach}
                {foreach $themes as $theme_name => $theme_title}
                    {$license_key = '#'|cat:$theme_name}
                    {$license = $licenses.$license_key}
                    {$status = null}
                    {$status_text = $module_license_api->getLicenseDataText($license_key, $status)}

                    {if $license.type != 'none' || !empty($license.error)}
                        <tr>
                            <td class="c-black">{t}Тема оформления{/t} "{$theme_title}"</td>
                            <td class="module-license-status-column">
                                {if $status == 'information'}
                                    <i class="zmdi zmdi-check c-green"></i>
                                {elseif $status == 'warning'}
                                    <i class="zmdi zmdi-info-outline c-orange"></i>
                                {elseif $status == 'danger'}
                                    <i class="zmdi zmdi-alert-circle-o c-red"></i>
                                {/if}
                                <div>
                                    {$status_text|nl2br}
                                    {if !isset($license.error) && $license.update_expire > 0}
                                        <br><span>
                                            {t}Автопродление{/t}
                                            <a target="_blank" class="c-gray u-link" href="{$my_apps_cabinet_url}">{if $license.auto_prolongation}{t}включено.{/t}{else}{t}выключено.{/t}{/if}</a>
                                        </span>
                                    {/if}
                                </div>
                            </td>
                        </tr>
                    {/if}
                {/foreach}
            </tbody>
        </table>

        {if $total_in_month}
            <div class="module-license-total">
                {if $shop_type == 'box'}
                    {t price={$total_in_year|format_price}}Общая сумма за автопродление в год - %price ₽{/t}
                {else}
                    {t price={$total_in_month|format_price}}Общая сумма за автопродление в месяц - %price ₽{/t}
                {/if}
            </div>
        {/if}

    {else}
        <div class="empty-widget">{t}Не найдено модулей, требующих лицензии{/t}</div>
    {/if}
</div>