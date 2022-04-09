{if $items->count()}
    <div class="h2">{$root.title}</div>
    <ul class="footer-menu">
        {foreach $items as $item}
            <li {$item.fields->getDebugAttributes()}>
                <a href="{$item.fields->getHref()}" {if $item.fields.target_blank}target="_blank"{/if}>{$item.fields.title}</a>
            </li>
        {/foreach}
    </ul>
{else}
    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
        name = "{t}Меню{/t}"
        skeleton = 'skeleton-footer-menu-small.svg'
        do = [
            [
                'title' => "{t}Добавить пункт меню{/t}",
                'href' => "{adminUrl do=false mod_controller="menu-ctrl"}"
            ],
            [
                'title' => "{t}Настроить блок{/t}",
                'href' => {$this_controller->getSettingUrl()},
                'class' => 'crud-add'
            ]
        ]}
{/if}