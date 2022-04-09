{$status=$cell->getRow()->status}

{if $status=='success'}
    <a class="crud-edit crud-sm-dialog uline" href="{$router->getAdminUrl('getSuccessInfo', ['id' => $cell->getRow()->id])}">{t}информация по чеку{/t}</a>
{elseif $status=='wait'}
    <a class="crud-get uline" href="{$router->getAdminUrl('getReport', ['id' => $cell->getRow()->id])}">{t}получить статус чека{/t}</a>
{elseif $status=='fail'}
    <a class="crud-edit crud-sm-dialog uline" href="{$router->getAdminUrl('getErrors', ['id' => $cell->getRow()->id])}">{t}показать ошибки{/t}</a><br/><br/>
    <a class="crud-get uline" href="{$router->getAdminUrl('getReport', ['id' => $cell->getRow()->id])}">{t}получить статус чека{/t}</a>
{/if}
