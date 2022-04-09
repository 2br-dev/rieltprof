<div class="head-dropdown-catalog__subcat d-block px-3" id="dropdown-subcat-0">
    <div class="mb-4">
        <img src="{$THEME_IMG}/icons/arrow-left.svg" alt="" class="me-2">
        <span>{t}Выберите интересующую вас категорию{/t}</span>
    </div>
    <div>
        {moduleinsert name="\Banners\Controller\Block\BannerZone" zone="zone-head-category" indexTemplate="blocks/bannerzone/zone_catalog.tpl" rotate=true}
    </div>
</div>
{foreach $dirlist as $node}
    {if $node->getChildsCount()}
        <div class="head-dropdown-catalog__subcat" id="dropdown-subcat-{$node@iteration}">
            <div class="row g-5 row-cols-xl-3 row-cols-2">
                {foreach $node->getChilds() as $sub_node}
                    {$sub_dir = $sub_node->getObject()}
                    <div>
                        <a href="{$sub_dir->getUrl()}" class="head-catalog-category" {$sub_dir->getDebugAttributes()}>{$sub_dir.name}</a>
                        {if $sub_node->getChildsCount()}
                            <ul class="head-catalog-subcategories">
                                {foreach $sub_node->getChilds() as $sub_node_2}
                                    {$sub_dir_2 = $sub_node_2->getObject()}
                                    <li><a {$sub_dir_2->getDebugAttributes()} href="{$sub_dir_2->getUrl()}">
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
    {/if}
{/foreach}