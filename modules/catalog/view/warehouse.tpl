{$main_config = ConfigLoader::byModule('main')}
{$main_config->initMapJs()}

{addjs file="%catalog%/rscomponent/warehouse.js"}

{* Страница одного склада *}
<div {$warehouse->getDebugAttributes()}>
    <div>
        <h1>{$warehouse.title}</h1>
        <div class="mt-5">{$warehouse.description}</div>
        <div class="mt-6">
            <div class="row row-cols-md-3 row-cols-sm-2 g-4">
                <div>
                    <div class="text-gray mb-2">{t}Адрес:{/t}</div>
                    <div class="fs-3">{$warehouse.adress}</div>
                </div>
                <div>
                    <div class="text-gray mb-2">{t}Контактные телефоны:{/t}</div>
                    <ul class="list-unstyled m-0 fs-3">
                        <li>
                            <a class="text-inherit" href="tel:{$warehouse.phone|format_phone}">{$warehouse.phone}</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="text-gray mb-2">{t}Часы работы:{/t}</div>
                    <div class="fs-3">{$warehouse.work_time}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6">
        <h2>{t}Схема проезда{/t}</h2>
        <div class="mt-5">
            {if $warehouse.image}
                <div class="row warehouse-row">
                    <div class="col-lg-5 warehouse-row__image mb-3">
                        <img src="{$warehouse.__image->getUrl(563, 400,'cxy')}"/>
                    </div>
                    <div class="col-lg-7 warehouse-row__map mb-3">
                        <div id="warehouseMap" style="width: 100%; height: 100%;"></div>
                    </div>
                </div>
            {else}
                <div id="warehouseMap" style="width: 100%; height: 400px"></div>
            {/if}
        </div>
    </div>
</div>

<script type="application/json" id="warehouseMapParams">
    {['lat' => $warehouse.coor_x, 'lng' => $warehouse.coor_y, 'zoom' => 12, 'balloon' => "<div><p><b>{$warehouse.title}</b></p>{if !empty($warehouse.adress)}<p>{t}адрес:{/t} {$warehouse.adress}</p>{/if}{if !empty($warehouse.phone)}<p>тел.:{$warehouse.phone}</p>{/if}{if !empty($warehouse.work_time)}<p>время работы: {$warehouse.work_time}</p>{/if}</div>"]|json_encode:320}
</script>


{*<script>
    $(function() {
        ymaps.ready(init); //Инициализация Yandex карты

        function init(){
            //Ставим параметры карты
            myMap = new ymaps.Map("warehouseMap", {
                center: [{$warehouse.coor_x}, {$warehouse.coor_y}],
                zoom: 12
            });

            //Ставим параметры метке
            myPlacemark = new ymaps.Placemark([{$warehouse.coor_x}, {$warehouse.coor_y}], {
                hintContent: '<div><p><b>{$warehouse.title}</b></p>{if !empty($warehouse.adress)}<p>{t}адрес:{/t} {$warehouse.adress}</p>{/if}{if !empty($warehouse.phone)}<p>тел.:{$warehouse.phone}</p>{/if}{if !empty($warehouse.work_time)}<p>время работы: {$warehouse.work_time}</p>{/if}</div>',
                balloonContent: '<div><p><b>{$warehouse.title}</b></p>{if !empty($warehouse.adress)}<p>{t}адрес:{/t} {$warehouse.adress}</p>{/if}{if !empty($warehouse.phone)}<p>тел.:{$warehouse.phone}</p>{/if}{if !empty($warehouse.work_time)}<p>время работы: {$warehouse.work_time}</p>{/if}</div>'
            });
            myMap.behaviors.disable('scrollZoom');
            myMap.geoObjects.add(myPlacemark); //Добавляем метку
        }
    });
</script>*}
