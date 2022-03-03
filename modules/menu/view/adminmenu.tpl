{addjs file="%menu%/admin_menu.js"}
<div class="side-menu-overlay"></div>
<ul class="side-menu side-main">
    {* Один уровень меню административной панели *}
    {foreach $items as $item}
        <li {if $item->getChildsCount()} class="sm-node rs-meter-group"{/if}>
            <a {if isset($sel_id) && $sel_id==$item.fields.id}class="active"{/if} {if $item->getChildsCount()}data-url="{$item.fields.link}"{else}href="{$item.fields.link}"{/if}>
                <i class="rs-icon rs-icon-{$item.fields.alias}" {if $item.fields.iconstyle}style="{$item.fields.iconstyle}"{/if}>{meter}</i>
                <span class="title">{$item.fields.title}</span>
            </a>
            {if $item->getChildsCount()}
                <div class="sm">
                    <div class="sm-head">
                        <a class="menu-close"><i class="zmdi zmdi-close"></i></a>
                        {$item.fields.title}
                    </div>
                    <div class="sm-body">
                        <ul>
                            {include file="adminmenu_branch.tpl" list=$item.child is_second_level=true}
                        </ul>
                    </div>
                </div>
            {/if}
        </li>
    {/foreach}
</ul>