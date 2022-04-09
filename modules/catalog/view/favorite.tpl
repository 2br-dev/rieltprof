{* Страница со списком избранных товаров *}
{$shop_config = ConfigLoader::byModule('shop')}
{$check_quantity = $shop_config->check_quantity}
{$list = $this_controller->api->addProductsMultiOffersInfo($list)}
{addjs file="%catalog%/rscomponent/listproducts.js"}

<div class="rs-favorite-page">
    <h1 class="mb-md-5 mb-4">{t}Избранное{/t}</h1>
    {if $list}
        {if !in_array($view_as, ['blocks', 'table'])}{$view_as = 'blocks'}{/if}
        <div class="row align-items-center g-md-4 g-lg-5 g-3 mb-4">
            <div class="col">
                <div class="catalog-bar">
                    <div class="catalog-select">
                        <div class="catalog-select__label">{t}Показать по{/t}:</div>
                        <div class="catalog-select__options">
                            <select class="rs-list-pagesize-change">
                                {foreach $items_on_page as $item}
                                    <option value="{$item}" {if $item == $page_size}selected{/if}>{$item}</option>
                                {/foreach}
                            </select>
                            <div class="catalog-select__value"></div>
                        </div>
                    </div>

                    {if !in_array($view_as, ['blocks', 'table'])}{$view_as = 'blocks'}{/if}
                    <ul class="catalog-view-as ms-3">
                        <li>
                            <a class="rs-list-view-change {if $view_as == 'blocks'}view-as_active{/if}" data-view="block">
                                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.2191 13.6393C17.4905 13.6393 16.866 13.0147 16.866 12.2861C16.866 11.5575 17.4905 10.933 18.2191 10.933C18.9478 10.933 19.5723 11.5575 19.5723 12.2861C19.5723 13.0147 18.9478 13.6393 18.2191 13.6393ZM18.2191 7.70628C17.4905 7.70628 16.866 7.08174 16.866 6.35313C16.866 5.62451 17.4905 5 18.2191 5C18.9478 5 19.5723 5.62451 19.5723 6.35313C19.5723 7.08174 18.9478 7.70628 18.2191 7.70628ZM12.2861 19.5723C11.5575 19.5723 10.933 18.9478 10.933 18.2191C10.933 17.4905 11.5575 16.866 12.2861 16.866C13.0147 16.866 13.6393 17.4905 13.6393 18.2191C13.6393 18.9478 13.0147 19.5723 12.2861 19.5723ZM12.2861 13.6393C11.5575 13.6393 10.933 13.0147 10.933 12.2861C10.933 11.5575 11.5575 10.933 12.2861 10.933C13.0147 10.933 13.6393 11.5575 13.6393 12.2861C13.6393 13.0147 13.0147 13.6393 12.2861 13.6393ZM12.2861 7.70628C11.5575 7.70628 10.933 7.08174 10.933 6.35313C10.933 5.62451 11.5575 5 12.2861 5C13.0147 5 13.6393 5.62451 13.6393 6.35313C13.6393 7.08174 13.0147 7.70628 12.2861 7.70628ZM6.35313 19.5723C5.62451 19.5723 5 18.9478 5 18.2191C5 17.4905 5.62451 16.866 6.35313 16.866C7.08174 16.866 7.70628 17.4905 7.70628 18.2191C7.70628 18.9478 7.08174 19.5723 6.35313 19.5723ZM6.35313 13.6393C5.62451 13.6393 5 13.0147 5 12.2861C5 11.5575 5.62451 10.933 6.35313 10.933C7.08174 10.933 7.70628 11.5575 7.70628 12.2861C7.70628 13.0147 7.08174 13.6393 6.35313 13.6393ZM6.35313 7.70628C5.62451 7.70628 5 7.08174 5 6.35313C5 5.62451 5.62451 5 6.35313 5C7.08174 5 7.70628 5.62451 7.70628 6.35313C7.70628 7.08174 7.08174 7.70628 6.35313 7.70628ZM18.2191 16.866C18.9478 16.866 19.5723 17.4905 19.5723 18.2191C19.5723 18.9478 18.9478 19.5723 18.2191 19.5723C17.4905 19.5723 16.866 18.9478 16.866 18.2191C16.866 17.4905 17.4905 16.866 18.2191 16.866Z" />
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a class="rs-list-view-change {if $view_as == 'table'}view-as_active{/if}" data-view="table">
                                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.25 6C4.25 5.58579 4.58579 5.25 5 5.25H19C19.4142 5.25 19.75 5.58579 19.75 6C19.75 6.41421 19.4142 6.75 19 6.75H5C4.58579 6.75 4.25 6.41421 4.25 6ZM4.25 12C4.25 11.5858 4.58579 11.25 5 11.25H19C19.4142 11.25 19.75 11.5858 19.75 12C19.75 12.4142 19.4142 12.75 19 12.75H5C4.58579 12.75 4.25 12.4142 4.25 12ZM4.25 18C4.25 17.5858 4.58579 17.25 5 17.25H19C19.4142 17.25 19.75 17.5858 19.75 18C19.75 18.4142 19.4142 18.75 19 18.75H5C4.58579 18.75 4.25 18.4142 4.25 18Z" />
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {if $view_as == 'blocks'}
            <div class="item-card-container">
                <div class="row row-cols-xxl-4 row-cols-md-3 row-cols-2 g-0 g-md-4">
                    {foreach $list as $product}
                        <div>
                            {include file="%catalog%/one_product.tpl"}
                        </div>
                    {/foreach}
                </div>
            </div>
        {else}
            <div class="item-list-container">
                {foreach $list as $product}
                    {include file="%catalog%/one_table_product.tpl"}
                {/foreach}
            </div>
        {/if}

        {include file="%THEME%/paginator.tpl"}
    {else}
        {include file="%THEME%/helper/usertemplate/include/empty_product_list.tpl" reason="{t}Нет товаров в избранном{/t}"}
    {/if}
</div>