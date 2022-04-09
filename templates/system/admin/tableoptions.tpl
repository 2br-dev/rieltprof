{* Настройка колонок таблицы *}
{addjs file="jquery.tablednd/jquery.tablednd.js"}
{addjs file="jquery.rs.tableoptions.js"}
<p class="caption-help">{t}Выберите колонки, которые должны отображаться в таблице, а также сортировку по-умолчанию{/t}</p>
<form class="crud-form">
    <table id="tableOptions" data-table-id="{$elements.tableOptionControl->getId()}">
    {foreach from=$elements.tableOptionControl->getTable()->getCustomizableColumns() key=key item=column}
        <tr data-field="{$key}">
            <td class="chk{if !$column->property.hidden} checked{/if}"><input type="checkbox" class="column" value="{$key}" {if !$column->property.hidden}checked{/if}></td>
            <td width="30" align="center" class="drag-handle">
                <a class="sort">
                    <i class="zmdi zmdi-unfold-more"></i>
                </a>
            </td>
            <td>
                {if $column->property['Sortable']}            
                    {if isset($column->property['CurrentSort'])}
                        {if $column->property['CurrentSort'] == $smarty.const.SORTABLE_ASC}
                            {assign var=sortn value="asc"}
                        {else}
                            {assign var=sortn value="desc"}
                        {/if}
                        <input type="hidden" class="current-sort-column" value="{$key}">
                        <input type="hidden" class="current-sort-direction" value="{$column->property['CurrentSort']}">
                    {else}
                        {assign var=sortn value="no"}
                    {/if}
                    <a class="a-underline ch-sort {$sortn}" data-can-be="{$column->property['Sortable']}">
                        <span>{$column->getTitle()}</span>
                        <i class="zmdi"></i>
                    </a>
                {else}
                    <span>{$column->getTitle()}</span>
                {/if}
            </td>
        </tr>
    {/foreach}
    </table>
</form>