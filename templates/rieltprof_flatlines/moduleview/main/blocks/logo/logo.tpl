{* Логотип в шапке *}

{if $site_config.logo}
    <div class="logo">
        {if $link != ' '}<a href="{$link}">{/if}
        <img src="{$site_config.__logo->getUrl($width, $height)}" alt=""/>
        {if $link != ' '}</a>{/if}
        <div class="slogan">{$site_config.slogan}</div>
                   </div>
{else}
    {include file="%THEME%/block_stub.tpl"  class="block-logo" do=[
        [
            'title' => t("Добавьте логотип"),
            'href' => {adminUrl do=false mod_controller="site-options"}
        ]
    ]}
{/if}