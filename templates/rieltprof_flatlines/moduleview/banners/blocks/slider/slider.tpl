{* Блок, отображает баннеры из указанной зоны в виде слайдера *}
{if $zone}
    {addcss file="libs/owl.carousel.min.css"}
    {addjs file="libs/owl.carousel.min.js"}
    {addjs file="rs.sliders.js"}

    {$banners = $zone->getBanners()}
    <div class="owl-carousel owl-theme main-corousel rs-js-slider">
        {foreach $banners as $banner}
            <div class="item" {$banner->getDebugAttributes()}>
                <a {if $banner.link}href="{$banner.link}"{/if} {if $banner.targetblank}target="_blank"{/if}><!--
                    --><img src="{$banner->getBannerUrl(1169, 701, 'cxy')}" alt="{$banner.title}"><!--
                --></a>
            </div>
        {/foreach}
    </div>
{else}
    {include file="%THEME%/block_stub.tpl"  class="block-banner-slider" do=[
    [
    'title' => t("Добавьте баннерную зону 1169x701px и загрузите в неё баннеры"),
    'href' => {adminUrl do=false mod_controller="banners-ctrl"}
    ],
    [
    'title' => t("Настройте блок"),
    'href' => {$this_controller->getSettingUrl()},
    'class' => 'crud-add'
    ]
    ]}
{/if}