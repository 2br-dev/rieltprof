<div id="import-yml-result" class="crud-form">
    <div data-dialog-options='{ "width":500, "height":500 }'></div>
    {if $next_step === false}
    <br>
    <div class="inform-block">{t}Произошла ошибка:{/t} {$error}</div>
    <br>
    {else}
        <ul class="step-list">
            {foreach $steps as $n => $step}
            <li>{if $next_step.step < $n && $next_step !== true}
                    {$step.title}
                {elseif $next_step.step == $n && $next_step !== true}
                    <b>{$step.title}</b> ({if $next_step.total}{$next_step.offset}/{$next_step.total}{else}{$next_step.percent|default:"0"}%{/if}) <img src="{$Setup.IMG_PATH}/adminstyle/small-loader.gif" class="vertMiddle">
                {else}
                    {$step.successTitle}
                {/if}
            </li>
            {/foreach}
        </ul>
        {if $next_step === true}
            <div class="notice-box">
                <strong>{t}Статистика{/t}</strong><br><br>
                <table>
                    <tr>
                        <td>{t}Добавлено товаров:{/t}</td> 
                        <td align="right">{$statistic.inserted_offers}</td>
                    </tr>
                    <tr>
                        <td>{t}Обновлено товаров:{/t}</td>
                        <td align="right">{$statistic.updated_offers}</td>
                    </tr>
                    <tr>
                        <td>{t}Добавлено категорий:{/t}</td>
                        <td align="right">{$statistic.inserted_categories}</td>
                    </tr>
                    <tr>
                        <td>{t}Обновлено категорий:{/t}</td>
                        <td align="right">{$statistic.updated_categories}</td>
                    </tr>
                    <tr>
                        <td>{t}Добавлено изображений:{/t}</td>
                        <td align="right">{$statistic.inserted_photos}</td>
                    </tr>
                    <tr>
                        <td>{t}Пропущено изображений(были загружены ранее):{/t}</td>
                        <td align="right">{$statistic.already_exists_photos}</td>
                    </tr> 
                     <tr>
                        <td>{t}Не удалось загрузить изображений{/t}:</td>
                        <td align="right">{$statistic.not_downloaded_photos}</td>
                     </tr>
                    <tr>
                        <td>{t}Деактивировано Товаров:{/t}</td>
                        <td align="right">{$statistic.deactivated_products}</td>
                    </tr>
                    <tr>
                        <td>{t}Удалено Товаров:{/t}</td>
                        <td align="right">{$statistic.removed_products}</td>
                    </tr>
                    <tr>
                        <td>{t}Деактивировано Категорий:{/t}</td>
                        <td align="right">{$statistic.deactivated_categories}</td>
                    </tr>
                    <tr>
                        <td>{t}Удалено Категорий:{/t}</td>
                        <td align="right">{$statistic.removed_categories}</td>
                    </tr>
                    <tr>
                        <td>{t}Обнулен остаток у товаров:{/t}</td>
                        <td align="right">{$statistic.cs_products}</td>
                    </tr>
                </table>
            </div>
            <br>
            <div class="link-blog" >
                <span class="main-link"><a class="crud-get crud-close-dialog">{t}Закрыть окно и обновить список товаров{/t}</a></span>
            </div>
        {/if}
        
        {if $next_step !== true}
            <script>
            $.allReady(function() {
                $.ajaxQuery({
                    url: '{adminUrl mod_controller="catalog-importyml" do="ajaxProcess"}',
                    data: { 'step_data': {json_encode($next_step)} },
                    type: 'POST',
                    success: function(response) {
                        $('#import-yml-result')
                            .replaceWith(response.html)
                            .trigger('new-content')
                            .trigger('initContent')
                            .trigger('contentSizeChanged');
                    }
                });
            });
            </script>
        {/if}        
    {/if}
</div>