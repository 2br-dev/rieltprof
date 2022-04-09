{if !empty($word_list)}
    <ul class="taglist">
        {foreach $word_list as $item}
        <li><div class="padd">{$item.word|escape} <a class="tagdel zmdi zmdi-close" data-lid="{$item.lid}" title="{t}Удалить{/t}"></a></div></li>
        {/foreach}
    </ul>
{else}
    <div class="notags">
        {t}Не добавлено ни одного тега{/t}
    </div>
{/if}
        