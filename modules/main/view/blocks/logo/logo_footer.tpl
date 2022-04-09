{if $site_config.logo}
    <div class="text-center d-inline-block mb-6">
        <a href="{if $link != ' '}{$link}{else}#{/if}">
            <img class="logo {if !$site_config.slogan}logo_without-desc{/if}" src="{$site_config.__logo->getUrl($width, $height)}" loading="lazy" alt="">
        </a>
        {if $site_config.slogan}
            <div class="logo-desc text-white">{$site_config.slogan}</div>
        {/if}
    </div>
{else}
    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Логотип{/t}"
    skeleton = 'skeleton-footer-logo.svg'
    do = [
        [
            'title' => "{t}Добавить логотип{/t}",
            'href' => "{adminUrl do=false mod_controller="site-options"}"
        ]
    ]}
{/if}