<ul class="footer-socials col-xxl-9">
    {if $CONFIG.vkontakte_group}
        <li>
            <a href="{$CONFIG.vkontakte_group}">
                <img src="{$THEME_IMG}/socials/vk.svg" loading="lazy" alt="">
            </a>
        </li>
    {/if}
    {if $CONFIG.whatsapp_group}
        <li>
            <a href="{$CONFIG.whatsapp_group}">
                <img src="{$THEME_IMG}/socials/whatsapp.svg" loading="lazy" alt="">
            </a>
        </li>
    {/if}
    {if $CONFIG.youtube_group}
        <li>
            <a href="{$CONFIG.youtube_group}">
                <img src="{$THEME_IMG}/socials/youtube.svg" loading="lazy" alt="">
            </a>
        </li>
    {/if}
    {if $CONFIG.facebook_group}
        <li>
            <a href="{$CONFIG.facebook_group}">
                <img src="{$THEME_IMG}/socials/facebook.svg" loading="lazy" alt="">
            </a>
        </li>
    {/if}
    {if $CONFIG.instagram_group}
        <li>
            <a href="{$CONFIG.instagram_group}">
                <img src="{$THEME_IMG}/socials/instagram.svg" loading="lazy" alt="">
            </a>
        </li>
    {/if}
    {if $CONFIG.twitter_group}
        <li>
            <a href="{$CONFIG.twitter_group}">
                <img src="{$THEME_IMG}/socials/twitter.svg" loading="lazy" alt="">
            </a>
        </li>
    {/if}
    {if $CONFIG.telegram_group}
        <li>
            <a href="{$CONFIG.telegram_group}">
                <img src="{$THEME_IMG}/socials/telegram.svg" loading="lazy" alt="">
            </a>
        </li>
    {/if}
</ul>