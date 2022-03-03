{* Блок, отображающий текущий город *}

{addjs file="%affiliate%/searchfiiliates.js"}
{addjs file="%affiliate%/affiliate.js"}
{addcss file="%affiliate%/affiliates.css" unshift=true}

{if $current_affiliate.id}
    <span class="header-top-city_select">
        <span>{t}Ваш город{/t}:</span>
        <a data-url="{$router->getUrl('affiliate-front-affiliates', ['referer' => $referrer])}" class="header-top-city_link rs-in-dialog cityLink" data-need-recheck="{$need_recheck}" {$current_affiliate->getDebugAttributes()}>
        <b>{$current_affiliate.title}</b>&nbsp;<i class="pe-7s-angle-down-circle pe-lg pe-va"></i></a>
    </span>
{/if}