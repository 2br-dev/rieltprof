<div class="archiveResult">
    <form class="crud-form" method="POST" action="{adminUrl}" enctype="multipart/form-data" data-dialog-options='{ "width":"500", "height":"450" }'>
        <p>
            {if $archive_constant == $mode}
                {t}После архивирования у затронутых документтов пропадет возможность редактирования. Для редактирования архивных документов потребуется их разархивание.{/t}
            {else}
                {t}Будет произведена разархивация документов с указанной даты по настоящее время.{/t}
            {/if}
        </p>
        <p><input name="archive" value="all" type="radio" checked> {t}Все{/t}</p>

        <p><input name="archive" value="date" type="radio"> {$form_object.__date->getTitle()}</p>
        <div>
            {include file=$form_object.__date->getRenderTemplate() field=$form_object.__date}
        </div>
        <br>
        <input type="hidden" name="mode" value="{$mode}">
    </form>
</div>

