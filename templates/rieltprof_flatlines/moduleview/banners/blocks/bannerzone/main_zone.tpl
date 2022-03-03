{* Блок, отображает баннеры из указанной зоны *}

{if $zone}
    {if $param.rotate}
        {$onebanner = $zone->getOneBanner()}
        {if $onebanner['id']}
            {$banners = [0 => $onebanner]}
        {/if}
    {else}
        {$banners = $zone->getBanners()}
    {/if}
    {if $banners}
        <div class="side-banners">
            <div class="col-xs-12">
                {foreach $banners as $banner}
                    <div class="banner" {$banner->getDebugAttributes()}>
                        <img src="{$banner->getBannerUrl($zone.width, $zone.height)}" alt="{$banner.title}">
                        <div class="col-xs-12 col-lg-6 col-lg-offset-6">
                            <div class="banner_description" {if !$banner->getInfoLine(0) and !$banner->getInfoLine(1)}style="background-color: transparent; box-shadow: none" {/if}>
                                <h3>{$banner->getInfoLine(0)}</h3>
                                <p>{$banner->getInfoLine(1)}</p>
                                {if $banner.link}
                                    <a href="{$banner.link}" {if $banner.targetblank}target="_blank"{/if} class="theme-btn_subscribe">
                                        {t}Подробнее{/t}
                                    </a>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
        <div class="clearfix"></div>
    {/if}
{/if}