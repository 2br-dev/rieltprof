{if $site_config.logo}
    <div class="text-center">
        <a href="{if $link != ' '}{$link}{else}#{/if}">
            <img class="logo {if !$site_config.slogan}logo_without-desc{/if}"
                 src="{$site_config.__logo->getUrl($width, $height)}" alt="{$SITE.title}"
                    {if $site_config.__logo->getExtension() != 'svg'}
                        srcset="{$site_config.__logo->getUrl($width*2, $height*2)} 2x"
                    {/if}>
        </a>
        {if $site_config.slogan}
            <div class="logo-desc">{$site_config.slogan}</div>
        {/if}
    </div>
{else}
    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
        name = "{t}Логотип{/t}"
        skeleton = "skeleton-logo.svg"
        do = [
            [
            'title' => "{t}Добавить логотип{/t}",
            'href' => "{adminUrl do=false mod_controller="site-options"}"
            ]
        ]}
{/if}