{if $banners}
    <div class="banner-zone">
        {foreach $banners as $banner}
            {if $banner.link}{$tag='a'}{else}{$tag='span'}{/if}
            <{$tag} class="d-flex align-items-center justify-content-center h-100" {if $banner.link}href="{$banner.link}"{/if} {if $banner.targetblank}target="_blank"{/if} {$banner->getDebugAttributes()}>
            <img width="{$zone.width}" height="{$zone.height}"
                 src="{$banner->getBannerUrl($zone.width, $zone.height)}"
                 loading="lazy" alt="{$banner.title}">
            </{$tag}>
        {/foreach}
    </div>
{else}
    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Баннер{/t}"
    skeleton = "skeleton-banner-catalog.svg"
    do = [
    [
    'title' => "{t}Добавить баннерную зону 1114x260 px{/t}",
    'href' => "{adminUrl do=false mod_controller="banners-ctrl"}"
    ],
    [
    'title' => "{t}Загрузить и включить баннеры в соданную зону{/t}",
    'href' => "{adminUrl do=false mod_controller="banners-ctrl"}"
    ],
    [
    'title' => "{t}Настроить блок{/t}",
    'href' => "{$this_controller->getSettingUrl()}",
    'class' => 'crud-add'
    ]
    ]}
{/if}