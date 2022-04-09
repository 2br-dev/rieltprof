<h1 class="mb-md-5 mb-4">
    {if !empty($query)}{t}Результаты поиска{/t}{else}{$category.name}{/if}
</h1>
{if $category.description}
    <div class="mb-4 col-lg-6">
        {$category.description}
    </div>
{/if}
{if $sub_dirs}
    <div class="mb-lg-6 mb-5">
        <div class="re-container-table">
            <div class="catalog-subcategories re-container-table__inner">
                {foreach $sub_dirs as $item}
                    <a href="{urlmake category=$item._alias p=null pf=null filters=null bfilter=null}" class="catalog-subcategory">{$item.name}</a>
                {/foreach}
            </div>
        </div>
    </div>
{/if}