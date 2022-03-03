{$app->autoloadScripsAjaxBefore()}
{addjs file="https://api-maps.yandex.ru/2.1/?lang=ru_RU" basepath="root"}
{addjs file="%main%/yandexmap/yandexmap.js"}
<div id="{$mapId}" style="height:{$height}px;{if $width}width:{$width}px;{/if}" data-points='{$this_controller->getPointsJSON()}' data-zoom="{$zoom}" data-block_mouse_zoom="{$block_mouse_zoom}" data-auto_init="{$auto_init}">
</div>
<script type="text/javascript">
    $(document).ready(function() {
        if ($("#{$mapId}").data('auto_init')) {
            $("#{$mapId}").initYandexMap({
                zoom             : $(this).data('zoom'),                //Масштаб карты
                block_mouse_zoom : $(this).data('block_mouse_zoom'),    //Блокировать изменение колесом мышки масштаба
            });
        }
    });
</script>
{$app->autoloadScripsAjaxAfter()}