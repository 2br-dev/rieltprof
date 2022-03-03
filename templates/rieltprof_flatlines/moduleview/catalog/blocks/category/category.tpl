{* Список категорий из 3-х уровней *}
{nocache}
{addjs file="libs/jquery.mmenu.min.js"}
{addcss file="libs/jquery.mmenu.css"}
{/nocache}

{if $dirlist}
    <nav>
        <ul class="nav navbar-nav">
            {hook name="catalog-blocks-category-category:list-item" title="{t}Доплнительные пункты меню, в меню каталога{/t}"}
                {foreach $dirlist as $node}
                    {$dir = $node->getObject()}
                    <li class="{if $node->getChildsCount()} t-dropdown{/if}" {$dir->getDebugAttributes()}>
                        {* Первый уровень *}
                        <a {$dir->getDebugAttributes()} href="{$dir->getUrl()}">{$dir.name}</a>

                        {if $node->getChildsCount()}
                            {* Второй уровень *}
                            <div class="t-dropdown-menu">
                                <div class="container-fluid">
                                    <div class="t-nav-catalog-list__inner">
                                        <div class="t-close"><i class="pe-2x pe-7s-close-circle"></i></div>
                                        <div class="t-nav-catalog-list__scene">
                                            {foreach $node->getChilds() as $sub_node}
                                                {$sub_dir = $sub_node->getObject()}
                                                <div class="t-nav-catalog-list-block">
                                                    <a {$sub_dir->getDebugAttributes()} href="{$sub_dir->getUrl()}" class="t-nav-catalog-list-block__header">
                                                        {$sub_dir.name}
                                                    </a>

                                                    {* Третий уровень *}
                                                    {if $sub_node->getChildsCount()}
                                                        <ul class="t-nav-catalog-list-block__list">
                                                            {foreach $sub_node->getChilds() as $sub_node_2}
                                                                {$sub_dir_2 = $sub_node_2->getObject()}
                                                                <li>
                                                                    <a {$sub_dir_2->getDebugAttributes()} href="{$sub_dir_2->getUrl()}" class="t-nav-catalog-list-block__link">
                                                                        {$sub_dir_2.name}
                                                                    </a>
                                                                </li>
                                                            {/foreach}
                                                        </ul>
                                                    {/if}
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </li>
                {/foreach}
            {/hook}
        </ul>
    </nav>

    {* Мобильная версия каталога - 2 уровня *}
    <nav id="mmenu" class="hidden">
        <ul>
            <li>
                {moduleinsert name="\Catalog\Controller\Block\SearchLine" hideAutoComplete=true}
            </li>
            {hook name="catalog-blocks-category-category:list-item-mobile" title="{t}Доплнительные пункты меню, в меню каталога - мобильная версия{/t}"}
            {foreach $dirlist as $node}
                {$dir = $node->getObject()}
                <li>
                    <a href="{$dir->getUrl()}">{$dir.name}</a>
                    {if $node->getChildsCount()}
                        <ul>
                            {foreach $node->getChilds() as $sub_node}
                                {$sub_dir = $sub_node->getObject()}
                                <li>
                                    <a href="{$sub_dir->getUrl()}">{$sub_dir.name}</a>
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                </li>
            {/foreach}
            {/hook}
        </ul>
    </nav>
{else}
    <div class="col-padding">
        {include file="%THEME%/block_stub.tpl"  class="text-center white block-category" do=[
            [
                'title' => t("Добавьте категории товаров"),
                'href' => {adminUrl do=false mod_controller="catalog-ctrl"}
            ]
        ]}
    </div>
{/if}