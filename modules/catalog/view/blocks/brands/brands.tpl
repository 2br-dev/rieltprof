{if !empty($brands)}
    <div class="section pt-0">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="h1 m-0">{t}Бренды{/t}</div>
                <div class="d-none d-sm-block ms-3">
                    <a href="{$router->getUrl('catalog-front-allbrands')}" class="btn btn-primary">{t}Все бренды{/t}</a>
                </div>
            </div>
            <div class="re-container-mob">
                <div class="row row-cols-xl-6 row-cols-md-4 row-cols-auto g-3 re-container-mob__inner">
                    {foreach $brands as $brand}
                    <div {$brand->getDebugAttributes()}>
                        <a class="brand-item{if $THEME_SETTINGS.force_white_background_brand} white-bg{/if}" href="{$brand->getUrl()}">
                            <div class="brand-item__img">
                                {if $brand.image}
                                    <img srcset="{$brand->__image->getUrl(324, 108,'xy')} 2x" src="{$brand->__image->getUrl(162, 54,'xy')}" loading="lazy" alt="{$brand.title}">
                                {else}
                                    <img src="{$THEME_IMG}/decorative/brand-empty.svg" alt="{$brand.title}">
                                {/if}
                            </div>
                            <div class="brand-item__title">{$brand.title}</div>
                        </a>
                    </div>
                    {/foreach}
                </div>
            </div>
            <div class="mt-4 d-sm-none">
                <a href="{$router->getUrl('catalog-front-allbrands')}" class="btn btn-primary col-12">{t}Все бренды{/t}</a>
            </div>
        </div>
    </div>
{else}
    <div class="h1 m-0 mb-4">{t}Бренды{/t}</div>

    {capture assign = "skeleton_html"}
        {$classes = ['', 'd-md-block d-none', 'd-xl-block d-none']}
        <div class="re-container-mob">
            <div class="row row-cols-xl-6 row-cols-md-4 row-cols-auto g-3 re-container-mob__inner">
                {foreach $classes as $class}
                    {for $i = 1 to 4}
                        <div class="{$class}">
                            <img width="230" height="132" src="{$THEME_IMG}/skeleton/skeleton-brand.svg" alt="">
                        </div>
                    {/for}
                {/foreach}
            </div>
        </div>
    {/capture}

    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Бренды{/t}"
    skeleton_html = $skeleton_html
    do = [
        [
            'title' => "{t}Добавить и включить к отображению бренды{/t}",
            'href' => "{adminUrl do=false mod_controller="catalog-brandctrl"}"
        ]
    ]}
{/if}