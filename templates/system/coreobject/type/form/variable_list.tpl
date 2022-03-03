{addjs file="jquery.tablednd/jquery.tablednd.js" basepath="common"}
{addjs file="jquery.rs.variablelist.js" basepath="common"}

{$values = $elem[$field->name]|default:[]}

<div class="table-responsive variable-list">
    <table class="rs-table variable-list_table" style="width:auto;">
        <thead>
            <th></th>
            {foreach $field->getTableFields() as $elem}
                <th>{$elem->getColumnTitle()}</th>
            {/foreach}
            <th></th>
        </thead>
        <tbody>
            <tr class="new-line hidden">
                <td class="drag drag-handle"><a class="sort vl-dndsort"><i class="zmdi zmdi-unfold-more"></i></a></td>
                {foreach $field->getTableFields() as $elem}
                    <td>
                        {$elem->getElementHtml($field->name)}
                    </td>
                {/foreach}
                <td><i class="delete-row zmdi zmdi-delete f-22 c-red"></i></td>
            </tr>
            {if !empty($values)}
                {foreach $values as $row_value}
                    <tr>
                        <td class="drag drag-handle"><a class="sort vl-dndsort"><i class="zmdi zmdi-unfold-more"></i></a></td>
                        {foreach $field->getTableFields() as $elem}
                            {$value = null}
                            {if isset($row_value[$elem->getName()])}
                                {$value = $row_value[$elem->getName()]}
                            {/if}
                            <td>
                                {$elem->getElementHtml($field->name, $row_value@iteration-1, $value)}
                            </td>
                        {/foreach}
                        <td><i class="delete-row zmdi zmdi-delete f-22 c-red"></i></td>
                    </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
    <div class="button add-line m-t-5">{t}Добавить{/t}</div>
</div>