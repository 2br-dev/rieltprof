{addjs file="%marketplace%/marketplace.js"}
{addcss file="%marketplace%/marketplace.css"}
<div id="mp-loader">
    <div class="mp-loading">
        <div class="cssload-container">
            <div class="cssload-loading"><i></i><i></i><i></i><i></i></div>
        </div>
        {t}Маркетплейс{/t}</div>
    <div class="mp-subtext">{t}загрузка...{/t}</div>
</div>
<div id="frame-position">
    <iframe id="frame" width="100%" scrolling="no" src="{$router->getAdminUrl(false, [], 'marketplace-proxy')}" data-proxy-url="{$router->getAdminUrl(false, [], 'marketplace-proxy')}"></iframe>
</div>