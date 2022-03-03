{$multioffer_help_url='http://readyscript.ru/manual/catalog_products.html#catalog_multioffers'}

{if !empty($all_props)}
    <div id="multioffer-wrap">
        <div class="multioffer-warning">
            {t}&quot;Многомерные комплектации&quot; недоступны, т.к. у товара не добавлены или не отмеченны списковые характеристики.{/t} <a href="{$multioffer_help_url}" target="_blank" class="how-to">{t}Подробнее...{/t}</a>
        </div>
        <div id="multi-check-wrap">
            <input type="checkbox" id="use-multioffer" name="multioffers[use]" value="1" {if $elem->isMultiOffersUse()}checked{/if}> 
            <label for="use-multioffer">{t}Использовать многомерные комплектации{/t}. <span><a href="{$multioffer_help_url}" target="_blank" class="how-to">{t}Как использовать?{/t}</a></span></label>
        </div>

        <div class="multioffer-wrap">
            <div class="item">
                <table class="multioffer-table">
                    <tbody>
                        <tr>
                            <td class="is_photo">
                                <label><input type="radio" name="multioffers[is_photo]" value="0" checked="checked"/> <span>{t}без фото{/t}</span></label>
                            </td>
                            <td class="key">
                                {t}Название параметра комплектации{/t}:
                            </td>
                            <td class="value">
                                {t}Списковые характеристики{/t}:
                            </td>
                            <td class="delete-level-td"></td>
                        </tr>
                    </tbody>
                    <tbody class="offers-body">
                        {if $elem->isMultiOffersUse()}
                            {$m=0}
                            {foreach $elem.multioffers.levels as $k=>$level}
                                <tr class="line">
                                    <td class="is_photo">
                                        <label><input type="radio" name="multioffers[is_photo]" value="{$m+1}" {if $level.is_photo}checked="checked"{/if}/> <span>{t}фото{/t}</span></label>
                                    </td>
                                    <td class="key">
                                       <input type="text" name="multioffers[levels][{$m}][title]" maxlength="255" value="{$level.title}"/> 
                                    </td>
                                    <td class="value">
                                        {html_options name="multioffers[levels][{$m}][prop]" options=$all_props selected=$level.prop_id}
                                    </td>
                                    <td class="delete-level-td">
                                        <a href="#" class="delete-level zmdi zmdi-close c-red" title="{t}удалить{/t}"></a>
                                    </td>
                                </tr>
                                {$m = $m+1}
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
            <div class="add-wrap">
                <div class="keyval-container" data-id=".multioffer-wrap .row">
                    <a class="btn btn-default va-m-c add-level" href="javascript:;">
                        <i class="zmdi zmdi-plus f-21 m-r-5"></i>
                        <span>{t}добавить параметр{/t}</span>
                    </a>
                </div>
                <div>
                   <input type="checkbox" id="create-auto-offers" name="offers[create_autooffers]" value="1" > 
                   <label for="create-auto-offers">{t}Создавать комплектации{/t} <a class="help-icon"
                    title="{t}Установите данный флаг, если есть необходимость изменения цены или количества товара для разных комплектаций{/t}">?</a></label> 
                </div>
                <div class="bottom-bar">
                    <input class="btn btn-default create-complexs" type="button" name="" value="{t}создать{/t}"/>
                </div>
            </div>
        </div>

        <script type="text/x-tmpl" id="multioffer-line">
            <tr class="line">
                <td class="is_photo">
                    <label><input type="radio" name="multioffers[is_photo]" value="0"/> <span>{t}фото{/t}</span></label>
                </td>
                <td class="key">
                   <input type="text" name="multioffers[levels][0][title]" maxlength="255"/> 
                </td>
                <td class="value">
                   {html_options name="multioffers[levels][0][prop]" options=$all_props data-prop-id="0"}
                </td>
                <td class="delete-level-td">
                    <a href="#" class="delete-level zmdi zmdi-close c-red" title="{t}удалить{/t}"></a>
                </td>
            </tr>
        </script>
    </div>
{/if}