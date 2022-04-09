{* Горизонтальное меню в шапке. Отображает 2 уровня вложенности *}
{if $items->count()}
    <ul class="head-bar__menu">
        {foreach $items as $item}
            {$hasChilds = $item->getChildsCount()}
            <li>
                <a class="head-bar__link" {if !$hasChilds}href="{$item.fields->getHref()}"{else}href="#" data-bs-toggle="dropdown" data-bs-reference="parent"{/if} {if $item.fields.target_blank}target="_blank"{/if}>
                    <span>{$item.fields.title}</span>
                    {if $hasChilds}
                        <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M3.8193 6.14645C4.02237 5.95118 4.35162 5.95118 4.55469 6.14645L8.00033 9.45956L11.446 6.14645C11.649 5.95118 11.9783 5.95118 12.1814 6.14645C12.3844 6.34171 12.3844 6.65829 12.1814 6.85355L8.36802 10.5202C8.16495 10.7155 7.8357 10.7155 7.63263 10.5202L3.8193 6.85355C3.61622 6.65829 3.61622 6.34171 3.8193 6.14645Z"/>
                        </svg>
                    {/if}
                </a>
                {if $hasChilds}
                    <ul class="dropdown-menu head-bar__dropdown">
                        {foreach $item->getChilds() as $subitem}
                            <li><a class="dropdown-item" href="{$subitem.fields->getHref()}">{$subitem.fields.title}</a></li>
                        {/foreach}
                    </ul>
                {/if}
            </li>
        {/foreach}
    </ul>
{else}
    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
        name = "{t}Меню{/t}"
        skeleton = "skeleton-head-menu.svg"
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