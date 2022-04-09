{if $banners}
    {$banner = $banners[0]}
    <a class="index-banner" {if $banner.link}href="{$banner.link}"{/if} {if $banner.targetblank}target="_blank"{/if} {$banner->getDebugAttributes()}>
        <div class="row">
            <div class="col-xl col-lg-6">
                <div class="py-lg-6">
                    <div class="index-banner__title">{$banner.title}</div>
                    <p>{$banner.info|nl2br}</p>
                </div>
            </div>
            <div class="col-xl-auto col text-center">
                <img width="{$zone.width}" height="{$zone.height}" src="{$banner->getImageUrl($zone.width, $zone.height)}" loading="lazy" alt="{$banner.title}">
            </div>
        </div>
    </a>
{else}
    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Баннер{/t}"
    skeleton = 'skeleton-banner-big.svg'
    do = [
        [
            'title' => "{t}Добавить баннерную зону 548x383 px{/t}",
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