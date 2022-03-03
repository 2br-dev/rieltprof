{$all_props = $field->callPropertyFunction('getAllProperties')}
{$multioffer_help_url='http://readyscript.ru/manual/catalog_products.html#catalog_multioffers'}
<div>
    <input id="deleteMe-offers" type="checkbox" name="_offers_[delete]" value="1"/>
    <label for="deleteMe-offers">{t}Удалить имеющиеся комплектации{/t}</label>
</div>
{if !empty($all_props)}
    <div>

        <div id="multi-check-wrapME">
            <input type="hidden" name="_offers_[use]" value="0"> 
            <input type="checkbox" id="use-multiofferME" name="_offers_[use]" value="1"> 
            <label for="use-multiofferME">{t}Использовать многомерные комплектации{/t}. <span>
            <a href="{$multioffer_help_url}" target="_blank" class="how-to">{t}Как использовать?{/t}</a></span></label>
        </div>

        <div class="multioffer-wrapME">
            <div class="item">
                <table class="multioffer-table">
                    <tbody>
                        <tr>
                            <td class="is_photo">
                                <input type="radio" name="_offers_[is_photo]" value="0" checked="checked"/> <span>{t}без фото{/t}</span>
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
                    <tbody class="offers-bodyME">
                    </tbody>
                </table>
            </div>
            <div class="add-wrap">
                <div class="keyval-container" data-id=".multioffer-wrapME .lineME">
                    <a class="btn btn-default va-m-c add-levelME" href="javascript:;">
                        <i class="zmdi zmdi-plus f-21 m-r-5"></i>
                        <span>{t}добавить параметр{/t}</span>
                    </a>
                </div>
                <div>
                   <input type="checkbox" id="create-auto-offersME" name="_offers_[create_autooffers]" value="1" > 
                   <label for="create-auto-offersME">{t}Создавать комплектации{/t} <a class="help-icon"
                    title="{t}Установите данный флаг, если есть необходимость изменения цены<br/> или количества товара для разных комплектаций{/t}">?</a></label> 
                </div>
                <div class="bottom-bar">
                    <input class="btn btn-default create-complexs" type="button" name="" value="{t}cоздать{/t}"/>
                </div>
            </div>
        </div>

        {literal}
        <script type="text/x-tmpl" id="multioffer-lineme">
            <tr class="lineME">
                <td class="is_photo">
                    <label><input type="radio" name="_offers_[is_photo]" value="1"/> <span>{/literal}{t}фото{/t}{literal}</span></label>
                </td>
                <td class="key">
                   <input type="text" name="_offers_[levels][0][title]" maxlength="255"/> 
                </td>
                <td class="value">
                   {/literal}
                  
                    {html_options name="_offers_[levels][0][prop]" options=$all_props data-prop-id="0"}
                   
                   {literal}
                </td>
                <td class="delete-level-td">
                    <a href="#" class="delete-levelME zmdi zmdi-close c-red" title="{/literal}{t}удалить{/t}{literal}"></a>
                </td>
            </tr>
        </script>
        {/literal}
    </div>
{/if}