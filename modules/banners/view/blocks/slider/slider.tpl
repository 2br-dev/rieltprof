{nocache}
    {addjs file="%banners%/rscomponent/slider.js"}
{/nocache}
{if $banners}
    <div class="swiper-banner swiper-container" data-autoplay-delay="{$param.autoplay_delay}">
        <div class="swiper-wrapper" >
            {foreach $banners as $banner}
            <div class="swiper-slide" {$banner->getDebugAttributes()}>
                <a {if $banner.link}href="{$banner.link}"{/if} {if $banner.targetblank}target="_blank"{/if}>
                    <picture>
                        {if $banner.use_original_file}
                            {$srcset = "{$banner->getMobileOriginalUrl()}"}
                            {$src = "{$banner->getOriginalUrl()}"}
                        {else}
                            {$srcset = "{$banner->getMobileImageUrl(round($zone.width/2), round($zone.height/2), 'xy')}, {$banner->getMobileImageUrl(round($zone.width), round($zone.height), 'xy')} 2x"}
                            {$src = "{$banner->getImageUrl($zone.width, $zone.height, 'axy')}"}
                        {/if}

                        {if $banner.mobile_file}
                            <source srcset="{$srcset}" media="(max-width: 576px)">
                        {/if}
                        <img width="{$zone.width}" height="{$zone.height}" src="{$src}" alt="{$banner.title}" loading="lazy">
                    </picture>
                </a>
            </div>
            {/foreach}
        </div>
        <div class="d-none d-md-block">
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
{else}
    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Слайдер{/t}"
    skeleton = "skeleton-banner-medium.svg"
    do = [
        [
            'title' => "{t}Добавить баннерную зону 1119x400px{/t}",
            'href' => "{adminUrl do=false mod_controller="banners-ctrl"}"
        ],
        [
            'title' => "{t}Загрузить и включить баннеры в соданную зону{/t}",
            'href' => "{adminUrl do=false mod_controller="banners-ctrl"}"
        ],
        [
            'title' => "{t}Настроить блок{/t}",
            'href' => "{$this_controller->getSettingUrl()}",
            'class' => "crud-add"
        ]
    ]}
{/if}