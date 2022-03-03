{if $paginator->total_pages>1}
    {$pagestr = t('Страница %page', ['page' => $paginator->page])}
    {if $paginator->page > 1 && !substr_count($app->title->get(), $pagestr)}
        {$app->title->addSection($pagestr, 0, 'after')|devnull}
        {$caonical = implode('', ['<link rel="canonical" href="', $SITE->getRootUrl(true), substr($paginator->getPageHref(1),1), '"/>'])}
        {$app->setAnyHeadData($caonical)|devnull}
    {/if}

    {if !$paginator_len}
        {$paginator_len = 5}
    {/if}
    {$paginator->setPaginatorLen($paginator_len)|devnull}

    <div class="paginator">
        {if $paginator->showFirst()}
            <a href="{$paginator->getPageHref(1)}" data-page="1" title="{t}первая страница{/t}" class="first">1</a>
        {/if}

        {if $paginator->page>1}
            <a href="{$paginator->getPageHref($paginator->page-1)}" data-page="{$paginator->page-1}" title="{t}предыдущая страница{/t}" class="prev">
                <i class="pe-7s-angle-left"></i>
            </a>
        {/if}

        {foreach $paginator->getPageList() as $page}
            <a href="{$page.href}" data-page="{$page.n}" {if $page.act}class="active"{/if}>{$page.n}</a>
        {/foreach}

        {if $paginator->page < $paginator->total_pages}
            <a href="{$paginator->getPageHref($paginator->page+1)}" data-page="{$paginator->page+1}" title="{t}следующая страница{/t}" class="next">
                <i class="pe-7s-angle-right"></i>
            </a>
        {/if}

        {if $paginator->showLast()}
            <a href="{$paginator->getPageHref($paginator->total_pages)}" data-page="{$paginator->total_pages}" title="{t}последняя страница{/t}" class="last">{$paginator->total_pages}</a>
        {/if}
    </div>
{/if}