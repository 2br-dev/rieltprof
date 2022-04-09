{if $site_config.logo}
    <div class="text-center">
        <a href="{if $link != ' '}{$link}{else}#{/if}">
            <picture>
                {if $site_config.logo_sm}
                    <source srcset="{$site_config.__logo_sm->getUrl(53, 32)}" media="(max-width: 576px)">
                {/if}

                <img src="{$site_config.__logo->getUrl($width, $height)}"
                     {if $site_config.__logo->getExtension() != 'svg'}
                        srcset="{$site_config.__logo->getUrl($width*2, $height*2)} 2x"
                     {/if}
                     alt="{$SITE.title}"
                     class="logo logo_checkout {if !$site_config.slogan}logo_without-desc{/if}">
            </picture>
        </a>
        {if $site_config.slogan}
            <div class="logo-desc d-none d-sm-block">{$site_config.slogan}</div>
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