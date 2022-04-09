<ul class="footer-contacts">
    {if $THEME_SETTINGS.default_phone}
        <li>
            <a href="tel:{$THEME_SETTINGS.default_phone|format_phone}">
                <span class="footer-contacts__icon">
                    <img src="{$THEME_IMG}/icons/phone.svg" width="20" height="20" loading="lazy" alt="">
                </span>
                <span class="ms-2">{$THEME_SETTINGS.default_phone}</span>
            </a>
        </li>
    {/if}
    {if $THEME_SETTINGS.default_email}
        <li>
            <a href="mailto:{$THEME_SETTINGS.default_email}">
                <span class="footer-contacts__icon">
                    <img src="{$THEME_IMG}/icons/mail.svg" width="20" height="20" loading="lazy" alt="">
                </span>
                <span class="ms-2">{$THEME_SETTINGS.default_email}</span>
            </a>
        </li>
    {/if}
    {if $THEME_SETTINGS.default_address}
        <li>
            <span class="footer-contacts__icon">
                <img src="{$THEME_IMG}/icons/location.svg" width="20" height="20" loading="lazy" alt="">
            </span>
            <span class="ms-2">{$THEME_SETTINGS.default_address}</span>
        </li>
    {/if}
</ul>