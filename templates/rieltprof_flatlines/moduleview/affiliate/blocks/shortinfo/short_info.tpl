{* Блок, отображающий краткую контактную информацию (Номер телефона в филиале) *}

{if $current_affiliate.short_contacts}
    <a href="tel:{$current_affiliate.short_contacts|format_phone}" class="header-top-city_phone">{nl2br($current_affiliate.short_contacts)}</a>
{/if}