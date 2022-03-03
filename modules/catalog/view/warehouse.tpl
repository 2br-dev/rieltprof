{addJS file="//api-maps.yandex.ru/2.1/?lang=ru_RU" basepath="root"}
<div class="warehouseCard">
    <h1>{$warehouse.title}</h1>
    <div class="warehouseDesc">
        {if $warehouse.image}
            <img class="mainImage" src="{$warehouse.__image->getUrl(200,200,'axy')}"/>
        {/if}
        {$warehouse.description}
    </div>
    <div class="warehouseInfo">
        <div class="address">
            <div class="center">
                <i class="icon"></i>
                <p class="title">{t}Адрес{/t}</p>
                <p class="value">{$warehouse.adress}</p>
            </div>
        </div>
        
        <div class="phone">
            <div class="center">
                <i class="icon"></i>
                <p class="title">{t}Телефон{/t}</p>
                <p class="value">{$warehouse.phone}</p>
            </div>
        </div>
        
        <div class="worktime">
            <div class="center">
                <i class="icon"></i>
                <p class="title">{t}Время работы{/t}</p>        
                <p class="value">{$warehouse.work_time}</p>
            </div>
        </div>
    </div>
    <div id="warehouseMap" class="warehouseMap">
        {* карта *}
    </div>
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
                hintContent: '<div><p><b>{$warehouse.title}</b></p>{if !empty($warehouse.adress)}<p>{t}адрес:{/t} {$warehouse.adress}</p>{/if}{if !empty($warehouse.phone)}<p>{t}тел.{/t}:{$warehouse.phone}</p>{/if}{if !empty($warehouse.work_time)}<p>{t}время работы{/t}: {$warehouse.work_time}</p>{/if}</div>',
                balloonContent: '<div><p><b>{$warehouse.title}</b></p>{if !empty($warehouse.adress)}<p>{t}адрес:{/t} {$warehouse.adress}</p>{/if}{if !empty($warehouse.phone)}<p>{t}тел.{/t}:{$warehouse.phone}</p>{/if}{if !empty($warehouse.work_time)}<p>{t}время работы{/t}: {$warehouse.work_time}</p>{/if}</div>'
            });
            
            myMap.geoObjects.add(myPlacemark); //Добавляем метку
        }  
   });
</script>