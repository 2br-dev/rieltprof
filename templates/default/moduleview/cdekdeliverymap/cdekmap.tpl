<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
{nocache}
    {addcss file="%cdekdeliverymap%/cdekmap.css"}
    {addjs file="jquery.ui/jquery.autocomplete.js" basepath="common"}
    {addjs file="%cdekdeliverymap%/cdekmap.js"}
    {addjs file="%cdekdeliverymap%/scrollto.js"}
    {addjs file="%cdekdeliverymap%/cdekcityautocomplete.js"}
{/nocache}
<div class="cdekDeliveryMapWrapper">
    <div>
        <h1 class="cdekDeliveryMapTitle" style="display:inline">{t}Пункты выдачи CDEK в{/t} {$city.title}</h1>
        <span>(<a data-url="{$router->getUrl('cdekdeliverymap-front-choosecityautocomplete', ['redirect' => urlencode($smarty.server.REQUEST_URI)])}" data-href="{$router->getUrl('cdekdeliverymap-front-choosecityautocomplete', ['redirect' => urlencode($smarty.server.REQUEST_URI)])}" class="inDialog rs-in-dialog">{t}Выбрать город{/t}</a>)</span>
    </div>
    <div class="cdekDeliveryMapShowContainer">
        <div id="cdekDeliveryMap" class="cdekDeliveryMap" data-options='{ "coor_x":"{$config.map_coor_x}","coor_y":"{$config.map_coor_y}","zoom":"{$config.map_zoom}","city":"{$city.title}"}' data-pochtomates='{$this_controller->api->getPochtomatesJSON()}'>
            {* Сюда будет вставлена карта *}
        </div>
    </div>

    {if !empty($pochtomates)}
        <ul class="cdekDeliveryMapPoints">
            {foreach $pochtomates as $pochtomate}
                <li>
                    <div class="title">
                        {$pochtomate.name}
                    </div>
                    <div class="address">
                        <b>{t}Адрес:{/t}</b> {$pochtomate.address}
                    </div>
                    <div class="worktime">
                        <b>{t}Время работы:{/t}</b> {$pochtomate.worktime}
                    </div>
                    <a data-coors='{ "coor_x":"{$pochtomate.coordX}","coor_y":"{$pochtomate.coordY}"}' class="showOnMap"> {t}Показать на карте{/t}</a>
                </li>
            {/foreach}
        </ul>
    {/if}
</div>