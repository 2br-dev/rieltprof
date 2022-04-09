{if $categories}
    <div class="re-container-mob">
        <div class="row row-cols-lg-4 row-cols-md-3 row-cols-auto g-3 g-xl-4 re-container-mob__inner">
            {foreach $categories as $category}
                <div>
                    {$postfix = ""}
                    {if $THEME_SETTINGS.top_category_with_images == 'background'}{$postfix = "-bg"}{/if}
                        <a href="{$category->getUrl()}" class="index-category{$postfix}{if $THEME_SETTINGS.force_white_background_category} white-bg{/if}">
                            <div class="index-category{$postfix}__img">
                                {if $postfix}
                                    <img loading="lazy" src="{$category->getMainImage(357, 200, 'cxy')}" alt="{$category.name}">
                                {else}
                                    <img loading="lazy" src="{$category->getMainImage(180, 120)}" alt="{$category.name}">
                                {/if}
                            </div>
                            <div class="index-category{$postfix}__title">{$category.name}</div>
                        </a>
                </div>
            {/foreach}
        </div>
    </div>
{else}
    {capture assign = "skeleton_html"}
        {$classes = [5 => 'd-md-block d-none', 6 => 'd-md-block d-none', 7 => 'd-lg-block d-none', 8 => 'd-lg-block d-none']}
        <div class="re-container-mob">
            <div class="row row-cols-lg-4 row-cols-md-3 row-cols-auto g-3 g-xl-4 re-container-mob__inner">
                {for $i = 1 to 8}
                    <div {if isset($classes[$i])}class="{$classes[$i]}"{/if}>
                        <img src="{$THEME_IMG}/skeleton/skeleton-category.svg" alt="">
                    </div>
                {/for}
            </div>
        </div>
    {/capture}

    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Категории товаров{/t}"
    skeleton_html = $skeleton_html
    do = [
        [
            'title' => "{t}Добавить категорию{/t}",
            'href' => "{adminUrl do=false mod_controller="catalog-ctrl"}"
        ],
        [
            'title' => "{t}Настроить блок{/t}",
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]
    ]}
{/if}