{assign var=row value=$cell->getRow()}
{if $row.type == 'script'}
    {assign var=key value="<strong>{$cell->getValue()}</strong>"}
{else}
    {assign var=key value="&nbsp;&nbsp;&middot;&nbsp;&nbsp;{$cell->getValue()}"}
{/if}
{if empty($row.errors)}
    {$key}
{else}
    <span style="color:red">{$key}</span><br>
    {foreach from=$row.errors item=item}
    <small>({$item})</small>
    {/foreach}
{/if}
{if $row.type == 'script' && $row.expire && empty($row.errors)}
    {* Если лицензия временная *}
    <p><a target="_blank" href="{adminUrl do="licenseUpdate" key=$cell->getValue()}" class="btn btn-default">{t}Продлить{/t}</a></p>
{/if}