{* Просмотр одного бренда *}
<div class="section pt-0">
    <div class="container">
        <div class="row g-3" {$brand->getDebugAttributes()}>
            {if $brand.image}
                <div class="col-lg-3 offset-xxl-1 order-lg-last">
                    <div class="brand-image">
                        <img src="{$brand->__image->getUrl(360,360,'xy')}"
                             srcset="{$brand->__image->getUrl(720, 720, 'xy')} 2x" class="mainImage" alt="{$brand.title}"/>
                    </div>
                </div>
            {/if}
            <div class="col">
                <h1 class="mb-lg-5">{$brand.title}</h1>
                {$brand.description}
            </div>
        </div>
    </div>
</div>
{if $dirs}
    <div class="brand-categories">
    <div class="container">
        <div class="h2 mb-lg-5 mb-4">{t brand=$brand.title}Категории товаров %brand{/t}</div>
        <div class="re-container-table">
            <div class="catalog-subcategories re-container-table__inner">
                {foreach $dirs as $dir}
                    <a href="{$this_controller->api->encodeDirFilterParamsToUrl($dir, ["brand" => [$brand.id]])}" class="catalog-subcategory">
                        {$dir.name} <span class="catalog-subcategory__count">({$dir.brands_cnt})</span>
                    </a>
                {/foreach}
            </div>
        </div>
    </div>
</div>
{/if}
{if $products}
    <div class="section">
        <div class="container">
            <div class="h2 mb-lg-5 mb-4">{t brand=$brand.title}Актуальные товары %brand{/t}</div>
            <div class="item-card-container">
                <div class="row row-cols-xxl-5 row-cols-xl-4 row-cols-md-3 row-cols-2 g-0 g-md-4">
                    {foreach $products as $product}
                    <div>
                        {include file="%catalog%/one_product.tpl" product=$product}
                    </div>
                    {/foreach}
                </div>
            </div>
            {include file="%THEME%/paginator.tpl"}
        </div>
    </div>
{/if}