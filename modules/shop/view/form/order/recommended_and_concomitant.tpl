<tr id="recommended-wrapper{$id}" class="recommended-wrapper">
    <td class="l-w-space">&nbsp;</td>
    <td class="chk">&nbsp;</td>
    <td colspan="7">
        <div class="tab" role="tabpanel">
            <ul class="tab-nav" role="tablist">
                {if !empty($concomitants)}
                    <li class="active">
                        <a data-target="#concomitant{$product.id}-tab0" data-toggle="tab" role="tab">{t}Сопутствующие{/t}</a>
                    </li>
                {/if}
                {if !empty($recommended)}
                    <li {if empty($concomitants)}class="active"{/if}>
                        <a data-target="#recommended{$product.id}-tab1" data-toggle="tab" role="tab">{t}Рекомендуемые{/t}</a>
                    </li>
                {/if}
            </ul>
            <a class="closerecommended" data-link-id="#showRecommended{$id}"><i class="zmdi zmdi-close f-16"></i></a>
            <div class="tab-content">
                {if !empty($concomitants)}
                    <div class="tab-pane active" id="concomitant{$product.id}-tab0" role="tabpanel">
                        <ul class="cart-item-concomitants">
                            {foreach $concomitants as $concomitant}
                                <li>
                                    <input type="checkbox" class="concomitant-check" data-cost-id="{$cost_id}" data-weight="{$concomitant->getWeight()}" name="concomitant[{$concomitant.id}]" value="{$concomitant.id}"/>
                                    {if $product->hasImage()}
                                        <a href="{$concomitant->getMainImage(800, 600, 'xy')}" rel="lightbox-products" data-title="{$concomitant.title}"><img src="{$concomitant->getMainImage(36, 36, 'axy')}"></a>
                                    {else}
                                        <img src="{$concomitant->getMainImage(36,36, 'axy')}">
                                    {/if}
                                    <a href="{$concomitant->getUrl()}" target="_blank" class="cart-item-concomitant-title">{$concomitant.title}</a>
                                    {$price=$concomitant->getCost(null, null, false)}
                                    {$old_price=$concomitant->getOldCost(null, null, false)}
                                    {$delta=0}
                                    {if $old_price>0}
                                        {$delta=round(($old_price-$price)/$old_price*100)}
                                    {/if}
                                    <span class="cart-item-concomitant-price"><b>{$concomitant->getCost(null, null)} {$concomitant->getCurrency()}</b> {if $delta}<sup>-{$delta}%</sup>{/if}</span>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
                {if !empty($recommended)}
                    <div class="tab-pane {if empty($concomitant)}active{/if}" id="recommended{$product.id}-tab1" role="tabpanel">
                        <ul class="cart-item-concomitants">
                            {foreach $recommended as $concomitant}
                                <li>
                                    <input type="checkbox" class="concomitant-check" name="concomitant[{$concomitant.id}]" data-cost-id="{$cost_id}" data-weight="{$concomitant->getWeight()}" value="{$concomitant.id}"/>
                                    {if $product->hasImage()}
                                        <a href="{$concomitant->getMainImage(800, 600, 'xy')}" rel="lightbox-products" data-title="{$concomitant.title}"><img src="{$concomitant->getMainImage(36, 36, 'axy')}"></a>
                                    {else}
                                        <img src="{$concomitant->getMainImage(36,36, 'axy')}">
                                    {/if}
                                    <a href="{$concomitant->getUrl()}" target="_blank" class="cart-item-concomitant-title">{$concomitant.title}</a>
                                    {$price=$concomitant->getCost(null, null, false)}
                                    {$old_price=$concomitant->getOldCost(null, null, false)}
                                    {$delta=0}
                                    {if $old_price>0}
                                        {$delta=round(($old_price-$price)/$old_price*100)}
                                    {/if}
                                    <span class="cart-item-concomitant-price"><b>{$concomitant->getCost(null, null)} {$concomitant->getCurrency()}</b> {if $delta}<sup>-{$delta}%</sup>{/if}</span>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
                <a class="btn btn-alt btn-primary va-m-c addconcomitantproduct">
                    <i class="zmdi zmdi-plus f-16"></i>
                    <span class="m-l-5">{t}Добавить{/t}</span>
                </a>
            </div>
        </div>
    </td>
    <td class="r-w-space">&nbsp;</td>
</tr>