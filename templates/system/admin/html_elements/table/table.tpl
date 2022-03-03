<div class="table-mobile-wrapper">
    <table border="0" {$table->getTableAttr()}>
        <thead>
        <tr>
            <th class="l-w-space"></th>
            {foreach $table->getColumns() as $col => $item}
                {if !$item->property.hidden}
                <th {$item->getThAttr()}>{include file=$item->getHeadTemplate() cell=$item}</th>
                {/if}
            {/foreach}
            <th class="r-w-space"></th>
        </tr>
        </thead>
        <tbody>
            {* Вставляем anyrows подряд, если нет записей в таблице *}
                {if isset($anyrows.0) && empty($rows)}
                <tr {$table->getAnyRowAttr($rownum)}>
                    <td class="l-w-space"></td>
                    {foreach $anyrows.0 as $anycell}
                    <td {$anycell->getTdAttr()}>
                        {if isset($anycell->property.href)}<a href="{$anycell->getHref()}" {$anycell->getLinkAttr()}>{/if}
                            {include file=$anycell->getBodyTemplate() cell=$anycell}
                        {if isset($anycell->property.href)}</a>{/if}
                    </td>
                    {/foreach}
                    <td class="r-w-space"></td>
                </tr>
                {/if}
            {* Вставляем записи в обычном порядке *}
            {foreach $rows as $rownum => $row}
            {if isset($anyrows.$rownum)}
            <tr {$table->getAnyRowAttr($rownum)}>
                <td class="l-w-space"></td>
                {foreach from=$anyrows.$rownum item=anycell}
                <td {$anycell->getTdAttr()}>
                    {if isset($anycell->property.href)}<a href="{$anycell->getHref()}" {$anycell->getLinkAttr()}>{/if}
                        {include file=$anycell->getBodyTemplate() cell=$anycell}
                    {if isset($anycell->property.href)}</a>{/if}
                </td>
                {/foreach}
                <td class="r-w-space"></td>
            </tr>
            {/if}
            <tr {$table->getRowAttr($rownum)}>
                <td class="l-w-space"></td>
                {foreach $row as $col => $cell}
                <td {$cell->getTdAttr()}>
                    {if isset($cell->property.href)}<a href="{$cell->getHref()}" {$cell->getLinkAttr()}>{/if}
                        {include file=$cell->getBodyTemplate() cell=$cell}
                    {if isset($cell->property.href)}</a>{/if}
                </td>
                {/foreach}
                <td class="r-w-space"></td>
            </tr>
            {/foreach}
            {if empty($anyrows) && empty($rows)}
            <tr>
                {$count=count($table->getColumns())}
                <td class="l-w-space"></td>
                <td colspan="{$count}" align="center"> {t}Нет элементов{/t} </td>
                <td class="r-w-space"></td>
            </tr>
            {/if}
        </tbody>
    </table>
</div>