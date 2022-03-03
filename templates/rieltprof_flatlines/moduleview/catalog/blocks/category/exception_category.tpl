{* Список категорий для страницы 404. 1 уровень *}
{if $dirlist}
    <nav>
        <ul class="exception-nav">
            {foreach $dirlist as $dir}
            <li {$dir.fields->getDebugAttributes()}>
                <a href="{$dir.fields->getUrl()}">{$dir.fields.name}</a>
            </li>
            {/foreach}
        </ul>
    </nav>
{/if}