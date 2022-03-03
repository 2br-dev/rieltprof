{* Блок, отображает список брендов *}

{addcss file="libs/owl.carousel.min.css"}
{addjs file="libs/owl.carousel.min.js"}
{addjs file="rs.brands.js"}

{if !empty($brands)}
    <section class="sec sec-brand anti-container">
        <div class="title">
            <div class="container-fluid"><a href="{$router->getUrl('catalog-front-allbrands')}" class="title-text">{t}Бренды{/t}</a>
                <div class="sec-nav"><i class="pe-7s-angle-left-circle pe-2x pe-va arrow-left"></i><i class="pe-7s-angle-right-circle pe-2x pe-va arrow-right"></i></div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="brand-carousel owl-carousel owl-theme">
                    {foreach $brands as $brand}
                        {if $brand.image}
                            <div class="item" {$brand->getDebugAttributes()}>
                                <a href="{$brand->getUrl()}">
                                    <img src="{$brand->__image->getUrl(100,100,'axy')}" alt="{$brand.title}" class="center-block"/>
                                </a>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>
        </div>
    </section>
{/if}