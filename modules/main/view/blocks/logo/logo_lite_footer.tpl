{if $site_config.logo}
    <img class="logo {if !$site_config.slogan}logo_without-desc{/if}"
            src="{$site_config.__logo->getUrl($width, $height)}" loading="lazy" alt=""
            {if $site_config.__logo->getExtension() != 'svg'}
                srcset="{$site_config.__logo->getUrl($width*2, $height*2)} 2x"
            {/if}>
{/if}
{if $site_config.slogan}
    <div class="logo-desc logo-desc_footer">{$site_config.slogan}</div>
{/if}