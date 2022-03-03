{if $products}
    {$products = $this_controller->api->addProductsFavorite($products)}
    {* Шаблон отображает список товаров из определенной категории *}
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
                {if $block_title}
                    <span>{$block_title}</span>
                {else}
                    <a href="{$dir->getUrl()}" class="title-text">{$dir.name}</a>
                {/if}
                <div class="sec-nav"><i class="pe-7s-angle-left-circle pe-2x pe-va arrow-left"></i><i class="pe-7s-angle-right-circle pe-2x pe-va arrow-right"></i></div>
            </div>
        </div>

        <div class="category-carousel owl-carousel owl-theme">
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
        </div>
    </section>
{else}
    <div class="col-padding">
        {include file="%THEME%/block_stub.tpl"  class="block-top-products" do=[
        [
            'title' => t("Добавьте категории товаров"),
            'href' => {adminUrl do=false mod_controller="catalog-ctrl"}
        ],
        [
            'title' => t("Настройте блок"),
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]
        ]}
    </div>
{/if}