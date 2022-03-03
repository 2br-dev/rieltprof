{t}Вы можете использовать следующие переменные в данном поле:{/t}
<ul>
    {foreach $replace_vars as $var => $title}
        <li>{ldelim}{$var}{rdelim} - {$title}</li>
    {/foreach}
</ul>
