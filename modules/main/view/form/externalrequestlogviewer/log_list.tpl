{if empty($links)}
    <p>{t}Файлы логов отсутствуют{/t}</p>
{else}
    <table class="rs-table">
        {foreach $links as $name => $link}
            <tr>
                <td>
                    <a href="{$link}" target="_blank"><div>{$name}</div></a>
                </td>
            </tr>
        {/foreach}
    </table>
{/if}