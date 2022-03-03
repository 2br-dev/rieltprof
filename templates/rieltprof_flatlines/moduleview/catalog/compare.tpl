{* Страница со списком сравниваемых товаров *}

{addjs file="rs.compareshow.js"}
{addjs file="rs.favorite.js"}

{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config->check_quantity}

{if count($comp_data.items)}
    <section class="sec sec-page-compare rs-compare-show" data-compare-url='{ "remove":"{$router->getUrl('catalog-front-compare', ["Act" => "remove"])}" }' data-favorite-url="{$router->getUrl('catalog-front-favorite')}">
        <div class="container-fluid">
            <div class="compare_wrapper">
                <h1 class="h1">{t}Сравнить товары{/t}</h1>
                <div class="compare_list">
                    <div class="row">
                        {foreach $comp_data.items as $product}
                            {$imglist = $product->getImages(false)}
                            <div class="col-xs-12 col-sm-6">
                                <div class="compare_product{if !$product->isAvailable()} rs-not-avaliable{/if}" data-id="{$product.id}">
                                    <div class="compare_product_img{if count($imglist)>1} rs-active-list{/if}">
                                        {foreach $imglist as $image}
                                            <img src="{$image->getUrl(276,224)}" {if !$image@first}class="hidden"{/if} alt="{$image.title}"/>
                                        {/foreach}
                                        {if !count($imglist)}
                                            <img src="{$product->getImageStub()->getUrl(276,224)}" alt=""/>
                                        {/if}
                                    </div>
                                    <div class="compare_product_text">
                                        <div class="compare_product_category"><a href="#"><small>{$product->getMaindir()->name}</small></a></div>
                                        <div class="compare_product_title">
                                            <span>{$product.title}</span>
                                            <div class="compare_product_cost">{$product->getMinPrice()} {$product->getCurrency()}</div>

                                            <span class="unobtainable hidden">{t}Нет в наличии{/t}</span>

                                            {if $THEME_SETTINGS.enable_favorite}
                                                <span class="card-product_ticket">
                                                    <a class="ticket-favorite rs-favorite {if $product->inFavorite()}rs-in-favorite{/if}" data-title="{t}В избранное{/t}" data-already-title="{t}В избранном{/t}"></a>
                                                </span><br>
                                            {/if}

                                            {if $shop_config}
                                                <a data-href="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}" data-add-text="{t}Добавлено{/t}" class="link link-more rs-add-to-cart rs-no-show-cart">{t}В корзину{/t}</a>
                                            {/if}
                                            <a class="link link-del rs-remove"><i class="pe-2x pe-7s-close"></i>{t}Удалить{/t}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <div class="page-product_content">
                        {*
                        <ul class="nav nav-tabs nav-tabs_scroll-x hidden-xs hidden-sm">
                            {foreach $comp_data.groups as $group_id => $group}
                                <li {if $group@first}class="active"{/if}><a data-toggle="tab" href="#tab-{$group_id}">{$group.title|default:"{t}Общие{/t}"}</a></li>
                            {/foreach}
                        </ul>
                        *}
                        <div class="tab-content">
                            <table class="tab-content_table_character">
                                <tbody>
                                {foreach $comp_data.values as $group_id=>$values}
                                        <tr>
                                            <td colspan="2" class="tab-content_table_character-title">
                                                {$comp_data.groups[$group_id].title|default:t('Общие')}
                                            </td>
                                        </tr>
                                    {foreach $values as $prop_id=>$product_values}
                                        {if !$comp_data.props[$prop_id].hidden}
                                        <tr>
                                            <td colspan="2" class="tab-content_table_character-subtitle">
                                                {$comp_data.props[$prop_id].title}{if $comp_data.props[$prop_id].unit}, {$comp_data.props[$prop_id].unit}{/if}
                                            </td>
                                        </tr>
                                        {foreach $product_values as $product_id=>$prop}
                                            <tr class="tab-content_table_character-text">
                                                <td><span>{$comp_data.items[$product_id].title}</span></td>
                                                <td><span>{if $prop}{$prop->textView()}{else}-{/if}</span></td>
                                            </tr>
                                        {/foreach}
                                        {/if}
                                    {/foreach}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{else}
    <div class="empty-list">
        {t}Добавьте товары для сравнения{/t}
    </div>
{/if}