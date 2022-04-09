{* Шаблон горизонтального списка категорий, используется, если в настройках темы включен соответствующий вид отображения *}
{nocache}
{addjs file="%catalog%/rscomponent/category.js"}
{/nocache}

{* Функция, возвращает необходимый класс видимости d-*-block для категории, согласно настройкам темы *}
{function getDisplayClass}
    {strip}
    {if $THEME_SETTINGS.category_item_resolution_count}
        {$infix = ['', 'xxl', 'xl']}
        {$arr = array_reverse(explode(',', $THEME_SETTINGS.category_item_resolution_count))}
        {foreach $arr as $limit}
            {if $index > $limit}
                d-none {if $infix[$limit@index]}d-{$infix[$limit@index]}-block{/if}
                {break}
            {/if}
        {/foreach}
    {/if}
    {/strip}
{/function}

{if $dirlist->count()}
    <ul class="head-catalog__list">
        {foreach $dirlist as $node}
            {$dir = $node->getObject()}
            <li class="{getDisplayClass index=$node@iteration}">
                <a class="head-catalog__link" href="{$dir->getUrl()}" {$dir->getDebugAttributes()}
                   {if $node->getChildsCount()}data-bs-toggle="dropdown" data-bs-reference="parent" data-bs-offset="0, 0"{/if}><span>{$dir.name}</span></a>
                {if $node->getChildsCount()}
                    <div class="dropdown-menu head-catalog__dropdown">
                        <div class="container">
                            <div class="row g-4 row-cols-3">
                                {foreach $node->getChilds() as $sub_node}
                                {$sub_dir = $sub_node->getObject()}
                                    <div><a href="{$sub_dir->getUrl()}" {$sub_dir->getDebugAttributes()}>{$sub_dir.name}</a></div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                {/if}
            </li>
        {/foreach}
        <li class="head-catalog__menu-btn">
            <button type="button" class="head-menu dropdown-catalog-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M4.75 6C4.75 5.58579 5.08579 5.25 5.5 5.25H19.5C19.9142 5.25 20.25 5.58579 20.25 6C20.25 6.41421 19.9142 6.75 19.5 6.75H5.5C5.08579 6.75 4.75 6.41421 4.75 6ZM4.75 12C4.75 11.5858 5.08579 11.25 5.5 11.25H19.5C19.9142 11.25 20.25 11.5858 20.25 12C20.25 12.4142 19.9142 12.75 19.5 12.75H5.5C5.08579 12.75 4.75 12.4142 4.75 12ZM4.75 18C4.75 17.5858 5.08579 17.25 5.5 17.25H19.5C19.9142 17.25 20.25 17.5858 20.25 18C20.25 18.4142 19.9142 18.75 19.5 18.75H5.5C5.08579 18.75 4.75 18.4142 4.75 18Z"/>
                </svg>
            </button>
        </li>
    </ul>
{else}
    {capture assign = "skeleton_html"}
        <ul class="head-catalog__list">
            {for $i = 1 to 7}
            <li class="{if $i > 5}d-xxl-block d-none{/if}">
                <a class="head-catalog__link">
                    <img height="18" src="{$THEME_IMG}/skeleton/skeleton-inline-item.svg" alt="">
                </a>
            </li>
            {/for}
        </ul>

    {/capture}

    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Каталог{/t}"
    skeleton_html = $skeleton_html
    do = [
        [
        'title' => "{t}Добавить категории{/t}",
        'href' => "{adminUrl do=false mod_controller="catalog-ctrl"}"
        ],
        [
            'title' => "{t}Настроить блок{/t}",
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]
    ]}
{/if}