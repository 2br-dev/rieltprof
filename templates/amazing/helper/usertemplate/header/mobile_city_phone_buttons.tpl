{$is_module_affiliate_exists = ModuleManager::staticModuleExists('affiliate')}
<div class="ms-4 d-lg-none d-flex">
    {if $is_module_affiliate_exists}
        {modulegetvars name="\Affiliate\Controller\Block\ShortInfo" var="affiliate_data"}
    {/if}
    {$phone = $affiliate_data.current_affiliate.short_contacts|default:$THEME_SETTINGS.default_phone}
    <a class="head-mob-link" href="tel:{$phone|format_phone}">
        <img src="{$THEME_IMG}/icons/phone-head.svg" alt="{t}Позвонить{/t}">
    </a>
    {if $is_module_affiliate_exists}
        <a class="ms-3 head-mob-link rs-in-dialog" data-href="{$router->getUrl('affiliate-front-affiliates', ['referer' => $referrer])}">
            <img src="{$THEME_IMG}/icons/location.svg" alt="{t}Выбрать город{/t}">
        </a>
    {/if}
</div>