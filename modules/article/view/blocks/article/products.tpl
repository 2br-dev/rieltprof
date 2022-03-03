{if !empty($products)}
    <section class="topProducts">
            <h2 class="mbot10"><span>{t}Прикреплённые товары{/t}</span></h2>
            <div class="productWrap">
                    <ul class="productList">
                        {foreach from=$products item=product}
                        <li {$product->getDebugAttributes()}>
                            {$main_image=$product->getMainImage()}
                            <a href="{$product->getUrl()}" class="pic">
                            <span class="labels">
                                {foreach from=$product->getMySpecDir() item=spec}
                                {if $spec.image && $spec.is_label}
                                    <img src="{$spec->__image->getUrl(62,62, 'xy')}">
                                {/if}
                                {/foreach}
                            </span>
                            <img src="{$main_image->getUrl(141,185,'xy')}" alt="{$main_image.title|default:"{$product.title}"}"/></a>
                            <a href="{$product->getUrl()}" class="info">
                                <h3>{$product.title}</h3>
                                <div class="group">
                                    <div class="scost">
                                        {$last_price = $product->getOldCost()}
                                        {if $last_price>0}<div class="lastPrice">{$last_price}</div>{/if}
                                        <span>{$product->getCost()} {$product->getCurrency()}</span>
                                    </div>
                                    <span class="name">{$product->getMainDir()->name}</span>
                                </div>
                            </a>
                        </li>
                        {/foreach}
                    </ul>
                    <div class="clearLeft"></div>
            </div>
    </section>
{/if}