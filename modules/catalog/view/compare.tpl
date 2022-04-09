{addjs file="%catalog%/rscomponent/compareshow.js"}
{addjs file="%catalog%/rscomponent/favorite.js"}

{* Страница со списком сравниваемых товаров *}
<div class="rs-compare-page">
    {if $comp_data.items}
        {* Трансформируем данные для удобного перебора дальше *}
        {$comp_by_products = []}
        {$identically = []}
        {$identically_group = []}
        {foreach $comp_data.values as $group_id=>$values}
            {foreach $values as $prop_id=>$product_values}
                {if !$comp_data.props[$prop_id].hidden}
                    {$before_value = null}
                    {$group_identical = true}
                    {$identically[$group_id][$prop_id] = true}
                    {foreach $product_values as $product_id => $prop}
                        {$value = "{if $prop}{$prop->textView()}{else}-{/if}"}
                        {$identically[$group_id][$prop_id] = $identically[$group_id][$prop_id]
                                                                && ($before_value === null || $before_value == $value)}
                        {$group_identical = $group_identical && $identically[$group_id][$prop_id]}
                        {$comp_by_products[$product_id][$group_id][$prop_id] = $value}
                        {$before_value = $value}
                    {/foreach}
                    {$identically_group[$group_id] = $group_identical} {* true, если все хар-ки в группе идентичны *}
                {/if}
            {/foreach}
        {/foreach}

        <div class="compare-products"
             data-compare-url='{ "remove":"{$router->getUrl('catalog-front-compare', ["Act" => "remove"])}" }'
             data-favorite-url="{$router->getUrl('catalog-front-favorite')}">
            <div class="compare-products__inner">
                <div class="container">
                    <div class="product-slider">
                        <div class="product-slider__container">
                            <div class="swiper-container swiper-compare-products">
                                <div class="swiper-wrapper" >
                                    {foreach $comp_data.items as $product}
                                        <div class="swiper-slide">
                                            {include file="%catalog%/one_product.tpl"}
                                        </div>
                                    {/foreach}
                                </div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-sm-6 mt-4">
            {if count($comp_data.items) > 1}
            <div class="compare-checkbox mb-5">
                <input id="compare-different" type="checkbox" {if $smarty.cookies.compareShowOnlyDifferent}checked{/if}>
                <label for="compare-different">
                    <span class="me-4">{t}Показать только отличия{/t}</span>
                    <span class="compare-checkbox__toggle"></span>
                </label>
            </div>
            {/if}
            <div class="swiper-container swiper-compare-table {if $smarty.cookies.compareShowOnlyDifferent && count($comp_data.items) > 1}hide-identical{/if}">
                <div class="compare-columns-titles">
                    {foreach $comp_data.values as $group_id => $values}
                        <div class="compare-columns-header {if $identically_group[$group_id]}param-identically{/if}">{$comp_data.groups[$group_id].title|default:t('Общие')}</div>
                        {foreach $values as $prop_id => $product_values}
                            {if !$comp_data.props[$prop_id].hidden}
                                <div class="compare-columns-title {if $identically[$group_id][$prop_id]}param-identically{/if}">{$comp_data.props[$prop_id].title}{if $comp_data.props[$prop_id].unit}, {$comp_data.props[$prop_id].unit}{/if}</div>
                            {/if}
                        {/foreach}
                    {/foreach}
                </div>
                <div class="swiper-wrapper">
                    {foreach $comp_by_products as $product_id=>$groups}
                        <div class="swiper-slide">
                            <div class="compare-columns">
                                {foreach $groups as $group_id => $props}
                                    <div class="compare-product-header {if $identically_group[$group_id]}param-identically{/if}"></div>
                                    {foreach $props as $prop_id => $value}
                                        <div class="compare-product-param {if $identically[$group_id][$prop_id]}param-identically{else}param-different{/if}">{$value}</div>
                                    {/foreach}
                                {/foreach}
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    {else}
        {include file="%THEME%/helper/usertemplate/include/empty_product_list.tpl" reason="{t}Добавьте товары для сравнения{/t}"}
    {/if}
</div>