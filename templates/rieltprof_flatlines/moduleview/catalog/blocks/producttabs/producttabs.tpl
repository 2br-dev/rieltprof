{$shop_config = ConfigLoader::byModule('shop')}
{$check_quantity = $shop_config->check_quantity}
{$list = $this_controller->api->addProductsMultiOffersInfo($list)}
{if $dirs}
    {$shop_config = ConfigLoader::byModule('shop')}
    <div class="container-fluid">
        <div class="row">
            <section class="product_featured clearfix">
                <ul class="nav nav-tabs text-center">
                    {foreach $dirs as $dir}
                    <li {if $dir@iteration == 1}class="active"{/if}><a data-toggle="tab"
                                                                       href="#category{$_block_id}-{$dir@iteration}">{$dir.name}</a>
                        {/foreach}
                    </li>
                </ul>

                <div class="tab-content">
                    {foreach $dirs as $dir}
                        <div id="category{$_block_id}-{$dir@iteration}"
                             class="tab-pane{if $dir@iteration != 1} fade {else} fade in active{/if}">

                            {foreach $products_by_dirs[$dir.id] as $product}
                                <div class="col-xs-6 col-sm-4 col-md-3">
                                    {include file="%catalog%/product_in_list_block.tpl" product=$product}
                                </div>
                            {/foreach}
                        </div>
                    {/foreach}
                </div>
            </section>
        </div>
    </div>
{else}
    <div class="col-xs-12 stub">
        {include file="%THEME%/block_stub.tpl"  class="blockProductTabs" do=[
        [
        'title' => t("Добавьте категории с товарами"),
        'href' => {adminUrl do=false mod_controller="catalog-ctrl"}
        ],
        [
        'title' => t("Настройте блок"),
        'href' => {$this_controller->getSettingUrl()},
        'class' => 'crud-add'
        ]
        ]}
    </div>
    <div class="delimiter-block stub"></div>
    <div class="clearfix"></div>
{/if}
