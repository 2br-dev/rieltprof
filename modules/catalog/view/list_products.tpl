{addjs file="core6/rsplugins/ajaxpaginator.js" basepath="common"}
{addjs file="%catalog%/rscomponent/listproducts.js"}
{$list = $this_controller->api->addProductsDirs($list)}
{$list = $this_controller->api->addProductsMultiOffersInfo($list)}
<div id="products">
    {function emptyList}
        <div class="text-center mt-6 container col-lg-4 col-md-6 col-sm-8">
            <div class="mb-lg-6 mb-4">
                <img class="empty-page-img" src="{$THEME_IMG}/decorative/search.svg" alt="{t}Ничего не найдено{/t}">
            </div>
            <p class="mb-lg-6 mb-5">{$reason}</p>
            {if $button_link}
                <a href="{$button_link|default:$SITE->getRootUrl()}" class="btn btn-primary">{$button_text|default:"{t}На главную{/t}"}</a>
            {/if}
        </div>
    {/function}

    {if count($list) || $is_filter_active}
        {if !in_array($view_as, ['blocks', 'table'])}{$view_as = 'blocks'}{/if}
        <div class="mb-4">
            <div class="row align-items-center g-md-4 g-lg-5 g-3">
                <div class="col-sm-auto {if $THEME_SETTINGS.filter_view_variant == 'visible'}d-xl-none{/if}">
                    <a role="button" class="offcanvas-open catalog-filter-btn" data-source=".rs-filter-wrapper">
                        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.1048 4.60967H9.327C9.0032 3.64795 8.09355 2.95312 7.02404 2.95312C5.95453 2.95312 5.04488 3.64795 4.72108 4.60967H2.88266C2.4556 4.60967 2.10938 4.95589 2.10938 5.38296C2.10938 5.81002 2.4556 6.15624 2.88266 6.15624H4.72113C5.04494 7.11796 5.95458 7.81279 7.02409 7.81279C8.0936 7.81279 9.00325 7.11796 9.32705 6.15624H21.1048C21.5319 6.15624 21.8781 5.81002 21.8781 5.38296C21.8781 4.95589 21.5319 4.60967 21.1048 4.60967ZM7.02404 6.26621C6.53702 6.26621 6.14079 5.86997 6.14079 5.38296C6.14079 4.89594 6.53702 4.4997 7.02404 4.4997C7.51106 4.4997 7.90729 4.89594 7.90729 5.38296C7.90729 5.86997 7.51106 6.26621 7.02404 6.26621Z" />
                            <path d="M21.1048 11.2356H19.2663C18.9425 10.2739 18.0328 9.5791 16.9633 9.5791C15.8939 9.5791 14.9842 10.2739 14.6604 11.2356H2.88266C2.4556 11.2356 2.10938 11.5819 2.10938 12.0089C2.10938 12.436 2.4556 12.7822 2.88266 12.7822H14.6604C14.9842 13.7439 15.8939 14.4388 16.9634 14.4388C18.0328 14.4388 18.9425 13.7439 19.2663 12.7822H21.1048C21.5319 12.7822 21.8781 12.436 21.8781 12.0089C21.8781 11.5819 21.5319 11.2356 21.1048 11.2356ZM16.9634 12.8922C16.4764 12.8922 16.0801 12.4959 16.0801 12.0089C16.0801 11.5219 16.4764 11.1257 16.9634 11.1257C17.4504 11.1257 17.8466 11.5219 17.8466 12.0089C17.8466 12.4959 17.4504 12.8922 16.9634 12.8922Z" />
                            <path d="M21.1048 17.8616H12.6401C12.3163 16.8999 11.4067 16.2051 10.3372 16.2051C9.26766 16.2051 8.35802 16.8999 8.03422 17.8616H2.88266C2.4556 17.8616 2.10938 18.2078 2.10938 18.6349C2.10938 19.062 2.4556 19.4082 2.88266 19.4082H8.03422C8.35802 20.3699 9.26766 21.0647 10.3372 21.0647C11.4067 21.0647 12.3163 20.3699 12.6401 19.4082H21.1048C21.5319 19.4082 21.8781 19.062 21.8781 18.6349C21.8781 18.2078 21.5319 17.8616 21.1048 17.8616ZM10.3372 19.5182C9.85016 19.5182 9.45392 19.122 9.45392 18.635C9.45392 18.1479 9.85016 17.7517 10.3372 17.7517C10.8242 17.7517 11.2204 18.1479 11.2204 18.6349C11.2204 19.1219 10.8242 19.5182 10.3372 19.5182Z" />
                        </svg>
                        {$total_filter_count = count($filter) + count($bfilter)}
                        <span class="ms-2">{t}Фильтры{/t} {if $total_filter_count}({$total_filter_count}){/if}</span>
                    </a>
                </div>
                <div class="col">
                    <div class="catalog-bar">
                        <div class="catalog-select">
                            <div class="catalog-select__label d-none d-md-block">{t}Сортировать{/t}:</div>
                            <div class="catalog-select__options">
                                <select class="rs-list-sort-change">
                                    <option value="sortn" data-nsort="asc" {if $cur_sort=='sortn'}selected{/if}>{t}умолчанию{/t}</option>
                                    <option value="cost" data-nsort="asc" {if $cur_sort == 'cost' && $cur_n == 'asc'}selected{/if}>{t}возрастанию цены{/t}</option>
                                    <option value="cost" data-nsort="desc" {if $cur_sort == 'cost' && $cur_n == 'desc'}selected{/if}>{t}убыванию цены{/t}</option>
                                    <option value="rating" data-nsort="desc" {if $cur_sort == 'rating'}selected{/if}>{t}популярности{/t}</option>
                                    <option value="dateof" data-nsort="desc" {if $cur_sort == 'dateof'}selected{/if}>{t}новизне{/t}</option>
                                    <option value="num" data-nsort="desc" {if $cur_sort == 'num'}selected{/if}>{t}наличию{/t}</option>
                                    <option value="title" data-nsort="asc" {if $cur_sort == 'title'}selected{/if}>{t}названию{/t}</option>
                                    {if $can_rank_sort}
                                        <option value="rank" data-nsort="asc" {if $cur_sort == 'rank'}selected{/if}>{t}релевантности{/t}</option>
                                    {/if}
                                </select>
                                <div class="catalog-select__value"></div>
                            </div>
                        </div>
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
        </div>
        {if $list}
            {if $view_as == 'blocks'}
                <div class="item-card-container">
                    <div class="row {if $THEME_SETTINGS.filter_view_variant == 'visible'}row-cols-xxl-4{else}row-cols-xxl-5 row-cols-xl-4{/if} row-cols-md-3 row-cols-2 g-0 g-md-4 rs-products-list">
                        {foreach $list as $product}
                            <div>
                                {include file="%catalog%/one_product.tpl"}
                            </div>
                        {/foreach}
                    </div>
                </div>
            {else}
                <div class="item-list-container rs-products-list">
                    {foreach $list as $product}
                        {include file="%catalog%/one_table_product.tpl"}
                    {/foreach}
                </div>
            {/if}

            {include file="%catalog%/list_products_paginator.tpl"}

        {else}
            {emptyList reason="{t}По вашему запросу ничего не найдено. Проверьте правильность установленных фильтров{/t}"
                button_link="{urlmake filters=null pf=null bfilter=null p=null}" button_text="{t}Сбросить фильтры{/t}"}
        {/if}
    {else}
        {if $query === ""}
            {emptyList button_link=false reason="{t}В этой категории нет товаров. Попробуйте найти ваш товар в другой категории.{/t}"}
        {else}
            {emptyList reason="{t}По вашему запросу ничего не найдено. Проверьте правильность введенного запроса{/t}"}
        {/if}
    {/if}
</div>