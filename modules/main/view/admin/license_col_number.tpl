{$row=$cell->getRow()}
{if $row.type == 'script'}
    {$key="<strong>{$cell->getValue()}</strong>"}
{else}
    {$key="&nbsp;&nbsp;&middot;&nbsp;&nbsp;{$cell->getValue()}"}
{/if}
{if empty($row.errors)}
    {$key}
{else}
    <span style="color:red">{$key}</span><br>
    {foreach $row.errors as $item}
    <small>({$item})</small>
    {/foreach}
{/if}
{if $row.type == 'script' && $row.expire && empty($row.errors)}
    {* Если лицензия временная *}
    <p><a target="_blank" href="{adminUrl do="licenseUpdate" key=$cell->getValue()}" class="btn btn-default">{t}Продлить{/t}</a></p>
{/if}