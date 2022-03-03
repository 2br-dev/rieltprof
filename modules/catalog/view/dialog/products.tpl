{if count($list)>0}
<div class="paginator before-table">
    <a gopage="{if $paginator.page>1}{$paginator.page-1}{else}1{/if}" class="pag_left prev zmdi zmdi-chevron-left" title="{t}предыдущая страница{/t}"></a>
    <input class="page pag_page" value="{$paginator.page}" onfocus="$(this).select()" type="text">
    <a gopage="{if $paginator.page<$paginator.totalPages}{$paginator.page+1}{else}{$paginator.totalPages}{/if}" class="pag_right next zmdi zmdi-chevron-right" title="{t}следующая страница{/t}"></a>
    <span class="text">{t}из{/t} {$paginator.totalPages}</span>
    <span class="text perpage_block">{t}по{/t} </span>
    <input type="text" class="perpage pag_pagesize" value="{$paginator.pageSize}" size="3">
    <button type="button" class="pag_submit btn btn-default"><i class="zmdi zmdi-check visible-xs"></i> <span class="hidden-xs">Применить</span></button>
    <span class="total">{t}всего записей:{/t} <span class="total_value">{$paginator.total}</span></span>
</div>
{/if}
{if count($list)>0}
<table class="product-list-table">
<thead>
    <tr>
        {if !$hideProductCheckbox}
        <th class="chk"><input type="checkbox" name="select-all"></th>
        {/if}
        <th></th>
        <th>{t}Название{/t}</th>
        <th>№</th>
        <th class="textright">{t}Артикул{/t}</th>
        <th class="textright">{t}Штрихкод{/t}</th>
    </tr>
</thead>
<tbody class="product-list">
{foreach from=$list item=item}
<tr data-id="{$item.id}">
    {if !$hideProductCheckbox}
    <td class="chk">
        <input type="checkbox" value="{$item.id}"
               data-barcode="{$item.barcode}"
               data-image="{$item->getMainImage()->getUrl(30, 30)}"
               data-preview-url="{$item->getMainImage()->getUrl(200, 200)}"
               data-weight="{$item.weight}"
               data-catids="{foreach from=$products_dirs[$item.id] item=cat},{$cat}{/foreach},">
    </td>
    {/if}
    <td class="image"><img src="{$item->getMainImage()->getUrl(30, 30)}" alt=""/></td>
    <td class="title">{$item.title}</td>
    <td class="no">{$item.id}</td>
    <td class="barcode" align="right">{$item.barcode}</td>
    <td class="barcode" align="right">{$item.sku}</td>
</tr>
{/foreach}
</tbody>
</table>
{else}
<br><br><br><br><br><br><br><br>
<table width="100%">
<tr>
    <td align="center" class="no-goods">{t}Нет товаров{/t}</td>
</tr>
</table>
{/if}
{if count($list)>0}
    <div class="paginator before-table">
        <a gopage="{if $paginator.page>1}{$paginator.page-1}{else}1{/if}" class="pag_left prev zmdi zmdi-chevron-left" title="{t}предыдущая страница{/t}"></a>
        <input class="page pag_page" value="{$paginator.page}" onfocus="$(this).select()" type="text">
        <a gopage="{if $paginator.page<$paginator.totalPages}{$paginator.page+1}{else}{$paginator.totalPages}{/if}" class="pag_right next zmdi zmdi-chevron-right" title="{t}следующая страница{/t}"></a>
        <span class="text">{t}из{/t} {$paginator.totalPages}</span>
        <span class="text perpage_block">{t}по{/t} </span>
        <input type="text" class="perpage pag_pagesize" value="{$paginator.pageSize}" size="3">
        <button type="button" class="pag_submit btn btn-default"><i class="zmdi zmdi-check visible-xs"></i> <span class="hidden-xs">Применить</span></button>
        <span class="total">{t}всего записей:{/t} <span class="total_value">{$paginator.total}</span></span>
    </div>
{/if}

{* Блок, который будет перенесен в полоску кнопок внизу диалога *}
<div class="hidden">
    <div class="to-dialog-buttonpane cost-pane">
        {t}Тип цены:{/t}
        <select name="costtype">
        {foreach from=$costtypes item=cost}
            <option value="{$cost->id}">{$cost->title}</option>
        {/foreach}
        </select>
    </div>
</div>
