{* Рекомендуемые товары *}

{if $recommended}
    {addcss file="libs/owl.carousel.min.css"}
    {addjs file="libs/owl.carousel.min.js"}

    {$shop_config=ConfigLoader::byModule('shop')}
    {$check_quantity=$shop_config.check_quantity}

    {* Количество элементов по вертикали *}
    {if !is_null($products) && count($products) >= 8}
        {$verticalNumber = 2}
    {else}
        {$verticalNumber = 1}
    {/if}

    <div class="sec sec-category">
        <div class="title anti-container">
            <div class="container-fluid">
                <span class="title-text">{$recommended_title|default:t("С этим товаром покупают")}</span>
                <div class="sec-nav"><i class="pe-7s-angle-left-circle pe-2x pe-va arrow-left"></i><i class="pe-7s-angle-right-circle pe-2x pe-va arrow-right"></i></div>
            </div>
        </div>

        <div class="category-carousel owl-carousel owl-theme">
            <div class="item">
                {foreach $recommended as $product}
                    {$url = $product->getUrl()}
                    <div class="col-xs-12">
                        {include file="%catalog%/product_in_list_block.tpl" product=$product}
                    </div>
                    {if ($product@iteration % $verticalNumber == 0) && !$product@last}
                        </div><div class="item">
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>
{/if}