<div id="import-photo-result" class="crud-form" data-dialog-options='{ "width":"700", "height":"600" }'>
    {if $error}
    <br>
    <div class="inform-block">{t}Произошла ошибка:{/t} {$error}</div>
    {else}
        <ul class="step-list">
            <li>{if $step==1}<strong>{/if}{t}Zip архив с изображениями загружен{/t}{if $step==1}</strong>{/if}</li>
            <li>{if $step<2}{t}Распаковка архива{/t}{elseif $step == 2}<strong>{t}Идет распаковка...{/t} ({$info.zip_done_percent}%) <img src="{$Setup.IMG_PATH}/adminstyle/small-loader.gif" class="vertMiddle"></strong>{else}{t}Архив распакован. Файлов и папок:{/t} {$info.zip_num_files}{/if}</li>
            <li>{if $step<3}{t}Импорт фотографий{/t}{elseif $step ==3}<strong>{t}Идет импорт фотографий...{/t} ({$info.import_done_percent|default:"0"}%) <img src="{$Setup.IMG_PATH}/adminstyle/small-loader.gif" class="vertMiddle"></strong>{else}{t}Изображения импортированы{/t}{/if}</li>
        </ul>
        {if $step == 4}
        <div class="inform-block no-vert-padd">
            <h3>{t}Статистика{/t}</h3>
            <p>{t}Обработано изображений:{/t} <strong>{$info.statistic.touch_images}</strong><br>
            {t}Импортировано фотографий:{/t} <strong>{$info.statistic.images_imported}</strong><br>
            {t}Изображения, к которым не нашлось товаров:{/t} <strong>{$info.statistic.no_match_images}</strong><br>
            {t}Обновлено товаров:{/t} <strong>{$info.statistic.touch_products}</strong></p>
        </div>
        {/if}
        {if $next_url}
        <script>
        $.allReady(function() {
            $.ajaxQuery({
                url: '{$next_url}',
                data: {$params},
                type: 'POST',
                success: function(response) {
                    $('.dialog-window')
                        .html(response.html)
                        .trigger('new-content')
                        .trigger('initContent')
                        .trigger('contentSizeChanged');
                }
            });
        });
        </script>
        {/if}        
    {/if}
        <h4>{t}Выберите следующее действие{/t}</h4>
        <div class="link-blog">
            <a href="{$log_url}" target="_blank">{t}Открыть отчет импорта данных{/t}</a><br>
            <a href="{adminUrl do=false mod_controller="catalog-importphotos"}" class="crud-add crud-replace-dialog">{t}Повторить импорт с другими условиями{/t}</a><br>
            <br><span class="main-link"><a href="{adminUrl do="cleanTmp" mod_controller="catalog-importphotos"}" class="crud-get crud-close-dialog">{t}Удалить временные файлы и закрыть окно{/t}</a></span>
        </div>        
</div>