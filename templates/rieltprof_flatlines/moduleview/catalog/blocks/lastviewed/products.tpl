{* Шаблон последних просмотренных пользователем товаров *}
{if count($products)}
    {$products = $this_controller->api->addProductsFavorite($products)}
    {addcss file="libs/owl.carousel.min.css"}
    {addjs file="libs/owl.carousel.min.js"}

    {$shop_config=ConfigLoader::byModule('shop')}
    {$check_quantity=$shop_config.check_quantity}

    {* Количество элементов по вертикали *}
    {if count($products) >= 8}
        {$verticalNumber = 2}
    {else}
        {$verticalNumber = 1}
    {/if}

    <section class="sec sec-category">
        <div class="title anti-container">
            <div class="container-fluid">
                <span class="title-text">{t}Вы смотрели{/t}</span>
                <div class="sec-nav"><i class="pe-7s-angle-left-circle pe-2x pe-va arrow-left"></i><i class="pe-7s-angle-right-circle pe-2x pe-va arrow-right"></i></div>
            </div>
        </div>

        <div class="category-carousel owl-carousel owl-theme">
            {if $products}
                <div class="item">
                {foreach $products as $product}
                    {$url = $product->getUrl()}
                    <div class="col-xs-12">
                        {include file="%catalog%/product_in_list_block.tpl" product=$product}
                    </div>
                    {if ($product@iteration % $verticalNumber == 0) && !$product@last}
                        </div><div class="item">
                    {/if}
                {/foreach}
                </div>
            {/if}
        </div>
    </section>
{/if}