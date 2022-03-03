{addjs file="%mobilesiteapp%/view_app.js"}

{$app->autoloadScripsAjaxBefore()}
<div class="mobile-site-app view">
    <h2 class="va-m-c">
        <span>{t alias="Приложение в админ панели - основной заголовок"}Сервис <nobr>ReadyScript Mobile &reg;</nobr>{/t}</span>

        <a href="{adminUrl mod_controller="modcontrol-control" mod="mobilesiteapp" do="edit"}" title="{t}Настройка модуля{/t}" class="btn btn-default mod-config">
            <img src="/resource/img/adminstyle/modoptions.png">
        </a>
    </h2>

    <div class="view-columns">

        <div class="preview">
            <div class="phone"></div>
            <div class="page">
                {if $info.settings}
                    {include file="phonepreview.tpl"}
                {else}
                    <div class="wait"></div>
                {/if}
            </div>
        </div>

        <div class="info">
            <div class="item">
                <p class="caption">{t}Количество заказов, оформленных через мобильное приложение{/t}</p>
                <p class="value">{$order_count}</p>
            </div>
            <div class="item">
                <p class="caption">{t}Статус{/t}</p>
                <p class="value">{$info.app_status_text}</p>
                <p class="comment">{$info.app_status_description}</p>
            </div>
            <div class="item clearfix">
                <div class="pull-left">
                    <p class="caption">{t}Срок окончания подписки{/t}</p>
                    <p class="value">
                        {if $info.date_of_expire}
                            {$info.date_of_expire|dateformat:"@date, @time"}
                        {else}
                            {t}Подписка еще не оформлена{/t}
                        {/if}
                    </p>
                </div>
                <a target="_blank" href="{$app_api->getControlUrl($domain)}" class="pull-right btn btn-default btn-lg btn-alt">{t}Управлять подпиской{/t}</a>
            </div>
            <div class="item">
                <p class="caption">{t}Ссылки на приложения{/t}</p>
                {if $info.url_appstore || $info.url_googleplay}
                    {if $info.url_googleplay}<a target="_blank" href="{$info.url_googleplay}" class="google-play"></a>{/if}
                    {if $info.url_appstore}<a target="_blank" href="{$info.url_appstore}" class="app-store"></a>{/if}
                {else}
                    <p class="value">
                        {t}Приложение еще не опубликовано{/t}
                    </p>
                {/if}
            </div>
            <div class="item tools">
                <p class="caption">{t}Инструменты{/t}</p>
                <a class="btn btn-default btn-alt refresh-app-status"><i class="zmdi zmdi-refresh"></i> {t}Обновить статус подписки{/t}</a>
                {if $info.url_appstore || $info.url_googleplay}
                    <a target="_blank" href="{adminUrl do=false mod_controller="pushsender-pushtokenctrl"}" class="btn btn-warning btn-alt">{t}Отправить Push-уведомления{/t}</a>
                {/if}
            </div>
        </div>
    </div>
</div>
{$app->autoloadScripsAjaxAfter()}