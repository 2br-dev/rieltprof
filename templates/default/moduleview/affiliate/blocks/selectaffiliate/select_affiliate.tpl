{addjs file="%affiliate%/modernizr-columns.js"}
{addjs file="%affiliate%/searchfiiliates.js"}
{addjs file="%affiliate%/affiliate.js"}
{addcss file="%affiliate%/affiliates.css" unshift=true}
{if $current_affiliate.id}
    <span class="citySelect">
        <span class="cityIcon"></span>
        <a data-href="{$router->getUrl('affiliate-front-affiliates', ['referer' => $referrer])}" class="cityLink inDialog" data-need-recheck="{$need_recheck}" {$current_affiliate->getDebugAttributes()}>
            {$current_affiliate.title}
        </a>
    </span>
{/if}