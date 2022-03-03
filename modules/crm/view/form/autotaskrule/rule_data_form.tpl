<tr>
    <td></td>
    <td>{$rule_if_object->getDescription()}
        {$replace_vars = $rule_if_object->getReplaceVarTitles()}
        {if $replace_vars}
        <div class="notice notice-warning m-t-20">
            <p>{t}Переменные, которые можно использовать в названии и описании задач:{/t}</p>
            <ul>
                {foreach $rule_if_object->getReplaceVarTitles() as $var => $title}
                    <li>{ldelim}{$var}{rdelim} - {$title}</li>
                {/foreach}
            </ul>
        </div>
        {/if}
    </td>
</tr>
{$rule_if_object->getFormHtml()}