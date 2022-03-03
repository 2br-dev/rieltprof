<div class="notice m-b-20 cache_info">
    {t alias="Информация о закладке кэш"}Клик по любой из представленных ниже ссылок не будет фатальным для системы.
    Удаление файлов из кэша лишь будет означать, что эти файлы нужно создать заново. 
    Система может работать медленне, пока кэш не будет пострен заново.
    Кэш будет строиться помере посещения страниц сайта (в том числе и административной панели).{/t}
</div>

<ul class="cache_links">
    <li><a href="JavaScript:;" data-ctype="common">{t}Очистить общий кэш (будет очищен также и кэш всех модулей){/t}</a>
        <span class="success hidden zmdi zmdi-check"></span>
    </li>
    <li><a href="JavaScript:;" data-ctype="min">{t}Удалить объединенные и минимизированные файлы CSS и JS{/t}</a>
        <span class="success hidden zmdi zmdi-check"></span>
    </li>
    <li><a href="JavaScript:;" data-ctype="tplcompile">{t}Удалить скомпилированные шаблоны Smarty{/t}</a>
        <span class="success hidden zmdi zmdi-check"></span>
    </li>
    <li><a href="JavaScript:;" data-ctype="autotpl">{t}Удалить автоматически сгенерированные шаблоны форм в админ. панели{/t}</a>
        <span class="success hidden zmdi zmdi-check"></span>
    </li>    
</ul>