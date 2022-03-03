{* Все бренды в системе *}

{if $brands}
    <div class="catalog-table">
        {foreach $brands as $brand}
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card card-category-mini text-center" {$brand->getDebugAttributes()}>
                <div class="card-image">
                    <a href="{$brand->getUrl()}"><img src="{$brand->getMainImage(355, 135, 'xy')}" alt="{$brand.title}"></a>
                </div>
                <div class="card-text"><a href="{$brand->getUrl()}"><span>{$brand.title}</span></a></div>
            </div>
        </div>
        {/foreach}
    </div>
{else}
    <p class="empty-list">{t}Нет ни одного бренда{/t}</p>
{/if}