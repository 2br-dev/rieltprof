{if $banners}
    {$banner = $banners[0]}
    <div class="banner-zone">
        <a {if $banner.link}href="{$banner.link}"{/if} {if $banner.targetblank}target="_blank"{/if} {$banner->getDebugAttributes()}>
            <picture>
                {if $banner.mobile_file}
                    <source srcset="{$banner->getMobileImageUrl(600, 600, 'xy')}, {$banner->getMobileImageUrl(1200, 1200, 'xy')} 2x" media="(max-width: 576px)">
                {/if}
                <img width="{$zone.width}" height="{$zone.height}" src="{$banner->getImageUrl($zone.width, $zone.height, 'xy')}" alt="{$banner.title}" loading="lazy">
            </picture>
        </a>
    </div>
{else}
    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Баннер{/t}"
    skeleton = 'skeleton-banner-big.svg'
    do = [
        [
            'title' => "{t}Добавить баннерную зону 1500x367 px{/t}",
            'href' => "{adminUrl do=false mod_controller="banners-ctrl"}"
        ],
        [
            'title' => "{t}Загрузить и включить баннеры в соданную зону{/t}",
            'href' => "{adminUrl do=false mod_controller="banners-ctrl"}"
        ],
        [
            'title' => "{t}Настроить блок{/t}",
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]
    ]}
{/if}