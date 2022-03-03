{* Просмотр одного бренда *}

{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config.check_quantity}

<div class="col-xs-12">
    <div class="sec-content_wrapper" {$brand->getDebugAttributes()}>
            <div class="sec-content_text">
                {if $brand.image}
                    <div class="text-center">
                        <img src="{$brand->__image->getUrl(250,250,'xy')}" class="mainImage" alt="{$brand.title}"/>
                    </div>
                {/if}

                {$brand.description}
            </div>
    </div>

    {if $dirs}
        <div class="sec-content_wrapper brand-category">
            <h2 class="h2">{t brand=$brand.title}Категории товаров %brand{/t}</h2>

            <ul class="brand-category_list">
                {foreach $dirs as $dir}
                    <li>
                        <a href="{$router->getUrl('catalog-front-listproducts',['category'=>$dir._alias,'bfilter'=> ["brand" => [$brand.id]]])}">{$dir.name}</a> <sup>({$dir.brands_cnt})</sup>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}
</div>

<div class="clearfix"></div>

{if $products}
    {addcss file="libs/owl.carousel.min.css"}
    {addjs file="libs/owl.carousel.min.js"}

    <section class="sec sec-category">
        <div class="title anti-container">
            <div class="container-fluid">
                <span class="title-text">{t brand=$brand.title}Актуальные товары %brand{/t}</span>
                <div class="sec-nav"><i class="pe-7s-angle-left-circle pe-2x pe-va arrow-left"></i><i class="pe-7s-angle-right-circle pe-2x pe-va arrow-right"></i></div>
            </div>
        </div>

        <div class="category-carousel owl-carousel owl-theme">
            {if $products}
                {foreach $products as $product}
                    {$url = $product->getUrl()}
                    <div class="item">
                        <div class="col-xs-12">
                            {include file="%catalog%/product_in_list_block.tpl" product=$product}
                        </div>
                    </div>
                {/foreach}
            {/if}
        </div>
    </section>
{/if}