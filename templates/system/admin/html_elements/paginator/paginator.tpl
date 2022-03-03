{if empty($local_options.short)}
    <span class="text hidden-xs">{t}страница{/t}</span>
{/if}

    <a {if $local_options.is_virtual}data-{/if}href="{$paginator->left}" class="prev {if !$local_options.is_virtual && !$local_options.no_ajax}call-update{/if} zmdi zmdi-chevron-left" {if $paginator->getUpdateContainer()}data-update-container="{$paginator->getUpdateContainer()}"{/if} title="{t}предыдущая страница{/t}"></a>
    <input type="text" class="page" name="{$paginator->page_key}" value="{$paginator->page}" onfocus="$(this).select()">

    <a {if $local_options.is_virtual}data-{/if}href="{$paginator->right}" class="next {if !$local_options.is_virtual && !$local_options.no_ajax}call-update{/if} zmdi zmdi-chevron-right" {if $paginator->getUpdateContainer()}data-update-container="{$paginator->getUpdateContainer()}"{/if} title="{t}следующая страница{/t}"></a>
    <span class="text">из {$paginator->page_count}</span>

{if empty($local_options.short)}
    <span class="text perpage_block"><span class="hidden-xs">{t}показывать{/t} </span>{t}по{/t} </span>
    <input type="text" class="perpage" name="{$paginator->pagesize_key}" value="{$paginator->page_size}" onfocus="$(this).select()">
    <button type="submit" class="btn btn-default"><i class="zmdi zmdi-check visible-xs"></i> <span class="hidden-xs">{t}Применить{/t}</span></button>

    <span class="total">{t}всего записей: {/t}<span class="total_value">{$paginator->total}</span></span>
{/if}