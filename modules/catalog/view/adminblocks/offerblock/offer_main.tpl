{$config = ConfigLoader::byModule('catalog')}
<h3>{t}Основная комплектация{/t}</h3>
<div class="main-offer main-offer-back" {if $config.inventory_control_enable}style="padding: 20px"{/if}>
    <input type="hidden" name="offers[main][id]" value="{$main_offer.id}"/>
    <input name="offers[main][xml_id]" type="hidden" value="{$main_offer.xml_id}">
    <div class="table-mobile-wrapper">
    <table class="offer-table">
        <tbody class="">

        {if $config.inventory_control_enable}
            <p class="label">{t}Название основной комплектации (используйте, если есть дополнительные комплектации){/t}</p>
            <input type="text" class="offers_title" name="offers[main][title]" value="{$main_offer.title}"/><br/>
            <tr class="offer-table-head">
                <td class="no-border"></td>
                <td>{t}Доступно{/t}</td>
                <td>{t}Остаток{/t}</td>
                <td>{t}Резерв{/t}</td>
                <td>{t}Ожидание{/t}</td>
            </tr>
        {foreach $warehouses as $warehouse}
            {$stocks = $main_offer->getStocks()}
            <tr class="offer-table-body">
                <td class="warehouse-title">{$warehouse.title}</td>
                <td>{(float)$stocks[$warehouse.id]['stock']}</td>
                <td>{(float)$stocks[$warehouse.id]['remains']}</td>
                <td>{(float)$stocks[$warehouse.id]['reserve']}</td>
                <td>{(float)$stocks[$warehouse.id]['waiting']}</td>
            </tr>
        {/foreach}
        <tr>
            <td colspan="5">
                {include file="%system%/admin/keyvaleditor.tpl" field_name="offers[main][_propsdata]" arr=$main_offer.propsdata_arr add_button_text=t('Добавить характеристику')}
            </td>
        </tr>
        <tr>
            <td class="images-row" colspan="5">
                {$images=$elem->getImages()}
                <div class="offer-images-line">
                    {if !empty($images)}
                        {foreach $images as $image}
                            {$is_act=is_array($main_offer.photos_arr) && in_array($image.id, $main_offer.photos_arr)}
                            <a data-id="{$image.id}" data-name="offers[main][photos_arr][]" class="{if $is_act}act{/if}"><img src="{$image->getUrl(30,30,'xy')}"/></a>
                            {if $is_act}<input type="hidden" name="offers[main][photos_arr][]" value="{$image.id}">{/if}
                        {/foreach}
                    {/if}
                </div>
            </td >
        </tr>
        {else}
            <tr>
               <td class="td title-td col2" rowspan="2">
                    <p class="label">{t}Название основной комплектации (используйте, если есть дополнительные комплектации){/t}</p>
                    <input type="text" class="offers_title" name="offers[main][title]" value="{$main_offer.title}"/><br/>
                    {foreach $warehouses as $warehouse}
                        <p class="label">"{$warehouse.title}" - {t}остаток{/t}</p>
                        <input name="offers[main][stock_num][{$warehouse.id}]" type="text" value="{$main_offer.stock_num[$warehouse.id]}"/><br/>
                    {/foreach}

                    {$other_fields_form}
                </td>
                <td class="td keyval-td col3">
                    {include file="%system%/admin/keyvaleditor.tpl" field_name="offers[main][_propsdata]" arr=$main_offer.propsdata_arr add_button_text=t('Добавить характеристику')}
                </td>
            </tr>
            <tr>
                <td class="images-row" style="padding-left: 20px">
                   {$images=$elem->getImages()}
                      <div class="offer-images-line">
                      {if !empty($images)}
                          {foreach $images as $image}
                             {$is_act=is_array($main_offer.photos_arr) && in_array($image.id, $main_offer.photos_arr)}
                             <a data-id="{$image.id}" data-name="offers[main][photos_arr][]" class="{if $is_act}act{/if}"><img src="{$image->getUrl(30,30,'xy')}"/></a>
                             {if $is_act}<input type="hidden" name="offers[main][photos_arr][]" value="{$image.id}">{/if}
                          {/foreach}
                      {/if}
                      </div>
                </td>
            </tr>
        {/if}
        </tbody>
    </table>
    </div>
</div>