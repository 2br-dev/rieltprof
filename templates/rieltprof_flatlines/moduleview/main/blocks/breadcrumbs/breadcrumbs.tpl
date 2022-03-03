{* Хлебные крошки *}
{$bc = $app->breadcrumbs->getBreadCrumbs()}
{if !empty($bc)}
    <div class="crumbs-wrapper">
        <div class="crumbs-rest-wrapper">
            <div class="crumbs-rest">
                {foreach $bc as $key => $item}

                        <a href="{$item.href}" class="crumb {if $item['title'] == 'Продажа' || $item['title'] == 'Аренда'}disabled{/if}"
                        >{$item.title}</a>
                        {if !$item@last}
                            <div class="separator">›</div>
                        {/if}

                {/foreach}
            </div>
        </div>
    </div>
{/if}
