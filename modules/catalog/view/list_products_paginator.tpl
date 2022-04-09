<div class="rs-pagination-block">
    {if $paginator->page < $paginator->total_pages}
        <div class="mt-5">
            <a class="btn btn-outline-primary col-12 rs-ajax-paginator"
               data-pagination-options='{ "appendElement":".rs-products-list", "loaderBlock":".rs-pagination-block", "replaceBrowserUrl": true}'
               data-url="{$paginator->getPageHref($paginator->page+1)}"
            ><span>{t}Показать еще{/t}</span>
            </a>
        </div>
    {/if}
    <div class="mt-5">
        <div class="g-4 row row-cols-auto align-items-center justify-content-between">
            {include file="%THEME%/paginator.tpl" class=" "}
            <div>
                <div class="catalog-select">
                    <div class="catalog-select__label">{t}Показать по{/t}:</div>
                    <div class="catalog-select__options">
                        <select class="rs-list-pagesize-change">
                            {foreach $items_on_page as $item}
                                <option value="{$item}" {if $item == $page_size}selected{/if}>{$item}</option>
                            {/foreach}
                        </select>
                        <div class="catalog-select__value"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>