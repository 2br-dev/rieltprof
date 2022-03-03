{if $Setup.DETAILED_EXCEPTION}
    {addcss file="user/errorblocks.css" basepath="common"}
    <div class="comError">
        <div>
            <strong>{$exception->getMessage()}</strong><br>
            {t}Ошибка в контроллере:{/t} {$controllerName}<br>
            <a href="JavaScript:;" onclick="document.getElementById('{$uniq}').style.display='block'; this.style.display = 'none'">{t}подробнее{/t}</a>
            <div class="more" id="{$uniq}">
                {t}Код ошибки:{/t}{$exception->getCode()}<br>
                {t}Тип ошибки:{/t}{$type}<br>
                {t}Файл:{/t}{$exception->getFile()}<br>
                {t}Строка:{/t}{$exception->getLine()}<br>
                {t}Стек вызова:{/t} <pre>{$exception->getTraceAsString()}</pre><br>
            </div>
                    
        </div>
    </div>
{else}
    <!-- Исключение в модуле {$controllerName} -->
{/if}