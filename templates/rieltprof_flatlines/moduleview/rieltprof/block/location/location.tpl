{* Блок, отображающий текущий регион и город *}

{addjs file="searchlocation.js"}
{addjs file="location.js"}
{addcss file="affiliates.css" unshift=true}


    <span class="header-top-city_select">
        <a data-url="{$router->getUrl('rieltprof-front-location', ['referer' => $referrer])}" class="header-top-city_link rs-in-dialog cityLink">
            {if !$current_region}
                <b>Вся Россия</b>
            {else}
                <b>{$current_region['title']}</b>
                {if !$current_city}
                    <b> > Все города</b>
                {else}
                    <b> > {$current_city['title']}</b>
                {/if}
            {/if}
        <b>{$current_affiliate.title}</b>&nbsp;<i class="pe-7s-angle-down-circle pe-lg pe-va"></i></a>
    </span>

