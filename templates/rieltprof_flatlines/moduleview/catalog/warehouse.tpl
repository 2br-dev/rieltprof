{* Страница одного склада *}

{addjs file="//api-maps.yandex.ru/2.1/?lang=ru_RU" basepath="root"}

<div class="page-warehouse warehouse-card" {$warehouse->getDebugAttributes()}>
    <h1 class="h1">{$warehouse.title}</h1>

    <div class="warehouse-card_description">
        {$warehouse.description}
    </div>

    <div class="warehouse-card_info">
        <div class="address">
            <div class="center">
                <i class="icon pe-7s-map-2"></i>
                <p class="title">{t}Адрес{/t}</p>
                <p class="value">{$warehouse.adress}</p>
            </div>
        </div>
        
        <div class="phone">
            <div class="center">
                <i class="icon pe-7s-call"></i>
                <p class="title">{t}Телефон{/t}</p>
                <p class="value"><a href="tel:{$warehouse.phone|format_phone}">{$warehouse.phone}</a></p>
            </div>
        </div>
        
        <div class="worktime">
            <div class="center">
                <i class="icon pe-7s-timer"></i>
                <p class="title">{t}Время работы{/t}</p>        
                <p class="value">{$warehouse.work_time}</p>
            </div>
        </div>
    </div>

    {if $warehouse.image}
        <div class="row">
            <div class="col-md-5 warehouse-card_image">
                <img src="{$warehouse.__image->getUrl(563,400,'cxy')}"/>
            </div>
            <div class="col-md-7">
                <div id="warehouseMap" class="warehouse-card_map">{* карта *}</div>
            </div>
        </div>
    {else}
        <div id="warehouseMap" class="warehouse-card_map">{* карта *}</div>
    {/if}
    <div class="clearfix"></div>
</div>

<script type="text/javascript">
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
</script>