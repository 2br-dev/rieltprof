{if $paginator->total_pages>1}
    {$pagestr = t('Страница %page', ['page' => $paginator->page])}
    {if $paginator->page > 1 && !substr_count($app->title->get(), $pagestr)}
        {$app->title->addSection($pagestr, 0, 'after')|devnull}
    {/if}

    {if !$paginator_len}
        {$paginator_len = 5}
    {/if}
    {$paginator->setPaginatorLen($paginator_len)|devnull}
    <div class="{$class|default:"mt-5"}">
        <nav class="pagination" aria-label="pagination">
            <ul class="pagination__list">
                {if $paginator->page>1}
                    <li class="pagination__item pagination__item_arrow">
                        <a href="{$paginator->getPageHref($paginator->page-1)}" data-page="{$paginator->page-1}" title="{t}предыдущая страница{/t}">
                            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7803 5.72846C15.0732 6.03307 15.0732 6.52693 14.7803 6.83154L9.81066 12L14.7803 17.1685C15.0732 17.4731 15.0732 17.9669 14.7803 18.2715C14.4874 18.5762 14.0126 18.5762 13.7197 18.2715L8.21967 12.5515C7.92678 12.2469 7.92678 11.7531 8.21967 11.4485L13.7197 5.72846C14.0126 5.42385 14.4874 5.42385 14.7803 5.72846Z" />
                            </svg>
                        </a>
                    </li>
                {/if}
                {if $paginator->showFirst()}
                    <li class="pagination__item pagination__item_num">
                        <a href="{$paginator->getPageHref(1)}" data-page="1" title="{t}первая страница{/t}"s>1</a>
                    </li>
                    <li class="pagination__item pagination__item_num pagination__item_disable">
                        <span>...</span>
                    </li>
                {/if}

                {foreach $paginator->getPageList() as $page}
                    <li class="pagination__item pagination__item_num{if $page.act} pagination__item_active{/if}">
                        <a href="{$page.href}" data-page="{$page.n}">{$page.n}</a>
                    </li>
                {/foreach}

                {if $paginator->showLast()}
                    <li class="pagination__item pagination__item_num pagination__item_disable">
                        <span>...</span>
                    </li>
                    <li class="pagination__item pagination__item_num">
                        <a href="{$paginator->getPageHref($paginator->total_pages)}" data-page="{$paginator->total_pages}" title="{t}последняя страница{/t}">{$paginator->total_pages}</a>
                    </li>
                {/if}

                {if $paginator->page < $paginator->total_pages}
                    <li class="pagination__item pagination__item_arrow">
                        <a href="{$paginator->getPageHref($paginator->page+1)}" data-page="{$paginator->page+1}" title="{t}следующая страница{/t}">
                            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.21967 5.72846C8.92678 6.03307 8.92678 6.52693 9.21967 6.83154L14.1893 12L9.21967 17.1685C8.92678 17.4731 8.92678 17.9669 9.21967 18.2715C9.51256 18.5762 9.98744 18.5762 10.2803 18.2715L15.7803 12.5515C16.0732 12.2469 16.0732 11.7531 15.7803 11.4485L10.2803 5.72846C9.98744 5.42385 9.51256 5.42385 9.21967 5.72846Z" />
                            </svg>
                        </a>
                    </li>
                {/if}
            </ul>
        </nav>
    </div>
{/if}