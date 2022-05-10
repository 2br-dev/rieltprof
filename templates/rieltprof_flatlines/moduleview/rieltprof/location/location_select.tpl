<div class="location_select_wrapper modal-wrapper">
    <h2>Выбирите регион или город</h2>
    <div class="location_select locations">
        <div class="query">
            <input type="text" placeholder="Поиск по названию" class="fastSearch">
        </div>
        <div class="location-columns">
            <ul class="tree">
                {foreach $location as $region}
                    <li>
                        <a class="city" data-href="{$router->getUrl('affiliate-front-change', ['affiliate' => {$item.fields.alias|default:$item.fields.id}, 'referer' => $referer])}">{$region['title']}</a>
                        <ul>
                            {foreach $region['cities'] as $city}
                                <li><a class="city">{$city['title']}</a></li>
                            {/foreach}
                        </ul>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>

